@extends('layouts.app')
@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard Administrator')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#2563eb,#60a5fa);">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-value">{{ $stats['total_users'] }}</div>
                    <div class="stat-label">Total Pengguna</div>
                </div>
                <i class="bi bi-people stat-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#10b981,#34d399);">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-value">{{ $stats['employees'] }}</div>
                    <div class="stat-label">Total Karyawan</div>
                </div>
                <i class="bi bi-person-badge stat-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#f59e0b,#fbbf24);">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-value">{{ $stats['pending_leaves'] }}</div>
                    <div class="stat-label">Cuti Pending</div>
                </div>
                <i class="bi bi-hourglass-split stat-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#8b5cf6,#a78bfa);">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-value">{{ $stats['approved_year'] }}</div>
                    <div class="stat-label">Disetujui {{ now()->year }}</div>
                </div>
                <i class="bi bi-check-circle stat-icon"></i>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="bi bi-people me-2"></i>Pengguna Terbaru</h6>
                <a href="{{ route('admin.users') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr><th>Nama</th><th>Email</th><th>Role</th><th>Jabatan</th><th>Status</th><th>Aksi</th></tr>
                        </thead>
                        <tbody>
                        @foreach($recentUsers as $u)
                        <tr>
                            <td>
                                <div style="font-size:.875rem;font-weight:600;">{{ $u->name }}</div>
                                <div style="font-size:.75rem;color:#64748b;">{{ $u->employee_id }}</div>
                            </td>
                            <td style="font-size:.82rem;">{{ $u->email }}</td>
                            <td>
                                @php $rc = ['employee'=>'secondary','manager'=>'warning','hrd'=>'info']; @endphp
                                <span class="badge bg-{{ $rc[$u->role] ?? 'secondary' }} {{ $u->role==='manager' ? 'text-dark' : '' }}">{{ ucfirst($u->role) }}</span>
                            </td>
                            <td style="font-size:.82rem;">{{ $u->position ?? '-' }}</td>
                            <td>
                                <span class="badge {{ $u->is_active ? 'bg-success' : 'bg-danger' }}">
                                    {{ $u->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.edit-user', $u) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-header"><h6 class="mb-0"><i class="bi bi-lightning-charge me-2 text-warning"></i>Aksi Cepat</h6></div>
            <div class="card-body d-flex flex-column gap-2">
                <a href="{{ route('admin.create-user') }}" class="btn btn-primary">
                    <i class="bi bi-person-plus me-2"></i>Tambah Pengguna
                </a>
                <a href="{{ route('admin.users') }}" class="btn btn-outline-primary">
                    <i class="bi bi-people me-2"></i>Kelola Pengguna
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h6 class="mb-0"><i class="bi bi-bar-chart me-2"></i>Distribusi Role</h6></div>
            <div class="card-body">
                <div class="d-flex justify-content-between py-2 border-bottom" style="font-size:.85rem;">
                    <span><span class="badge bg-secondary me-2">Employee</span></span>
                    <strong>{{ $stats['employees'] }} orang</strong>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom" style="font-size:.85rem;">
                    <span><span class="badge bg-warning text-dark me-2">Manager</span></span>
                    <strong>{{ $stats['managers'] }} orang</strong>
                </div>
                <div class="d-flex justify-content-between py-2" style="font-size:.85rem;">
                    <span><span class="badge bg-info me-2">HRD</span></span>
                    <strong>{{ $stats['hrd'] }} orang</strong>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
