@extends('layouts.app')
@section('title', 'Riwayat Cuti Saya')
@section('page-title', 'Riwayat Cuti Saya')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="card text-center p-3 border-primary">
            <div style="font-size:1.5rem;font-weight:700;color:#2563eb;">{{ $quota->annual_quota }}</div>
            <div style="font-size:.78rem;color:#64748b;">Kuota Tahunan</div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card text-center p-3 border-warning">
            <div style="font-size:1.5rem;font-weight:700;color:#f59e0b;">{{ $quota->annual_used }}</div>
            <div style="font-size:.78rem;color:#64748b;">Terpakai</div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card text-center p-3 border-success">
            <div style="font-size:1.5rem;font-weight:700;color:#10b981;">{{ $quota->remaining }}</div>
            <div style="font-size:.78rem;color:#64748b;">Sisa Cuti</div>
        </div>
    </div>
</div>

<div class="alert alert-info d-flex gap-2">
    <i class="bi bi-info-circle-fill mt-1 flex-shrink-0"></i>
    <div style="font-size:.85rem;">
        Sebagai <strong>Manager</strong>, pengajuan cuti Anda akan langsung diproses oleh <strong>HRD</strong> (tanpa perlu persetujuan manager).
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0"><i class="bi bi-list-ul me-2"></i>Pengajuan Cuti Saya</h6>
        <a href="{{ route('manager.create-leave') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle me-1"></i>Ajukan Baru
        </a>
    </div>
    <div class="card-body p-0">
        @if($applications->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                Belum ada pengajuan cuti.
            </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>#</th><th>Jenis</th><th>Tanggal</th><th>Hari</th><th>Status</th><th>Catatan HRD</th><th>Diajukan</th></tr>
                </thead>
                <tbody>
                @foreach($applications as $i => $app)
                <tr>
                    <td class="text-muted" style="font-size:.8rem;">{{ $applications->firstItem()+$i }}</td>
                    <td><span class="badge {{ $app->leave_type==='annual' ? 'bg-primary' : 'bg-info text-dark' }}">{{ $app->leave_type_label }}</span></td>
                    <td style="font-size:.82rem;">{{ $app->start_date->format('d M Y') }} – {{ $app->end_date->format('d M Y') }}</td>
                    <td><strong>{{ $app->total_days }}</strong></td>
                    <td><span class="badge bg-{{ $app->status_color }}">{{ $app->status_label }}</span></td>
                    <td style="font-size:.8rem;color:#64748b;">{{ $app->hrd_notes ?? '-' }}</td>
                    <td style="font-size:.8rem;color:#64748b;">{{ $app->created_at->format('d M Y') }}</td>
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
