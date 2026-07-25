<?php

namespace App\Http\Controllers;

use App\Models\LeaveApplication;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class HrdController extends Controller
{
    public function dashboard()
    {
        $year = now()->year;

        $stats = [
            // Employee leaves waiting for manager approval (pending)
            'pending_employee'    => LeaveApplication::forEmployee()->pending()->count(),
            // Employee leaves approved by manager, waiting HRD
            'waiting_hrd'         => LeaveApplication::forEmployee()->where('status','manager_approved')->count(),
            // Manager leaves waiting HRD
            'pending_manager'     => LeaveApplication::forManager()->pending()->count(),
            // All finally approved this year
            'total_approved_year' => LeaveApplication::where('status','hrd_approved')->where('year',$year)->count(),
        ];

        $awaitingHrd = LeaveApplication::with('user')
            ->where(function($q){
                // Employee: after manager approved
                $q->where(fn($q2) => $q2->forEmployee()->where('status','manager_approved'));
                // Manager: pending (goes directly to HRD)
                $q->orWhere(fn($q2) => $q2->forManager()->where('status','pending'));
            })
            ->orderBy('created_at')
            ->take(5)
            ->get();

        return view('hrd.dashboard', compact('stats', 'awaitingHrd'));
    }

    // ── All leaves for HRD to approve ────────────────────────────
    public function allLeaves(Request $request)
    {
        $query = LeaveApplication::with('user', 'managerApprover', 'hrdApprover');

        // Filter by approval queue or status
        if ($request->filter === 'awaiting') {
            $query->where(function($q){
                $q->where(fn($q2) => $q2->forEmployee()->where('status','manager_approved'));
                $q->orWhere(fn($q2) => $q2->forManager()->where('status','pending'));
            });
        } elseif ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->leave_type) {
            $query->where('leave_type', $request->leave_type);
        }
        if ($request->role) {
            $query->whereHas('user', fn($u) => $u->where('role', $request->role));
        }

        $applications = $query->orderByDesc('created_at')->paginate(10);
        return view('hrd.all-leaves', compact('applications'));
    }

    public function showLeave(LeaveApplication $leaveApplication)
    {
        $leaveApplication->load('user', 'managerApprover', 'hrdApprover');
        return view('hrd.show-leave', compact('leaveApplication'));
    }

    public function approveLeave(Request $request, LeaveApplication $leaveApplication)
    {
        $user     = $leaveApplication->user;
        $isEmployee = $user->role === 'employee';
        $isManager  = $user->role === 'manager';

        // Validate proper state
        if ($isEmployee && $leaveApplication->status !== 'manager_approved') {
            return back()->with('error', 'Pengajuan karyawan harus disetujui manager terlebih dahulu.');
        }
        if ($isManager && $leaveApplication->status !== 'pending') {
            return back()->with('error', 'Pengajuan ini sudah diproses sebelumnya.');
        }

        $request->validate([
            'action' => 'required|in:approve,reject',
            'notes'  => 'nullable|string|max:300',
        ]);

        if ($request->action === 'reject' && empty(trim($request->notes ?? ''))) {
            return back()->withErrors(['notes' => 'Catatan penolakan wajib diisi ketika menolak.']);
        }

        $newStatus = $request->action === 'approve' ? 'hrd_approved' : 'hrd_rejected';

        $leaveApplication->update([
            'status'          => $newStatus,
            'hrd_approved_by' => Auth::id(),
            'hrd_approved_at' => now(),
            'hrd_notes'       => $request->notes,
        ]);

        // Update quota if approved
        if ($request->action === 'approve') {
            $quota = $user->getOrCreateQuota($leaveApplication->year);
            if ($leaveApplication->leave_type === 'annual') {
                $quota->increment('annual_used', $leaveApplication->total_days);
            } else {
                $quota->increment('sick_used', $leaveApplication->total_days);
            }
        }

        $msg = $request->action === 'approve'
            ? 'Pengajuan cuti berhasil disetujui.'
            : 'Pengajuan cuti berhasil ditolak.';

        return redirect()->route('hrd.all-leaves')->with('success', $msg);
    }

    // ── PDF Report ────────────────────────────────────────────────
    public function reportForm()
    {
        $years = range(now()->year, now()->year - 3);
        return view('hrd.report-form', compact('years'));
    }

    public function generateReport(Request $request)
    {
        $request->validate([
            'year'       => 'required|integer|min:2020|max:' . (now()->year + 1),
            'leave_type' => 'nullable|in:annual,sick',
            'status'     => 'nullable|in:hrd_approved,hrd_rejected,pending,manager_approved,manager_rejected',
        ], [
            'year.required' => 'Tahun wajib dipilih.',
            'year.min'      => 'Tahun tidak valid.',
        ]);

        $year      = $request->year;
        $query     = LeaveApplication::with('user', 'managerApprover', 'hrdApprover')
                        ->where('year', $year)
                        ->orderBy('user_id')
                        ->orderBy('start_date');

        if ($request->leave_type) $query->where('leave_type', $request->leave_type);
        if ($request->status)     $query->where('status', $request->status);

        $applications = $query->get();

        // Summary per user
        $summary = User::whereIn('role', ['employee', 'manager'])
            ->where('is_active', true)
            ->get()
            ->map(function($u) use ($year, $applications) {
                $userApps = $applications->where('user_id', $u->id);
                $quota    = $u->getOrCreateQuota($year);
                return [
                    'user'           => $u,
                    'annual_quota'   => $quota->annual_quota,
                    'annual_used'    => $quota->annual_used,
                    'annual_remain'  => $quota->remaining,
                    'sick_used'      => $quota->sick_used,
                    'total_leaves'   => $userApps->count(),
                    'approved'       => $userApps->where('status','hrd_approved')->count(),
                    'rejected'       => $userApps->whereIn('status',['manager_rejected','hrd_rejected'])->count(),
                    'pending'        => $userApps->whereIn('status',['pending','manager_approved'])->count(),
                ];
            });

        $pdf = Pdf::loadView('hrd.report-pdf', compact('applications','summary','year','request'))
                  ->setPaper('a4', 'landscape');

        $filename = "laporan-cuti-{$year}.pdf";
        return $pdf->download($filename);
    }
}
