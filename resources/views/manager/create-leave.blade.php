@extends('layouts.app')
@section('title', 'Ajukan Cuti')
@section('page-title', 'Ajukan Cuti Saya')

@section('content')
<div class="row justify-content-center">
<div class="col-lg-8">

<div class="alert alert-info d-flex gap-2 mb-4">
    <i class="bi bi-info-circle-fill mt-1 flex-shrink-0"></i>
    <div style="font-size:.85rem;">
        Pengajuan cuti Anda akan langsung diteruskan ke <strong>HRD</strong> untuk persetujuan.
        Sisa kuota cuti tahunan Anda: <strong>{{ $quota->remaining }} dari {{ $quota->annual_quota }} hari</strong>.
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h6 class="mb-0"><i class="bi bi-plus-circle me-2 text-primary"></i>Formulir Pengajuan Cuti Manager</h6>
    </div>
    <div class="card-body p-4">
        @if($errors->any())
            <div class="alert alert-danger">
                <strong><i class="bi bi-exclamation-triangle-fill me-2"></i>Terdapat kesalahan:</strong>
                <ul class="mb-0 mt-2">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <form method="POST" action="{{ route('manager.store-leave') }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label class="form-label fw-600">Jenis Cuti <span class="text-danger">*</span></label>
                <div class="row g-3">
                    <div class="col-sm-6">
                        <label class="d-block">
                            <input type="radio" name="leave_type" value="annual" class="d-none leave-type-radio" {{ old('leave_type') === 'annual' ? 'checked' : '' }}>
                            <div class="leave-type-card p-3 rounded-3 border text-center {{ old('leave_type') === 'annual' ? 'selected' : '' }}">
                                <i class="bi bi-calendar-event fs-2 text-primary d-block mb-2"></i>
                                <strong style="font-size:.9rem;">Cuti Tahunan</strong>
                                <div style="font-size:.75rem;color:#64748b;">Sisa {{ $quota->remaining }} hari</div>
                            </div>
                        </label>
                    </div>
                    <div class="col-sm-6">
                        <label class="d-block">
                            <input type="radio" name="leave_type" value="sick" class="d-none leave-type-radio" {{ old('leave_type') === 'sick' ? 'checked' : '' }}>
                            <div class="leave-type-card p-3 rounded-3 border text-center {{ old('leave_type') === 'sick' ? 'selected' : '' }}">
                                <i class="bi bi-heart-pulse fs-2 text-danger d-block mb-2"></i>
                                <strong style="font-size:.9rem;">Cuti Sakit</strong>
                                <div style="font-size:.75rem;color:#64748b;">Lampirkan surat dokter</div>
                            </div>
                        </label>
                    </div>
                </div>
                @error('leave_type')<div class="text-danger mt-1" style="font-size:.8rem;"><i class="bi bi-x-circle me-1"></i>{{ $message }}</div>@enderror
            </div>

            <div class="row g-3 mb-3">
                <div class="col-sm-6">
                    <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                    <input type="date" name="start_date" id="startDate"
                           class="form-control @error('start_date') is-invalid @enderror"
                           value="{{ old('start_date') }}" min="{{ now()->format('d-m-Y') }}" required>
                    @error('start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-sm-6">
                    <label class="form-label">Tanggal Selesai <span class="text-danger">*</span></label>
                    <input type="date" name="end_date" id="endDate"
                           class="form-control @error('end_date') is-invalid @enderror"
                           value="{{ old('end_date') }}" min="{{ now()->format('d-m-Y') }}" required>
                    @error('end_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div id="daysPreview" class="alert alert-secondary py-2 mb-3 d-none">
                <i class="bi bi-calendar-week me-2"></i>Estimasi: <strong id="daysCount">0</strong> hari kerja
            </div>

            <div class="mb-3">
                <label class="form-label">Alasan <span class="text-danger">*</span></label>
                <textarea name="reason" rows="4" class="form-control @error('reason') is-invalid @enderror"
                          placeholder="Jelaskan alasan cuti (min. 10 karakter)..." maxlength="500" required>{{ old('reason') }}</textarea>
                @error('reason')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div id="sickNoteField" class="mb-4 d-none">
                <label class="form-label">Surat Keterangan Dokter (opsional)</label>
                <input type="file" name="sick_note" class="form-control @error('sick_note') is-invalid @enderror"
                       accept=".pdf,.jpg,.jpeg,.png">
                @error('sick_note')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="form-text">Format: PDF, JPG, PNG. Maksimal 2MB.</div>
            </div>

            <hr class="my-4">
            <div class="d-flex gap-2 justify-content-end">
                <a href="{{ route('manager.my-leaves') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-x-circle me-1"></i>Batal
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-send me-2"></i>Kirim ke HRD
                </button>
            </div>
        </form>
    </div>
</div>
</div>
</div>
@endsection

@push('styles')
<style>
.leave-type-card { cursor:pointer; transition:all .2s; border-color:#d1d5db!important; }
.leave-type-card:hover { border-color:#2563eb!important; background:#f0f7ff; }
.leave-type-card.selected { border-color:#2563eb!important; background:#eff6ff; box-shadow:0 0 0 2px rgba(37,99,235,.3); }
.fw-600 { font-weight:600; }
</style>
@endpush

@push('scripts')
<script>
document.querySelectorAll('.leave-type-radio').forEach(r => {
    r.addEventListener('change', function() {
        document.querySelectorAll('.leave-type-card').forEach(c => c.classList.remove('selected'));
        this.closest('label').querySelector('.leave-type-card').classList.add('selected');
        document.getElementById('sickNoteField').classList.toggle('d-none', this.value !== 'sick');
    });
    if (r.checked) {
        r.closest('label').querySelector('.leave-type-card').classList.add('selected');
        if (r.value === 'sick') document.getElementById('sickNoteField').classList.remove('d-none');
    }
});
function countWorkingDays(start, end) {
    let count = 0, cur = new Date(start);
    while (cur <= end) { if (cur.getDay()!==0&&cur.getDay()!==6) count++; cur.setDate(cur.getDate()+1); }
    return count;
}
function updateDaysPreview() {
    const s = document.getElementById('startDate').value;
    const e = document.getElementById('endDate').value;
    if (s && e) {
        document.getElementById('daysCount').textContent = countWorkingDays(new Date(s), new Date(e));
        document.getElementById('daysPreview').classList.remove('d-none');
    } else { document.getElementById('daysPreview').classList.add('d-none'); }
}
document.getElementById('startDate')?.addEventListener('change', () => {
    document.getElementById('endDate').min = document.getElementById('startDate').value;
    updateDaysPreview();
});
document.getElementById('endDate')?.addEventListener('change', updateDaysPreview);
</script>
@endpush
