@extends('layouts.app')
@section('title', 'Detail Pengajuan Cuti')
@section('page-title', 'Detail & Persetujuan HRD')

@section('content')
<div class="row justify-content-center">
<div class="col-lg-8">

    <div class="mb-3">
        <a href="{{ route('hrd.all-leaves') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
    </div>

    {{-- Applicant Info --}}
    <div class="card mb-3">
        <div class="card-body">
            <div class="d-flex align-items-center gap-3">
                <div style="width:50px;height:50px;border-radius:50%;background:#8b5cf6;display:flex;align-items:center;justify-content:center;color:#fff;font-size:1.2rem;font-weight:700;flex-shrink:0;">
                    {{ strtoupper(substr($leaveApplication->user->name,0,2)) }}
                </div>
                <div>
                    <div style="font-size:1rem;font-weight:700;">{{ $leaveApplication->user->name }}</div>
                    <div style="font-size:.82rem;color:#64748b;">
                        {{ $leaveApplication->user->position }} ·
                        ID: {{ $leaveApplication->user->employee_id }} ·
                        <span class="badge {{ $leaveApplication->user->role==='manager' ? 'bg-warning text-dark' : 'bg-secondary' }}">{{ ucfirst($leaveApplication->user->role) }}</span>
                    </div>
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
                    <div><span class="badge {{ $leaveApplication->leave_type==='annual' ? 'bg-primary' : 'bg-info text-dark' }} fs-6">{{ $leaveApplication->leave_type_label }}</span></div>
                </div>
                <div class="col-sm-4">
                    <label class="form-label text-muted mb-1" style="font-size:.75rem;">Tanggal Mulai</label>
                    <div style="font-weight:600;">{{ $leaveApplication->start_date->isoFormat('D MMMM YYYY') }}</div>
                </div>
                <div class="col-sm-4">
                    <label class="form-label text-muted mb-1" style="font-size:.75rem;">Tanggal Selesai</label>
                    <div style="font-weight:600;">{{ $leaveApplication->end_date->isoFormat('D MMMM YYYY') }}</div>
                </div>
                <div class="col-sm-4">
                    <label class="form-label text-muted mb-1" style="font-size:.75rem;">Jumlah Hari Kerja</label>
                    <div style="font-size:1.4rem;font-weight:700;color:#2563eb;">{{ $leaveApplication->total_days }} <small style="font-size:.7rem;font-weight:400;color:#64748b;">hari</small></div>
                </div>
                <div class="col-sm-4">
                    <label class="form-label text-muted mb-1" style="font-size:.75rem;">Diajukan Pada</label>
                    <div style="font-size:.875rem;">{{ $leaveApplication->created_at->isoFormat('D MMMM YYYY, HH:mm') }}</div>
                </div>
                @if($leaveApplication->user->role === 'employee')
                <div class="col-sm-4">
                    <label class="form-label text-muted mb-1" style="font-size:.75rem;">Kuota Cuti Tahunan</label>
                    @php $quota = $leaveApplication->user->getOrCreateQuota($leaveApplication->year); @endphp
                    <div style="font-size:.875rem;">{{ $quota->annual_used }}/{{ $quota->annual_quota }} terpakai · Sisa <strong>{{ $quota->remaining }}</strong></div>
                </div>
                @endif
                <div class="col-12">
                    <label class="form-label text-muted mb-1" style="font-size:.75rem;">Alasan Cuti</label>
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

    {{-- Manager approval info (for employee leaves) --}}
    @if($leaveApplication->user->role === 'employee')
    <div class="card mb-3">
        <div class="card-header"><h6 class="mb-0"><i class="bi bi-person-check me-2"></i>Persetujuan Manager</h6></div>
        <div class="card-body">
            @if($leaveApplication->managerApprover)
                <div class="d-flex align-items-center gap-3">
                    <i class="bi bi-check-circle-fill text-success fs-4"></i>
                    <div>
                        <div style="font-size:.875rem;font-weight:600;">Disetujui oleh {{ $leaveApplication->managerApprover->name }}</div>
                        <div style="font-size:.78rem;color:#64748b;">{{ $leaveApplication->manager_approved_at?->isoFormat('D MMMM YYYY, HH:mm') }}</div>
                        @if($leaveApplication->manager_notes)
                            <div class="mt-1 p-2 bg-light rounded" style="font-size:.82rem;">
                                <i class="bi bi-chat-left-text me-1"></i>{{ $leaveApplication->manager_notes }}
                            </div>
                        @endif
                    </div>
                </div>
            @elseif($leaveApplication->isManagerRejected())
                <div class="d-flex align-items-center gap-3">
                    <i class="bi bi-x-circle-fill text-danger fs-4"></i>
                    <div>
                        <div style="font-size:.875rem;font-weight:600;color:#ef4444;">Ditolak oleh Manager</div>
                        @if($leaveApplication->manager_notes)
                            <div class="mt-1 p-2 bg-light rounded" style="font-size:.82rem;">{{ $leaveApplication->manager_notes }}</div>
                        @endif
                    </div>
                </div>
            @else
                <div class="text-muted" style="font-size:.875rem;">
                    <i class="bi bi-hourglass-split me-2 text-warning"></i>Masih menunggu persetujuan manager.
                </div>
            @endif
        </div>
    </div>
    @else
    <div class="alert alert-info d-flex gap-2">
        <i class="bi bi-info-circle-fill mt-1 flex-shrink-0"></i>
        <span style="font-size:.85rem;">Pengajuan ini dari <strong>Manager</strong>. Tidak perlu persetujuan manager — langsung diproses HRD.</span>
    </div>
    @endif

    {{-- HRD Approval Action --}}
    @php
        $canApprove = ($leaveApplication->user->role === 'employee' && $leaveApplication->isManagerApproved())
                   || ($leaveApplication->user->role === 'manager'   && $leaveApplication->isPending());
    @endphp

    @if($canApprove)
    <div class="card border-primary">
        <div class="card-header bg-primary bg-opacity-10 border-primary">
            <h6 class="mb-0 text-primary"><i class="bi bi-check2-square me-2"></i>Tindakan Persetujuan HRD</h6>
        </div>
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger mb-3">
                    @foreach($errors->all() as $e)<div><i class="bi bi-x-circle me-1"></i>{{ $e }}</div>@endforeach
                </div>
            @endif
            <form method="POST" action="{{ route('hrd.approve-leave', $leaveApplication) }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Catatan HRD <span class="text-muted">(wajib jika menolak)</span></label>
                    <textarea name="notes" rows="3"
                              class="form-control @error('notes') is-invalid @enderror"
                              placeholder="Tambahkan catatan persetujuan atau alasan penolakan...">{{ old('notes') }}</textarea>
                    @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" name="action" value="approve" class="btn btn-success flex-fill"
                            onclick="return confirm('Setujui pengajuan cuti {{ $leaveApplication->user->name }}? Kuota cuti akan dikurangi.')">
                        <i class="bi bi-check-circle me-2"></i>Setujui & Kurangi Kuota
                    </button>
                    <button type="submit" name="action" value="reject" class="btn btn-danger flex-fill"
                            onclick="return confirm('Tolak pengajuan cuti ini?')">
                        <i class="bi bi-x-circle me-2"></i>Tolak
                    </button>
                </div>
            </form>
        </div>
    </div>

    @elseif($leaveApplication->isHrdApproved() || $leaveApplication->isHrdRejected())
    <div class="card">
        <div class="card-header"><h6 class="mb-0"><i class="bi bi-patch-check me-2"></i>Keputusan HRD</h6></div>
        <div class="card-body">
            <div class="d-flex align-items-center gap-3">
                <i class="bi bi-{{ $leaveApplication->isHrdApproved() ? 'check-circle-fill text-success' : 'x-circle-fill text-danger' }} fs-4"></i>
                <div>
                    <div style="font-size:.875rem;font-weight:600;">
                        {{ $leaveApplication->isHrdApproved() ? 'Disetujui' : 'Ditolak' }} oleh {{ $leaveApplication->hrdApprover?->name }}
                    </div>
                    <div style="font-size:.78rem;color:#64748b;">{{ $leaveApplication->hrd_approved_at?->isoFormat('D MMMM YYYY, HH:mm') }}</div>
                    @if($leaveApplication->hrd_notes)
                        <div class="mt-1 p-2 bg-light rounded" style="font-size:.82rem;">
                            <i class="bi bi-chat-left-text me-1"></i>{{ $leaveApplication->hrd_notes }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="alert alert-warning">
        <i class="bi bi-info-circle me-2"></i>
        Pengajuan ini belum dapat diproses karena masih menunggu persetujuan manager.
    </div>
    @endif

</div>
</div>
@endsection
