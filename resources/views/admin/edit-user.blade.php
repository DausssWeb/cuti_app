@extends('layouts.app')
@section('title', 'Edit Pengguna')
@section('page-title', 'Edit Pengguna')

@section('content')
<div class="row justify-content-center">
<div class="col-lg-8">

    <div class="mb-3">
        <a href="{{ route('admin.users') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0"><i class="bi bi-pencil me-2 text-primary"></i>Edit Pengguna: {{ $user->name }}</h6>
            <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-danger' }}">
                {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
            </span>
        </div>
        <div class="card-body p-4">
            @if($errors->any())
                <div class="alert alert-danger">
                    <strong><i class="bi bi-exclamation-triangle-fill me-2"></i>Terdapat kesalahan:</strong>
                    <ul class="mb-0 mt-2">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.update-user', $user) }}">
                @csrf @method('PUT')
                <div class="row g-3">
                    <div class="col-sm-6">
                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $user->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label">ID Karyawan <span class="text-danger">*</span></label>
                        <input type="text" name="employee_id" class="form-control @error('employee_id') is-invalid @enderror"
                               value="{{ old('employee_id', $user->employee_id) }}" required>
                        @error('employee_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email', $user->email) }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label">Role <span class="text-danger">*</span></label>
                        <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                            <option value="employee" {{ old('role',$user->role)==='employee' ? 'selected' : '' }}>Employee</option>
                            <option value="manager"  {{ old('role',$user->role)==='manager'  ? 'selected' : '' }}>Manager</option>
                            <option value="hrd"      {{ old('role',$user->role)==='hrd'      ? 'selected' : '' }}>HRD</option>
                        </select>
                        @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label">No. Telepon</label>
                        <input type="text" name="phone" class="form-control"
                               value="{{ old('phone', $user->phone) }}">
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label">Jabatan</label>
                        <input type="text" name="position" class="form-control"
                               value="{{ old('position', $user->position) }}">
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label">Tanggal Bergabung</label>
                        <input type="date" name="join_date" class="form-control"
                               value="{{ old('join_date', $user->join_date?->format('Y-m-d')) }}">
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label">Kuota Cuti Tahunan {{ now()->year }} <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="annual_quota" min="0" max="30"
                                   class="form-control @error('annual_quota') is-invalid @enderror"
                                   value="{{ old('annual_quota', $quota->annual_quota) }}" required>
                            <span class="input-group-text">hari</span>
                        </div>
                        @error('annual_quota')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        <div class="form-text">Sudah terpakai: <strong>{{ $quota->annual_used }}</strong> hari · Sisa: <strong>{{ $quota->remaining }}</strong> hari</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label">Status Akun</label>
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1"
                                   {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="isActive">Akun Aktif</label>
                        </div>
                    </div>

                    {{-- Password change section --}}
                    <div class="col-12">
                        <hr>
                        <p class="text-muted mb-2" style="font-size:.85rem;"><i class="bi bi-lock me-1"></i>Ganti Password (kosongkan jika tidak ingin mengubah)</p>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label">Password Baru</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                               placeholder="Minimal 8 karakter">
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" class="form-control"
                               placeholder="Ulangi password baru">
                    </div>
                </div>

                <hr class="my-4">
                <div class="d-flex gap-2 justify-content-end">
                    <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle me-1"></i>Batal
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-2"></i>Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
</div>
@endsection
