@extends('layouts.app')
@section('title', 'Cetak Laporan Cuti PDF')
@section('page-title', 'Cetak Laporan Cuti')

@section('content')
<div class="row justify-content-center">
<div class="col-lg-6">

<div class="card">
    <div class="card-header">
        <h6 class="mb-0"><i class="bi bi-file-earmark-pdf me-2 text-danger"></i>Generate Laporan Cuti PDF</h6>
    </div>
    <div class="card-body p-4">
        <div class="alert alert-info d-flex gap-2 mb-4">
            <i class="bi bi-info-circle-fill mt-1 flex-shrink-0"></i>
            <div style="font-size:.85rem;">
                Laporan mencakup semua pengajuan cuti karyawan dan manager dalam satu tahun.
                File akan diunduh langsung sebagai PDF landscape A4.
            </div>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                @foreach($errors->all() as $e)<div><i class="bi bi-x-circle me-1"></i>{{ $e }}</div>@endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('hrd.report.generate') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Tahun <span class="text-danger">*</span></label>
                <select name="year" class="form-select @error('year') is-invalid @enderror" required>
                    <option value="">-- Pilih Tahun --</option>
                    @foreach($years as $y)
                        <option value="{{ $y }}" {{ old('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
                @error('year')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Jenis Cuti <span class="text-muted">(opsional)</span></label>
                <select name="leave_type" class="form-select">
                    <option value="">Semua Jenis Cuti</option>
                    <option value="annual" {{ old('leave_type')==='annual' ? 'selected' : '' }}>Cuti Tahunan</option>
                    <option value="sick"   {{ old('leave_type')==='sick'   ? 'selected' : '' }}>Cuti Sakit</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="form-label">Status <span class="text-muted">(opsional)</span></label>
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="hrd_approved"     {{ old('status')==='hrd_approved'     ? 'selected' : '' }}>Disetujui HRD</option>
                    <option value="hrd_rejected"     {{ old('status')==='hrd_rejected'     ? 'selected' : '' }}>Ditolak HRD</option>
                    <option value="manager_approved" {{ old('status')==='manager_approved' ? 'selected' : '' }}>Disetujui Manager (menunggu HRD)</option>
                    <option value="pending"          {{ old('status')==='pending'          ? 'selected' : '' }}>Menunggu</option>
                </select>
            </div>

            <button type="submit" class="btn btn-danger w-100">
                <i class="bi bi-file-earmark-pdf me-2"></i>Generate & Unduh PDF
            </button>
        </form>
    </div>
</div>

</div>
</div>
@endsection
