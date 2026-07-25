@extends('layouts.app')
@section('title', 'Semua Pengajuan Cuti')
@section('page-title', 'Semua Pengajuan Cuti')

@section('content')
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-sm-3">
                <label class="form-label mb-1" style="font-size:.8rem;">Filter</label>
                <select name="filter" class="form-select form-select-sm">
                    <option value="">Semua Pengajuan</option>
                    <option value="awaiting" {{ request('filter')==='awaiting' ? 'selected' : '' }}>Menunggu Saya</option>
                </select>
            </div>
            <div class="col-sm-3">
                <label class="form-label mb-1" style="font-size:.8rem;">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status')==='pending' ? 'selected' : '' }}>Pending</option>
                    <option value="manager_approved" {{ request('status')==='manager_approved' ? 'selected' : '' }}>Disetujui Manager</option>
                    <option value="manager_rejected" {{ request('status')==='manager_rejected' ? 'selected' : '' }}>Ditolak Manager</option>
                    <option value="hrd_approved" {{ request('status')==='hrd_approved' ? 'selected' : '' }}>Disetujui HRD</option>
                    <option value="hrd_rejected" {{ request('status')==='hrd_rejected' ? 'selected' : '' }}>Ditolak HRD</option>
                </select>
            </div>
            <div class="col-sm-2">
                <label class="form-label mb-1" style="font-size:.8rem;">Jenis</label>
                <select name="leave_type" class="form-select form-select-sm">
                    <option value="">Semua</option>
                    <option value="annual" {{ request('leave_type')==='annual' ? 'selected' : '' }}>Tahunan</option>
                    <option value="sick" {{ request('leave_type')==='sick' ? 'selected' : '' }}>Sakit</option>
                </select>
            </div>
            <div class="col-sm-2">
                <label class="form-label mb-1" style="font-size:.8rem;">Role</label>
                <select name="role" class="form-select form-select-sm">
                    <option value="">Semua Role</option>
                    <option value="employee" {{ request('role')==='employee' ? 'selected' : '' }}>Employee</option>
                    <option value="manager" {{ request('role')==='manager' ? 'selected' : '' }}>Manager</option>
                </select>
            </div>
            <div class="col-sm-2">
                <button type="submit" class="btn btn-primary btn-sm w-100">
                    <i class="bi bi-funnel me-1"></i>Filter
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h6 class="mb-0"><i class="bi bi-journal-check me-2"></i>Daftar Pengajuan</h6>
    </div>
    <div class="card-body p-0">
        @if($applications->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bi bi-inbox fs-1 d-block mb-3"></i>Tidak ada pengajuan ditemukan.
            </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No</th><th>Nama</th><th>Role</th><th>Jenis</th>
                        <th>Tanggal</th><th>Hari</th><th>Status</th>
                        <th>Persetujuan Manager</th><th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($applications as $i => $app)
                <tr>
                    <td class="text-muted" style="font-size:.8rem;">{{ $applications->firstItem()+$i }}</td>
                    <td>
                        <div style="font-size:.875rem;font-weight:600;">{{ $app->user->name }}</div>
                        <div style="font-size:.75rem;color:#64748b;">{{ $app->user->employee_id }}</div>
                    </td>
                    <td>
                        <span class="badge {{ $app->user->role==='manager' ? 'bg-warning text-dark' : 'bg-secondary' }}">
                            {{ ucfirst($app->user->role) }}
                        </span>
                    </td>
                    <td><span class="badge {{ $app->leave_type==='annual' ? 'bg-primary' : 'bg-info text-dark' }}">{{ $app->leave_type_label }}</span></td>
                    <td style="font-size:.82rem;">
                        {{ $app->start_date->format('d M Y') }}<br>
                        <span class="text-muted">s/d {{ $app->end_date->format('d M Y') }}</span>
                    </td>
                    <td><strong>{{ $app->total_days }}</strong></td>
                    <td><span class="badge bg-{{ $app->status_color }}">{{ $app->status_label }}</span></td>
                    <td style="font-size:.8rem;">
                        @if($app->user->role === 'manager')
                            <span class="badge bg-light text-muted border">Langsung ke HRD</span>
                        @elseif($app->managerApprover)
                            <span class="text-success"><i class="bi bi-check me-1"></i>{{ $app->managerApprover->name }}</span>
                        @elseif($app->isManagerRejected())
                            <span class="text-danger"><i class="bi bi-x me-1"></i>Ditolak</span>
                        @else
                            <span class="text-warning">Menunggu</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('hrd.show-leave', $app) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye me-1"></i>Detail
                        </a>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $applications->links() }}</div>
        @endif
    </div>
</div>
@endsection
