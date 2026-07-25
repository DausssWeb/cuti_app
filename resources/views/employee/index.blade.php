@extends('layouts.app')
@section('title', 'Riwayat Pengajuan Cuti')
@section('page-title', 'Riwayat Pengajuan Cuti')

@section('content')
{{-- Quota info bar --}}
<div class="row g-3 mb-4">
    <div class="col-sm-3">
        <div class="card text-center p-3 border-primary">
            <div style="font-size:1.5rem;font-weight:700;color:#2563eb;">{{ $quota->annual_quota }}</div>
            <div style="font-size:.78rem;color:#64748b;">Total Kuota Tahunan</div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="card text-center p-3 border-warning">
            <div style="font-size:1.5rem;font-weight:700;color:#f59e0b;">{{ $quota->annual_used }}</div>
            <div style="font-size:.78rem;color:#64748b;">Cuti Tahunan Terpakai</div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="card text-center p-3 border-success">
            <div style="font-size:1.5rem;font-weight:700;color:#10b981;">{{ $quota->remaining }}</div>
            <div style="font-size:.78rem;color:#64748b;">Sisa Cuti Tahunan</div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="card text-center p-3 border-info">
            <div style="font-size:1.5rem;font-weight:700;color:#06b6d4;">{{ $quota->sick_used }}</div>
            <div style="font-size:.78rem;color:#64748b;">Hari Cuti Sakit</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0"><i class="bi bi-list-ul me-2"></i>Semua Pengajuan Cuti</h6>
        <a href="{{ route('employee.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle me-1"></i>Ajukan Baru
        </a>
    </div>
    <div class="card-body p-0">
        @if($applications->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                <p class="mb-2">Belum ada pengajuan cuti.</p>
                <a href="{{ route('employee.create') }}" class="btn btn-primary btn-sm">Ajukan Sekarang</a>
            </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Jenis Cuti</th>
                        <th>Tanggal Mulai</th>
                        <th>Tanggal Selesai</th>
                        <th>Hari</th>
                        <th>Alasan</th>
                        <th>Status</th>
                        <th>Tanggal Diajukan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($applications as $i => $app)
                <tr>
                    <td class="text-muted" style="font-size:.8rem;">{{ $applications->firstItem() + $i }}</td>
                    <td>
                        <span class="badge {{ $app->leave_type === 'annual' ? 'bg-primary' : 'bg-info text-dark' }}">
                            {{ $app->leave_type_label }}
                        </span>
                    </td>
                    <td style="font-size:.85rem;">{{ $app->start_date->format('d M Y') }}</td>
                    <td style="font-size:.85rem;">{{ $app->end_date->format('d M Y') }}</td>
                    <td><strong>{{ $app->total_days }}</strong> hari</td>
                    <td style="font-size:.82rem;max-width:180px;">
                        <span class="text-truncate d-block" style="max-width:160px;" title="{{ $app->reason }}">
                            {{ $app->reason }}
                        </span>
                    </td>
                    <td><span class="badge bg-{{ $app->status_color }}">{{ $app->status_label }}</span></td>
                    <td style="font-size:.8rem;color:#64748b;">{{ $app->created_at->format('d M Y') }}</td>
                    <td>
                        <a href="{{ route('employee.show', $app) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye"></i>
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
