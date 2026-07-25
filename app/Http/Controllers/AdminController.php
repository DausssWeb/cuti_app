<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\LeaveApplication;
use App\Models\LeaveQuota;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_users'    => User::where('role', '!=', 'admin')->count(),
            'employees'      => User::where('role', 'employee')->count(),
            'managers'       => User::where('role', 'manager')->count(),
            'hrd'            => User::where('role', 'hrd')->count(),
            'pending_leaves' => LeaveApplication::pending()->count(),
            'approved_year'  => LeaveApplication::where('status','hrd_approved')->where('year', now()->year)->count(),
        ];

        $recentUsers = User::where('role','!=','admin')->orderByDesc('created_at')->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recentUsers'));
    }

    // ── User Management ───────────────────────────────────────────
    public function users(Request $request)
    {
        $query = User::where('role', '!=', 'admin');
        if ($request->role) $query->where('role', $request->role);
        if ($request->search) {
            $query->where(fn($q) => $q->where('name','like',"%{$request->search}%")
                                      ->orWhere('email','like',"%{$request->search}%")
                                      ->orWhere('employee_id','like',"%{$request->search}%"));
        }
        $users = $query->orderBy('name')->paginate(15);
        return view('admin.users', compact('users'));
    }

    public function createUser()
    {
        return view('admin.create-user');
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:100',
            'email'       => 'required|email|unique:users,email',
            'employee_id' => 'required|string|max:20|unique:users,employee_id',
            'role'        => 'required|in:employee,manager,hrd',
            'password'    => 'required|string|min:8|confirmed',
            'phone'       => 'nullable|string|max:20',
            'position'    => 'nullable|string|max:100',
            'join_date'   => 'nullable|date',
        ], [
            'name.required'            => 'Nama wajib diisi.',
            'email.required'           => 'Email wajib diisi.',
            'email.email'              => 'Format email tidak valid.',
            'email.unique'             => 'Email sudah digunakan.',
            'employee_id.required'     => 'ID Karyawan wajib diisi.',
            'employee_id.unique'       => 'ID Karyawan sudah digunakan.',
            'role.required'            => 'Role wajib dipilih.',
            'role.in'                  => 'Role tidak valid.',
            'password.required'        => 'Password wajib diisi.',
            'password.min'             => 'Password minimal 8 karakter.',
            'password.confirmed'       => 'Konfirmasi password tidak cocok.',
        ]);

        $user = User::create([
            'name'        => $validated['name'],
            'email'       => $validated['email'],
            'employee_id' => $validated['employee_id'],
            'role'        => $validated['role'],
            'password'    => Hash::make($validated['password']),
            'phone'       => $request->phone,
            'position'    => $request->position,
            'join_date'   => $request->join_date,
            'is_active'   => true,
        ]);

        // Create initial quota
        $user->getOrCreateQuota(now()->year);

        return redirect()->route('admin.users')->with('success', "Pengguna {$user->name} berhasil ditambahkan.");
    }

    public function editUser(User $user)
    {
        if ($user->isAdmin()) abort(403);
        $quota = $user->getOrCreateQuota(now()->year);
        return view('admin.edit-user', compact('user', 'quota'));
    }

    public function updateUser(Request $request, User $user)
    {
        if ($user->isAdmin()) abort(403);

        $validated = $request->validate([
            'name'         => 'required|string|max:100',
            'email'        => ['required','email', Rule::unique('users','email')->ignore($user->id)],
            'employee_id'  => ['required','string','max:20', Rule::unique('users','employee_id')->ignore($user->id)],
            'role'         => 'required|in:employee,manager,hrd',
            'phone'        => 'nullable|string|max:20',
            'position'     => 'nullable|string|max:100',
            'join_date'    => 'nullable|date',
            'is_active'    => 'boolean',
            'annual_quota' => 'required|integer|min:0|max:30',
            'password'     => 'nullable|string|min:8|confirmed',
        ], [
            'name.required'        => 'Nama wajib diisi.',
            'email.unique'         => 'Email sudah digunakan.',
            'employee_id.unique'   => 'ID Karyawan sudah digunakan.',
            'annual_quota.min'     => 'Kuota tidak boleh negatif.',
            'annual_quota.max'     => 'Kuota maksimal 30 hari.',
            'password.min'         => 'Password minimal 8 karakter.',
            'password.confirmed'   => 'Konfirmasi password tidak cocok.',
        ]);

        $data = collect($validated)->except(['password','annual_quota'])->toArray();
        $data['is_active'] = $request->boolean('is_active');

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        // Update quota
        $quota = $user->getOrCreateQuota(now()->year);
        $quota->update(['annual_quota' => $validated['annual_quota']]);

        return redirect()->route('admin.users')->with('success', "Data pengguna {$user->name} berhasil diperbarui.");
    }

    public function toggleUser(User $user)
    {
        if ($user->isAdmin()) abort(403);
        $user->update(['is_active' => !$user->is_active]);
        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Akun {$user->name} berhasil {$status}.");
    }
}
