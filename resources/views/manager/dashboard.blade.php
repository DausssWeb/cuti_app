@extends('layouts.app')
@section('title', 'Dashboard Manager')
@section('page-title', 'Dashboard Manager')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#f59e0b,#fbbf24);">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-value">{{ $stats['pending_employee'] }}</div>
                    <div class="stat-label">Menunggu Persetujuan Saya</div>
                </div>
                <i class="bi bi-hourglass-split stat-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#10b981,#34d399);">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-value">{{ $stats['approved_today'] }}</div>
                    <div class="stat-label">Disetujui Hari Ini</div>
                </div>
                <i class="bi bi-check-circle stat-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#2563eb,#60a5fa);">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-value">{{ $stats['my_annual_remaining'] }}</div>
                    <div class="stat-label">Sisa Cuti Tahunan Saya</div>
                </div>
                <i class="bi bi-calendar2-check stat-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#8b5cf6,#a78bfa);">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-value">{{ $stats['my_sick_used'] }}</div>
                    <div class="stat-label">Hari Cuti Sakit Saya</div>
                </div>
                <i class="bi bi-heart-pulse stat-icon"></i>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- Pending applications --}}
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="bi bi-clock-history me-2 text-warning"></i>Pengajuan Karyawan Menunggu Persetujuan</h6>
                <a href="{{ route('manager.employee-leaves') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                @if($pendingApplications->isEmpty())
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-check-circle fs-2 d-block mb-2 text-success"></i>
                        Tidak ada pengajuan yang menunggu persetujuan.
                    </div>
                @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light"><tr>
                            <th>Karyawan</th><th>Jenis</th><th>Tanggal</th><th>Hari</th><th>Aksi</th>
                        </tr></thead>
                        <tbody>
                        @foreach($pendingApplications as $app)
                        <tr>
                            <td>
                                <div style="font-size:.875rem;font-weight:600;">{{ $app->user->name }}</div>
                                <div style="font-size:.75rem;color:#64748b;">{{ $app->user->position }}</div>
                            </td>
                            <td><span class="badge {{ $app->leave_type === 'annual' ? 'bg-primary' : 'bg-info text-dark' }}">{{ $app->leave_type_label }}</span></td>
                            <td style="font-size:.82rem;">{{ $app->start_date->format('d/m') }} – {{ $app->end_date->format('d/m/Y') }}</td>
                            <td><strong>{{ $app->total_days }}</strong></td>
                            <td>
                                <a href="{{ route('manager.show-employee-leave', $app) }}" class="btn btn-sm btn-primary">
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

    {{-- Quick actions --}}
    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-header"><h6 class="mb-0"><i class="bi bi-lightning-charge me-2 text-warning"></i>Aksi Cepat</h6></div>
            <div class="card-body d-flex flex-column gap-2">
                <a href="{{ route('manager.employee-leaves') }}" class="btn btn-outline-primary">
                    <i class="bi bi-person-check me-2"></i>Kelola Cuti Karyawan
                </a>
                <a href="{{ route('manager.create-leave') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Ajukan Cuti Saya
                </a>
                <a href="{{ route('manager.my-leaves') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-list-ul me-2"></i>Riwayat Cuti Saya
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h6 class="mb-0"><i class="bi bi-person-circle me-2"></i>Kuota Cuti Saya</h6></div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span style="font-size:.82rem;">Cuti Tahunan {{ now()->year }}</span>
                        <span style="font-size:.82rem;">{{ $quota->annual_used }}/{{ $quota->annual_quota }} hari</span>
                    </div>
                    @php $pct = $quota->annual_quota > 0 ? round(($quota->annual_used/$quota->annual_quota)*100) : 0; @endphp
                    <div class="progress">
                        <div class="progress-bar {{ $pct >= 80 ? 'bg-danger' : 'bg-primary' }}" style="width:{{ $pct }}%"></div>
                    </div>
                    <div class="mt-1" style="font-size:.75rem;color:#64748b;">Sisa {{ $quota->remaining }} hari</div>
                </div>
                <div class="d-flex justify-content-between" style="font-size:.85rem;">
                    <span>Cuti Sakit Terpakai</span>
                    <strong>{{ $quota->sick_used }} hari</strong>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
