@extends('layouts.app')
@section('title', 'Dashboard Karyawan')
@section('page-title', 'Dashboard')

@section('content')
<div class="row g-4 mb-4">
    {{-- Quota Card --}}
    <div class="col-12">
        <div class="card border-0" style="background:linear-gradient(135deg,#1e40af,#3b82f6);color:#fff;">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <p class="mb-1 opacity-75" style="font-size:.85rem;"><i class="bi bi-person-badge me-2"></i>Selamat datang,</p>
                        <h3 class="fw-700 mb-0">{{ Auth::user()->name }}</h3>
                        <p class="mb-0 mt-1 opacity-75" style="font-size:.85rem;">{{ Auth::user()->position ?? 'Karyawan' }} · ID: {{ Auth::user()->employee_id }}</p>
                    </div>
                    <div class="col-md-6 mt-3 mt-md-0">
                        <div class="row g-3">
                            <div class="col-6">
                                <div style="background:rgba(255,255,255,.15);border-radius:10px;padding:.9rem;text-align:center;">
                                    <div style="font-size:1.75rem;font-weight:700;">{{ $stats['annual_remaining'] }}</div>
                                    <div style="font-size:.75rem;opacity:.85;">Sisa Cuti Tahunan</div>
                                    <div style="font-size:.7rem;opacity:.7;">dari {{ $stats['annual_quota'] }} hari</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div style="background:rgba(255,255,255,.15);border-radius:10px;padding:.9rem;text-align:center;">
                                    <div style="font-size:1.75rem;font-weight:700;">{{ $stats['sick_used'] }}</div>
                                    <div style="font-size:.75rem;opacity:.85;">Hari Cuti Sakit</div>
                                    <div style="font-size:.7rem;opacity:.7;">tahun {{ now()->year }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Stats row --}}
<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="card text-center p-3">
            <div style="font-size:2rem;font-weight:700;color:#f59e0b;">{{ $stats['pending'] }}</div>
            <div style="font-size:.8rem;color:#64748b;">Menunggu Persetujuan</div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card text-center p-3">
            <div style="font-size:2rem;font-weight:700;color:#10b981;">{{ $stats['approved'] }}</div>
            <div style="font-size:.8rem;color:#64748b;">Disetujui</div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card text-center p-3">
            <div style="font-size:2rem;font-weight:700;color:#ef4444;">{{ $stats['rejected'] }}</div>
            <div style="font-size:.8rem;color:#64748b;">Ditolak</div>
        </div>
    </div>
</div>

{{-- Annual leave progress --}}
<div class="card mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span style="font-size:.85rem;font-weight:600;">Pemakaian Cuti Tahunan {{ now()->year }}</span>
            <span style="font-size:.85rem;color:#64748b;">{{ $stats['annual_used'] }} / {{ $stats['annual_quota'] }} hari</span>
        </div>
        @php $pct = $stats['annual_quota'] > 0 ? round(($stats['annual_used']/$stats['annual_quota'])*100) : 0; @endphp
        <div class="progress">
            <div class="progress-bar {{ $pct >= 80 ? 'bg-danger' : ($pct >= 50 ? 'bg-warning' : 'bg-success') }}"
                 style="width:{{ $pct }}%"></div>
        </div>
        <div class="mt-1" style="font-size:.75rem;color:#64748b;">{{ $pct }}% terpakai · Sisa {{ $stats['annual_remaining'] }} hari</div>
    </div>
</div>

{{-- Quick action + recent --}}
<div class="row g-4">
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header"><h6><i class="bi bi-lightning-charge me-2 text-warning"></i>Aksi Cepat</h6></div>
            <div class="card-body d-flex flex-column gap-2">
                <a href="{{ route('employee.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Ajukan Cuti Baru
                </a>
                <a href="{{ route('employee.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-list-ul me-2"></i>Lihat Semua Pengajuan
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6><i class="bi bi-clock-history me-2"></i>Pengajuan Terbaru</h6>
                <a href="{{ route('employee.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                @if($recent->isEmpty())
                    <div class="text-center py-4 text-muted"><i class="bi bi-inbox fs-2 d-block mb-2"></i>Belum ada pengajuan</div>
                @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light"><tr>
                            <th>Jenis</th><th>Tanggal</th><th>Hari</th><th>Status</th>
                        </tr></thead>
                        <tbody>
                        @foreach($recent as $app)
                        <tr>
                            <td>
                                <span class="badge {{ $app->leave_type === 'annual' ? 'bg-primary' : 'bg-info' }}">
                                    {{ $app->leave_type_label }}
                                </span>
                            </td>
                            <td style="font-size:.82rem;">
                                {{ $app->start_date->format('d/m/Y') }} – {{ $app->end_date->format('d/m/Y') }}
                            </td>
                            <td><strong>{{ $app->total_days }}</strong></td>
                            <td>
                                <span class="badge bg-{{ $app->status_color }}">{{ $app->status_label }}</span>
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
</div>
@endsection
