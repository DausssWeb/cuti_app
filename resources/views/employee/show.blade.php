@extends('layouts.app')
@section('title', 'Detail Pengajuan Cuti')
@section('page-title', 'Detail Pengajuan Cuti')

@section('content')
<div class="row justify-content-center">
<div class="col-lg-8">

    <div class="mb-3">
        <a href="{{ route('employee.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
    </div>

    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0"><i class="bi bi-file-text me-2"></i>Informasi Pengajuan</h6>
            <span class="badge bg-{{ $leaveApplication->status_color }} fs-6">
                {{ $leaveApplication->status_label }}
            </span>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-sm-6">
                    <label class="form-label text-muted mb-1" style="font-size:.75rem;">Jenis Cuti</label>
                    <div class="fw-600">
                        <span class="badge {{ $leaveApplication->leave_type === 'annual' ? 'bg-primary' : 'bg-info text-dark' }} me-2">
                            {{ $leaveApplication->leave_type_label }}
                        </span>
                    </div>
                </div>
                <div class="col-sm-6">
                    <label class="form-label text-muted mb-1" style="font-size:.75rem;">Jumlah Hari Kerja</label>
                    <div class="fw-600">{{ $leaveApplication->total_days }} hari</div>
                </div>
                <div class="col-sm-6">
                    <label class="form-label text-muted mb-1" style="font-size:.75rem;">Tanggal Mulai</label>
                    <div class="fw-600">{{ $leaveApplication->start_date->isoFormat('dddd, D MMMM YYYY') }}</div>
                </div>
                <div class="col-sm-6">
                    <label class="form-label text-muted mb-1" style="font-size:.75rem;">Tanggal Selesai</label>
                    <div class="fw-600">{{ $leaveApplication->end_date->isoFormat('dddd, D MMMM YYYY') }}</div>
                </div>
                <div class="col-12">
                    <label class="form-label text-muted mb-1" style="font-size:.75rem;">Alasan Cuti</label>
                    <div class="p-3 bg-light rounded-3" style="font-size:.875rem;">{{ $leaveApplication->reason }}</div>
                </div>
                @if($leaveApplication->sick_note)
                <div class="col-12">
                    <label class="form-label text-muted mb-1" style="font-size:.75rem;">Surat Dokter</label>
                    <div>
                       <a href="{{ route('file.storage', ['path' => $leaveApplication->sick_note]) }}" target="_blank" class="btn btn-sm btn-outline-info">
                            <i class="bi bi-paperclip me-1"></i>Lihat Surat Dokter
                        </a>
                    </div>
                </div>
                @endif
                <div class="col-sm-6">
                    <label class="form-label text-muted mb-1" style="font-size:.75rem;">Tanggal Pengajuan</label>
                    <div style="font-size:.875rem;">{{ $leaveApplication->created_at->isoFormat('D MMMM YYYY, HH:mm') }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Approval Timeline --}}
    <div class="card">
        <div class="card-header">
            <h6 class="mb-0"><i class="bi bi-diagram-3 me-2"></i>Alur Persetujuan</h6>
        </div>
        <div class="card-body">
            <div class="d-flex flex-column gap-0">
                {{-- Step 1: Submitted --}}
                <div class="d-flex gap-3 align-items-start">
                    <div class="d-flex flex-column align-items-center">
                        <div style="width:36px;height:36px;border-radius:50%;background:#10b981;display:flex;align-items:center;justify-content:center;color:#fff;font-size:.9rem;flex-shrink:0;">
                            <i class="bi bi-check"></i>
                        </div>
                        <div style="width:2px;background:#e2e8f0;flex:1;min-height:32px;margin:4px 0;"></div>
                    </div>
                    <div class="pb-3">
                        <div style="font-size:.875rem;font-weight:600;">Pengajuan Dikirim</div>
                        <div style="font-size:.78rem;color:#64748b;">{{ $leaveApplication->created_at->isoFormat('D MMM YYYY, HH:mm') }}</div>
                    </div>
                </div>

                @if($leaveApplication->user->role === 'employee')
                {{-- Step 2: Manager --}}
                <div class="d-flex gap-3 align-items-start">
                    <div class="d-flex flex-column align-items-center">
                        @php
                            $mgColor = $leaveApplication->wasManagerApproved() ? '#10b981' : ($leaveApplication->isManagerRejected() ? '#ef4444' : '#d1d5db');
                            $mgIcon = $leaveApplication->wasManagerApproved() ? 'check' : ($leaveApplication->isManagerRejected() ? 'x' : 'three-dots');
                        @endphp
                        <div style="width:36px;height:36px;border-radius:50%;background:{{ $mgColor }};display:flex;align-items:center;justify-content:center;color:#fff;font-size:.9rem;flex-shrink:0;">
                            <i class="bi bi-{{ $mgIcon }}"></i>
                        </div>
                        <div style="width:2px;background:#e2e8f0;flex:1;min-height:32px;margin:4px 0;"></div>
                    </div>
                    <div class="pb-3">
                        <div style="font-size:.875rem;font-weight:600;">
                            Persetujuan Manager
                            @if($leaveApplication->wasManagerApproved()) <span class="badge bg-success ms-1">Disetujui</span>
                            @elseif($leaveApplication->isManagerRejected()) <span class="badge bg-danger ms-1">Ditolak</span>
                            @else <span class="badge bg-warning ms-1">Menunggu</span>
                            @endif
                        </div>
                        @if($leaveApplication->managerApprover)
                            <div style="font-size:.78rem;color:#64748b;">
                                Oleh {{ $leaveApplication->managerApprover->name }} · {{ $leaveApplication->manager_approved_at?->isoFormat('D MMM YYYY, HH:mm') }}
                            </div>
                        @endif
                        @if($leaveApplication->manager_notes)
                            <div class="mt-1 p-2 bg-light rounded" style="font-size:.8rem;">
                                <i class="bi bi-chat-left-text me-1"></i>{{ $leaveApplication->manager_notes }}
                            </div>
                        @endif
                    </div>
                </div>
                @endif

                {{-- Step 3: HRD --}}
                <div class="d-flex gap-3 align-items-start">
                    <div class="d-flex flex-column align-items-center">
                        @php
                            $hrdColor = $leaveApplication->isHrdApproved() ? '#10b981' : ($leaveApplication->isHrdRejected() ? '#ef4444' : '#d1d5db');
                            $hrdIcon = $leaveApplication->isHrdApproved() ? 'check' : ($leaveApplication->isHrdRejected() ? 'x' : 'three-dots');
                        @endphp
                        <div style="width:36px;height:36px;border-radius:50%;background:{{ $hrdColor }};display:flex;align-items:center;justify-content:center;color:#fff;font-size:.9rem;flex-shrink:0;">
                            <i class="bi bi-{{ $hrdIcon }}"></i>
                        </div>
                    </div>
                    <div>
                        <div style="font-size:.875rem;font-weight:600;">
                            Persetujuan HRD
                            @if($leaveApplication->isManagerRejected())
                                <span class="badge bg-secondary ms-1">Tidak Berlaku</span>
                            @elseif($leaveApplication->isHrdApproved())
                                <span class="badge bg-success ms-1">Disetujui</span>
                            @elseif($leaveApplication->isHrdRejected())
                                <span class="badge bg-danger ms-1">Ditolak</span>
                            @else
                                <span class="badge bg-warning ms-1">Menunggu</span>
                            @endif
                        </div>
                        @if($leaveApplication->hrdApprover)
                            <div style="font-size:.78rem;color:#64748b;">
                                Oleh {{ $leaveApplication->hrdApprover->name }} · {{ $leaveApplication->hrd_approved_at?->isoFormat('D MMM YYYY, HH:mm') }}
                            </div>
                        @endif
                        @if($leaveApplication->hrd_notes)
                            <div class="mt-1 p-2 bg-light rounded" style="font-size:.8rem;">
                                <i class="bi bi-chat-left-text me-1"></i>{{ $leaveApplication->hrd_notes }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
</div>
@endsection

@push('styles')
<style>.fw-600{font-weight:600;}</style>
@endpush
