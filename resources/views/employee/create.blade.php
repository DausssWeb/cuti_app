@extends('layouts.app')
@section('title', 'Ajukan Cuti')
@section('page-title', 'Ajukan Cuti Baru')

@section('content')
<div class="row justify-content-center">
<div class="col-lg-8">

{{-- Quota reminder --}}
<div class="alert alert-info d-flex align-items-start gap-3 mb-4">
    <i class="bi bi-info-circle-fill fs-5 mt-1 flex-shrink-0"></i>
    <div>
        <strong>Informasi Kuota Cuti {{ now()->year }}</strong><br>
        <span style="font-size:.85rem;">
            Cuti Tahunan: <strong>{{ $quota->annual_used }} / {{ $quota->annual_quota }} hari</strong> terpakai
            · Sisa <strong>{{ $quota->remaining }} hari</strong>
            &nbsp;|&nbsp; Cuti Sakit: <strong>{{ $quota->sick_used }} hari</strong> (tidak terbatas)
        </span>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h6 class="mb-0"><i class="bi bi-plus-circle me-2 text-primary"></i>Formulir Pengajuan Cuti</h6>
    </div>
    <div class="card-body p-4">
        @if($errors->any())
            <div class="alert alert-danger">
                <strong><i class="bi bi-exclamation-triangle-fill me-2"></i>Terdapat kesalahan:</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('employee.store') }}" enctype="multipart/form-data">
            @csrf

            {{-- Jenis Cuti --}}
            <div class="mb-4">
                <label class="form-label fw-600">Jenis Cuti <span class="text-danger">*</span></label>
                <div class="row g-3">
                    <div class="col-sm-6">
                        <label class="d-block cursor-pointer">
                            <input type="radio" name="leave_type" value="annual" class="d-none leave-type-radio"
                                   {{ old('leave_type') === 'annual' ? 'checked' : '' }}>
                            <div class="leave-type-card p-3 rounded-3 border text-center {{ old('leave_type') === 'annual' ? 'selected' : '' }}">
                                <i class="bi bi-calendar-event fs-2 text-primary d-block mb-2"></i>
                                <strong style="font-size:.9rem;">Cuti Tahunan</strong>
                                <div style="font-size:.75rem;color:#64748b;">Sisa {{ $quota->remaining }} hari</div>
                            </div>
                        </label>
                    </div>
                    <div class="col-sm-6">
                        <label class="d-block cursor-pointer">
                            <input type="radio" name="leave_type" value="sick" class="d-none leave-type-radio"
                                   {{ old('leave_type') === 'sick' ? 'checked' : '' }}>
                            <div class="leave-type-card p-3 rounded-3 border text-center {{ old('leave_type') === 'sick' ? 'selected' : '' }}">
                                <i class="bi bi-heart-pulse fs-2 text-danger d-block mb-2"></i>
                                <strong style="font-size:.9rem;">Cuti Sakit</strong>
                                <div style="font-size:.75rem;color:#64748b;">Dapat dilampirkan surat dokter</div>
                            </div>
                        </label>
                    </div>
                </div>
                @error('leave_type')
                    <div class="text-danger mt-1" style="font-size:.8rem;"><i class="bi bi-x-circle me-1"></i>{{ $message }}</div>
                @enderror
            </div>

            {{-- Dates --}}
            <div class="row g-3 mb-3">
                <div class="col-sm-6">
                    <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                    <input type="date" name="start_date" id="startDate"
                           class="form-control @error('start_date') is-invalid @enderror"
                           value="{{ old('start_date') }}"
                           min="{{ now()->format('Y-m-d') }}" required>
                    @error('start_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-sm-6">
                    <label class="form-label">Tanggal Selesai <span class="text-danger">*</span></label>
                    <input type="date" name="end_date" id="endDate"
                           class="form-control @error('end_date') is-invalid @enderror"
                           value="{{ old('end_date') }}"
                           min="{{ now()->format('Y-m-d') }}" required>
                    @error('end_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Working days preview --}}
            <div id="daysPreview" class="alert alert-secondary py-2 mb-3 d-none">
                <i class="bi bi-calendar-week me-2"></i>
                Estimasi: <strong id="daysCount">0</strong> hari kerja
            </div>

            {{-- Reason --}}
            <div class="mb-3">
                <label class="form-label">Alasan Cuti <span class="text-danger">*</span></label>
                <textarea name="reason" rows="4"
                          class="form-control @error('reason') is-invalid @enderror"
                          placeholder="Jelaskan alasan pengajuan cuti Anda secara singkat (minimal 10 karakter)..."
                          maxlength="500" required>{{ old('reason') }}</textarea>
                @error('reason')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text"><span id="charCount">0</span>/500 karakter</div>
            </div>

            {{-- Sick note (conditional) --}}
            <div id="sickNoteField" class="mb-4 d-none">
                <label class="form-label">Surat Keterangan Dokter <span class="text-danger">*</span></label>
                <input type="file" name="sick_note" class="form-control @error('sick_note') is-invalid @enderror"
                       accept=".pdf,.jpg,.jpeg,.png">
                @error('sick_note')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">Format: PDF, JPG, PNG. Maksimal 2MB.</div>
            </div>

            <hr class="my-4">
            <div class="d-flex gap-2 justify-content-end">
                <a href="{{ route('employee.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-x-circle me-1"></i>Batal
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-send me-2"></i>Kirim Pengajuan
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
// Leave type toggle
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

// Char counter
const reasonTa = document.querySelector('textarea[name="reason"]');
reasonTa?.addEventListener('input', () => {
    document.getElementById('charCount').textContent = reasonTa.value.length;
});
if (reasonTa) document.getElementById('charCount').textContent = reasonTa.value.length;

// Working days estimator (client-side, weekends excluded)
function countWorkingDays(start, end) {
    let count = 0, cur = new Date(start);
    while (cur <= end) {
        if (cur.getDay() !== 0 && cur.getDay() !== 6) count++;
        cur.setDate(cur.getDate() + 1);
    }
    return count;
}
function updateDaysPreview() {
    const s = document.getElementById('startDate').value;
    const e = document.getElementById('endDate').value;
    if (s && e) {
        const days = countWorkingDays(new Date(s), new Date(e));
        document.getElementById('daysCount').textContent = days;
        document.getElementById('daysPreview').classList.remove('d-none');
    } else {
        document.getElementById('daysPreview').classList.add('d-none');
    }
}
document.getElementById('startDate')?.addEventListener('change', () => {
    const s = document.getElementById('startDate').value;
    if (s) document.getElementById('endDate').min = s;
    updateDaysPreview();
});
document.getElementById('endDate')?.addEventListener('change', updateDaysPreview);
</script>
@endpush
