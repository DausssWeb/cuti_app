<?php

namespace App\Http\Controllers;

use App\Models\LeaveApplication;
use App\Models\LeaveQuota;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class EmployeeController extends Controller
{
    public function dashboard()
    {
        $user  = Auth::user();
        $year  = now()->year;
        $quota = $user->getOrCreateQuota($year);

        $stats = [
            'annual_quota'     => $quota->annual_quota,
            'annual_used'      => $quota->annual_used,
            'annual_remaining' => $quota->remaining,
            'sick_used'        => $quota->sick_used,
            'pending'          => $user->leaveApplications()->pending()->whereYear('created_at', $year)->count(),
            'approved'         => $user->leaveApplications()->where('status', 'hrd_approved')->whereYear('created_at', $year)->count(),
            'rejected'         => $user->leaveApplications()->whereIn('status', ['manager_rejected','hrd_rejected'])->whereYear('created_at', $year)->count(),
        ];

        $recent = $user->leaveApplications()->with('managerApprover','hrdApprover')
                       ->orderByDesc('created_at')->take(5)->get();

        return view('employee.dashboard', compact('user', 'stats', 'recent', 'quota'));
    }

    public function index()
    {
        $user         = Auth::user();
        $applications = $user->leaveApplications()
                             ->with('managerApprover', 'hrdApprover')
                             ->orderByDesc('created_at')
                             ->paginate(10);

        $quota = $user->getOrCreateQuota(now()->year);
        return view('employee.index', compact('applications', 'quota'));
    }

    public function create()
    {
        $user  = Auth::user();
        $quota = $user->getOrCreateQuota(now()->year);
        return view('employee.create', compact('quota'));
    }

    public function store(Request $request)
    {
        $user  = Auth::user();
        $year  = now()->year;
        $quota = $user->getOrCreateQuota($year);

        $rules = [
            'leave_type' => 'required|in:annual,sick',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'reason'     => 'required|string|min:10|max:500',
        ];

        $messages = [
            'leave_type.required'       => 'Jenis cuti wajib dipilih.',
            'leave_type.in'             => 'Jenis cuti tidak valid. Pilih Cuti Tahunan atau Cuti Sakit.',
            'start_date.required'       => 'Tanggal mulai wajib diisi.',
            'start_date.after_or_equal' => 'Tanggal mulai tidak boleh sebelum hari ini.',
            'end_date.required'         => 'Tanggal selesai wajib diisi.',
            'end_date.after_or_equal'   => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
            'reason.required'           => 'Alasan cuti wajib diisi.',
            'reason.min'                => 'Alasan cuti minimal 10 karakter.',
            'reason.max'                => 'Alasan cuti maksimal 500 karakter.',
        ];

        if ($request->leave_type === 'sick') {
            $rules['sick_note'] = 'required|file|mimes:pdf,jpg,jpeg,png|max:2048';
            $messages['sick_note.required'] = 'Surat keterangan dokter wajib dilampirkan untuk cuti sakit.';
            $messages['sick_note.mimes'] = 'Surat dokter harus berformat PDF, JPG, atau PNG.';
            $messages['sick_note.max']   = 'Ukuran surat dokter maksimal 2MB.';
        }

        $validated = $request->validate($rules, $messages);

        $start = Carbon::parse($validated['start_date']);
        $end   = Carbon::parse($validated['end_date']);

        // Check weekends
        if ($start->isWeekend()) {
            return back()->withErrors(['start_date' => 'Tanggal mulai tidak boleh hari Sabtu atau Minggu.'])->withInput();
        }

        $totalDays = LeaveApplication::calculateWorkingDays($start, $end);

        if ($totalDays === 0) {
            return back()->withErrors(['end_date' => 'Tidak ada hari kerja dalam rentang tanggal tersebut.'])->withInput();
        }

        // Validate annual quota
        if ($validated['leave_type'] === 'annual') {
            if ($quota->remaining < $totalDays) {
                return back()->withErrors([
                    'leave_type' => "Sisa kuota cuti tahunan Anda hanya {$quota->remaining} hari, sedangkan pengajuan ini membutuhkan {$totalDays} hari."
                ])->withInput();
            }
        }

        // Check overlapping leave
        $overlap = $user->leaveApplications()
            ->whereNotIn('status', ['manager_rejected', 'hrd_rejected'])
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('start_date', [$start, $end])
                  ->orWhereBetween('end_date', [$start, $end])
                  ->orWhere(function ($q2) use ($start, $end) {
                      $q2->where('start_date', '<=', $start)->where('end_date', '>=', $end);
                  });
            })->exists();

        if ($overlap) {
            return back()->withErrors(['start_date' => 'Terdapat pengajuan cuti yang tumpang tindih dengan tanggal yang dipilih.'])->withInput();
        }

        // Handle sick note upload
        $sickNotePath = null;
        if ($request->hasFile('sick_note')) {
            $sickNotePath = $request->file('sick_note')->store('sick-notes', 'public');
        }

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

        return redirect()->route('employee.index')
                         ->with('success', 'Pengajuan cuti berhasil dikirim dan sedang menunggu persetujuan.');
    }

    public function show(LeaveApplication $leaveApplication)
    {
        if ($leaveApplication->user_id !== Auth::id()) {
            abort(403);
        }
        return view('employee.show', compact('leaveApplication'));
    }
}
