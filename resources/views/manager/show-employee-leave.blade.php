@extends('layouts.app')
@section('title', 'Review Pengajuan Cuti')
@section('page-title', 'Review Pengajuan Cuti')

@section('content')
<div class="row justify-content-center">
<div class="col-lg-8">

    <div class="mb-3">
        <a href="{{ route('manager.employee-leaves') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
    </div>

    {{-- Applicant Info --}}
    <div class="card mb-3">
        <div class="card-body">
            <div class="d-flex align-items-center gap-3">
                <div style="width:50px;height:50px;border-radius:50%;background:#2563eb;display:flex;align-items:center;justify-content:center;color:#fff;font-size:1.2rem;font-weight:700;flex-shrink:0;">
                    {{ strtoupper(substr($leaveApplication->user->name,0,2)) }}
                </div>
                <div>
                    <div style="font-size:1rem;font-weight:700;">{{ $leaveApplication->user->name }}</div>
                    <div style="font-size:.82rem;color:#64748b;">{{ $leaveApplication->user->position }} · ID: {{ $leaveApplication->user->employee_id }}</div>
                </div>
                <div class="ms-auto">
                    <span class="badge bg-{{ $leaveApplication->status_color }} fs-6">{{ $leaveApplication->status_label }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Leave Details --}}
    <div class="card mb-3">
        <div class="card-header"><h6 class="mb-0"><i class="bi bi-file-text me-2"></i>Detail Pengajuan</h6></div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-sm-4">
                    <label class="form-label text-muted mb-1" style="font-size:.75rem;">Jenis Cuti</label>
                    <div><span class="badge {{ $leaveApplication->leave_type === 'annual' ? 'bg-primary' : 'bg-info text-dark' }}">{{ $leaveApplication->leave_type_label }}</span></div>
                </div>
                <div class="col-sm-4">
                    <label class="form-label text-muted mb-1" style="font-size:.75rem;">Tanggal Mulai</label>
                    <div style="font-size:.875rem;font-weight:600;">{{ $leaveApplication->start_date->isoFormat('D MMMM YYYY') }}</div>
                </div>
                <div class="col-sm-4">
                    <label class="form-label text-muted mb-1" style="font-size:.75rem;">Tanggal Selesai</label>
                    <div style="font-size:.875rem;font-weight:600;">{{ $leaveApplication->end_date->isoFormat('D MMMM YYYY') }}</div>
                </div>
                <div class="col-sm-4">
                    <label class="form-label text-muted mb-1" style="font-size:.75rem;">Jumlah Hari Kerja</label>
                    <div style="font-size:1.2rem;font-weight:700;color:#2563eb;">{{ $leaveApplication->total_days }} <small style="font-size:.7rem;font-weight:400;color:#64748b;">hari</small></div>
                </div>
                <div class="col-sm-4">
                    <label class="form-label text-muted mb-1" style="font-size:.75rem;">Tanggal Pengajuan</label>
                    <div style="font-size:.875rem;">{{ $leaveApplication->created_at->isoFormat('D MMMM YYYY') }}</div>
                </div>
                <div class="col-12">
                    <label class="form-label text-muted mb-1" style="font-size:.75rem;">Alasan</label>
                    <div class="p-3 bg-light rounded-3" style="font-size:.875rem;">{{ $leaveApplication->reason }}</div>
                </div>
                @if($leaveApplication->sick_note)
                <div class="col-12">
                    <label class="form-label text-muted mb-1" style="font-size:.75rem;">Surat Keterangan Dokter</label>
                    <a href="{{ Storage::url($leaveApplication->sick_note) }}" target="_blank" class="btn btn-sm btn-outline-info">
                        <i class="bi bi-paperclip me-1"></i>Lihat Surat Dokter
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Approval Action (only if pending) --}}
    @if($leaveApplication->isPending())
    <div class="card border-warning">
        <div class="card-header bg-warning bg-opacity-10 border-warning">
            <h6 class="mb-0 text-warning"><i class="bi bi-check2-square me-2"></i>Tindakan Persetujuan</h6>
        </div>
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger mb-3">
                    @foreach($errors->all() as $e)<div><i class="bi bi-x-circle me-1"></i>{{ $e }}</div>@endforeach
                </div>
            @endif
            <form method="POST" action="{{ route('manager.approve-leave', $leaveApplication) }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Catatan (wajib diisi jika menolak)</label>
                    <textarea name="notes" rows="3" class="form-control @error('notes') is-invalid @enderror"
                              placeholder="Tambahkan catatan persetujuan atau alasan penolakan...">{{ old('notes') }}</textarea>
                    @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" name="action" value="approve"
                            class="btn btn-success flex-fill"
                            onclick="return confirm('Setujui pengajuan cuti {{ $leaveApplication->user->name }}?')">
                        <i class="bi bi-check-circle me-2"></i>Setujui
                    </button>
                    <button type="submit" name="action" value="reject"
                            class="btn btn-danger flex-fill"
                            onclick="return confirm('Tolak pengajuan cuti {{ $leaveApplication->user->name }}?')">
                        <i class="bi bi-x-circle me-2"></i>Tolak
                    </button>
                </div>
            </form>
        </div>
    </div>
    @else
    <div class="card">
        <div class="card-body">
            <div class="text-center text-muted py-2">
                <i class="bi bi-info-circle me-2"></i>
                Pengajuan ini sudah diproses dengan status: <strong>{{ $leaveApplication->status_label }}</strong>
            </div>
            @if($leaveApplication->manager_notes)
                <div class="mt-2 p-3 bg-light rounded">
                    <strong style="font-size:.8rem;">Catatan Manager:</strong>
                    <div style="font-size:.875rem;">{{ $leaveApplication->manager_notes }}</div>
                </div>
            @endif
        </div>
    </div>
    @endif

</div>
</div>
@endsection
