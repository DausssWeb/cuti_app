@extends('layouts.app')
@section('title', 'Dashboard HRD')
@section('page-title', 'Dashboard HRD')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#f59e0b,#fbbf24);">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-value">{{ $stats['pending_employee'] }}</div>
                    <div class="stat-label">Pending Karyawan</div>
                </div>
                <i class="bi bi-people stat-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#06b6d4,#22d3ee);">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-value">{{ $stats['waiting_hrd'] }}</div>
                    <div class="stat-label">Menunggu HRD</div>
                </div>
                <i class="bi bi-hourglass-split stat-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#8b5cf6,#a78bfa);">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-value">{{ $stats['pending_manager'] }}</div>
                    <div class="stat-label">Pending Manager</div>
                </div>
                <i class="bi bi-person-workspace stat-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#10b981,#34d399);">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-value">{{ $stats['total_approved_year'] }}</div>
                    <div class="stat-label">Disetujui Tahun Ini</div>
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
                <h6 class="mb-0"><i class="bi bi-hourglass-split me-2 text-warning"></i>Menunggu Persetujuan HRD</h6>
                <a href="{{ route('hrd.all-leaves', ['filter'=>'awaiting']) }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                @if($awaitingHrd->isEmpty())
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-check-circle fs-2 d-block mb-2 text-success"></i>
                        Tidak ada pengajuan yang menunggu persetujuan Anda.
                    </div>
                @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr><th>Nama</th><th>Role</th><th>Jenis</th><th>Tanggal</th><th>Hari</th><th>Aksi</th></tr>
                        </thead>
                        <tbody>
                        @foreach($awaitingHrd as $app)
                        <tr>
                            <td>
                                <div style="font-size:.875rem;font-weight:600;">{{ $app->user->name }}</div>
                                <div style="font-size:.75rem;color:#64748b;">{{ $app->user->employee_id }}</div>
                            </td>
                            <td>
                                <span class="badge {{ $app->user->role === 'manager' ? 'bg-warning text-dark' : 'bg-secondary' }}">
                                    {{ ucfirst($app->user->role) }}
                                </span>
                            </td>
                            <td><span class="badge {{ $app->leave_type==='annual' ? 'bg-primary' : 'bg-info text-dark' }}">{{ $app->leave_type_label }}</span></td>
                            <td style="font-size:.82rem;">{{ $app->start_date->format('d/m') }} – {{ $app->end_date->format('d/m/Y') }}</td>
                            <td><strong>{{ $app->total_days }}</strong></td>
                            <td>
                                <a href="{{ route('hrd.show-leave', $app) }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-eye me-1"></i>Review
                                </a>
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-header"><h6 class="mb-0"><i class="bi bi-lightning-charge me-2 text-warning"></i>Aksi Cepat</h6></div>
            <div class="card-body d-flex flex-column gap-2">
                <a href="{{ route('hrd.all-leaves', ['filter'=>'awaiting']) }}" class="btn btn-warning text-dark">
                    <i class="bi bi-hourglass-split me-2"></i>Proses Antrian HRD
                </a>
                <a href="{{ route('hrd.all-leaves') }}" class="btn btn-outline-primary">
                    <i class="bi bi-journal-check me-2"></i>Semua Pengajuan
                </a>
                <a href="{{ route('hrd.report') }}" class="btn btn-outline-danger">
                    <i class="bi bi-file-earmark-pdf me-2"></i>Cetak Laporan PDF
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h6 class="mb-0"><i class="bi bi-bar-chart me-2"></i>Ringkasan {{ now()->year }}</h6></div>
            <div class="card-body">
                @php
                    $approvedTotal = \App\Models\LeaveApplication::where('status','hrd_approved')->where('year',now()->year)->count();
                    $rejectedTotal = \App\Models\LeaveApplication::whereIn('status',['manager_rejected','hrd_rejected'])->where('year',now()->year)->count();
                    $pendingTotal  = \App\Models\LeaveApplication::whereIn('status',['pending','manager_approved'])->where('year',now()->year)->count();
                @endphp
                <div class="d-flex justify-content-between py-2 border-bottom" style="font-size:.85rem;">
                    <span><i class="bi bi-circle-fill text-success me-2" style="font-size:.6rem;"></i>Disetujui</span>
                    <strong>{{ $approvedTotal }}</strong>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom" style="font-size:.85rem;">
                    <span><i class="bi bi-circle-fill text-warning me-2" style="font-size:.6rem;"></i>Dalam Proses</span>
                    <strong>{{ $pendingTotal }}</strong>
                </div>
                <div class="d-flex justify-content-between py-2" style="font-size:.85rem;">
                    <span><i class="bi bi-circle-fill text-danger me-2" style="font-size:.6rem;"></i>Ditolak</span>
                    <strong>{{ $rejectedTotal }}</strong>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
