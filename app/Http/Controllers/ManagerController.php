<?php

namespace App\Http\Controllers;

use App\Models\LeaveApplication;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ManagerController extends Controller
{
    // ── Dashboard ─────────────────────────────────────────────────
    public function dashboard()
    {
        $user  = Auth::user();
        $year  = now()->year;
        $quota = $user->getOrCreateQuota($year);

        // Employee applications needing manager approval
        $pendingCount = LeaveApplication::forEmployee()->pending()->count();

        $stats = [
            'pending_employee'  => $pendingCount,
            'approved_today'    => LeaveApplication::forEmployee()
                                    ->where('status', 'manager_approved')
                                    ->whereDate('manager_approved_at', today())->count(),
            'my_annual_remaining' => $quota->remaining,
            'my_sick_used'      => $quota->sick_used,
        ];

        $pendingApplications = LeaveApplication::forEmployee()
            ->with('user')
            ->pending()
            ->orderBy('created_at')
            ->take(5)
            ->get();

        return view('manager.dashboard', compact('user', 'stats', 'pendingApplications', 'quota'));
    }

    // ── Employee leave approval list ──────────────────────────────
    public function employeeLeaves(Request $request)
    {
        $query = LeaveApplication::forEmployee()->with('user');

        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->leave_type) {
            $query->where('leave_type', $request->leave_type);
        }

        $applications = $query->orderByDesc('created_at')->paginate(10);

        return view('manager.employee-leaves', compact('applications'));
    }

    public function showEmployeeLeave(LeaveApplication $leaveApplication)
    {
        // Only show employee applications
        if ($leaveApplication->user->role !== 'employee') {
            abort(403, 'Anda hanya dapat melihat pengajuan cuti karyawan.');
        }
        return view('manager.show-employee-leave', compact('leaveApplication'));
    }

    public function approveLeave(Request $request, LeaveApplication $leaveApplication)
    {
        if ($leaveApplication->user->role !== 'employee') {
            abort(403);
        }
        if (!$leaveApplication->isPending()) {
            return back()->with('error', 'Pengajuan ini sudah diproses sebelumnya.');
        }

        $request->validate([
            'action' => 'required|in:approve,reject',
            'notes'  => 'nullable|string|max:300',
        ], [
            'action.required' => 'Aksi wajib dipilih.',
            'action.in'       => 'Aksi tidak valid.',
        ]);

        $status = $request->action === 'approve' ? 'manager_approved' : 'manager_rejected';

        if ($request->action === 'reject' && empty(trim($request->notes ?? ''))) {
            return back()->withErrors(['notes' => 'Catatan penolakan wajib diisi.']);
        }

        $leaveApplication->update([
            'status'              => $status,
            'manager_approved_by' => Auth::id(),
            'manager_approved_at' => now(),
            'manager_notes'       => $request->notes,
        ]);

        $msg = $request->action === 'approve'
            ? 'Pengajuan cuti berhasil disetujui dan diteruskan ke HRD.'
            : 'Pengajuan cuti berhasil ditolak.';

        return redirect()->route('manager.employee-leaves')->with('success', $msg);
    }

    // ── Manager's own leave application ──────────────────────────
    public function myLeaves()
    {
        $user         = Auth::user();
        $applications = $user->leaveApplications()
                             ->with('hrdApprover')
                             ->orderByDesc('created_at')
                             ->paginate(10);
        $quota = $user->getOrCreateQuota(now()->year);
        return view('manager.my-leaves', compact('applications', 'quota'));
    }

    public function createLeave()
    {
        $user  = Auth::user();
        $quota = $user->getOrCreateQuota(now()->year);
        return view('manager.create-leave', compact('quota'));
    }

    public function storeLeave(Request $request)
    {
        $user  = Auth::user();
        $year  = now()->year;
        $quota = $user->getOrCreateQuota($year);

        $validated = $request->validate([
            'leave_type' => 'required|in:annual,sick',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'reason'     => 'required|string|min:10|max:500',
            'sick_note'  => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ], [
            'leave_type.required'       => 'Jenis cuti wajib dipilih.',
            'leave_type.in'             => 'Jenis cuti tidak valid.',
            'start_date.required'       => 'Tanggal mulai wajib diisi.',
            'start_date.after_or_equal' => 'Tanggal mulai tidak boleh sebelum hari ini.',
            'end_date.required'         => 'Tanggal selesai wajib diisi.',
            'end_date.after_or_equal'   => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
            'reason.required'           => 'Alasan cuti wajib diisi.',
            'reason.min'                => 'Alasan cuti minimal 10 karakter.',
        ]);

        $start     = Carbon::parse($validated['start_date']);
        $end       = Carbon::parse($validated['end_date']);
        $totalDays = LeaveApplication::calculateWorkingDays($start, $end);

        if ($totalDays === 0) {
            return back()->withErrors(['end_date' => 'Tidak ada hari kerja dalam rentang tanggal tersebut.'])->withInput();
        }

        if ($validated['leave_type'] === 'annual' && $quota->remaining < $totalDays) {
            return back()->withErrors([
                'leave_type' => "Sisa kuota cuti tahunan Anda hanya {$quota->remaining} hari."
            ])->withInput();
        }

        $overlap = $user->leaveApplications()
            ->whereNotIn('status', ['manager_rejected', 'hrd_rejected'])
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('start_date', [$start, $end])
                  ->orWhereBetween('end_date', [$start, $end])
                  ->orWhere(fn($q2) => $q2->where('start_date', '<=', $start)->where('end_date', '>=', $end));
            })->exists();

        if ($overlap) {
            return back()->withErrors(['start_date' => 'Terdapat pengajuan cuti yang tumpang tindih.'])->withInput();
        }

        $sickNotePath = null;
        if ($request->hasFile('sick_note')) {
            $sickNotePath = $request->file('sick_note')->store('sick-notes', 'public');
        }

        // Manager leave: status = pending, goes directly to HRD
        LeaveApplication::create([
            'user_id'    => $user->id,
            'leave_type' => $validated['leave_type'],
            'start_date' => $start,
            'end_date'   => $end,
            'total_days' => $totalDays,
            'reason'     => $validated['reason'],
            'sick_note'  => $sickNotePath,
            'status'     => 'pending',
            'year'       => $year,
        ]);

        return redirect()->route('manager.my-leaves')
                         ->with('success', 'Pengajuan cuti berhasil dikirim ke HRD.');
    }
}
