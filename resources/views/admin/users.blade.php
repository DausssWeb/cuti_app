@extends('layouts.app')
@section('title', 'Manajemen Pengguna')
@section('page-title', 'Manajemen Pengguna')

@section('content')
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-sm-4">
                <label class="form-label mb-1" style="font-size:.8rem;">Cari</label>
                <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm"
                       placeholder="Nama, email, atau ID karyawan...">
            </div>
            <div class="col-sm-3">
                <label class="form-label mb-1" style="font-size:.8rem;">Role</label>
                <select name="role" class="form-select form-select-sm">
                    <option value="">Semua Role</option>
                    <option value="employee" {{ request('role')==='employee' ? 'selected' : '' }}>Employee</option>
                    <option value="manager"  {{ request('role')==='manager'  ? 'selected' : '' }}>Manager</option>
                    <option value="hrd"      {{ request('role')==='hrd'      ? 'selected' : '' }}>HRD</option>
                </select>
            </div>
            <div class="col-sm-2">
                <button type="submit" class="btn btn-primary btn-sm w-100"><i class="bi bi-search me-1"></i>Cari</button>
            </div>
            <div class="col-sm-3 text-end">
                <a href="{{ route('admin.create-user') }}" class="btn btn-success btn-sm">
                    <i class="bi bi-person-plus me-1"></i>Tambah Pengguna
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h6 class="mb-0"><i class="bi bi-people me-2"></i>Daftar Pengguna ({{ $users->total() }})</h6>
    </div>
    <div class="card-body p-0">
        @if($users->isEmpty())
            <div class="text-center py-5 text-muted"><i class="bi bi-inbox fs-1 d-block mb-3"></i>Tidak ada pengguna ditemukan.</div>
        @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No</th><th>Pengguna</th><th>Email</th><th>Role</th>
                        <th>Jabatan</th><th>Bergabung</th><th>Kuota/Sisa</th><th>Status</th><th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($users as $i => $u)
                <tr>
                    <td class="text-muted" style="font-size:.8rem;">{{ $users->firstItem()+$i }}</td>
                    <td>
                        <div style="font-size:.875rem;font-weight:600;">{{ $u->name }}</div>
                        <div style="font-size:.75rem;color:#64748b;">{{ $u->employee_id }}</div>
                    </td>
                    <td style="font-size:.82rem;">{{ $u->email }}</td>
                    <td>
                        @php $rc = ['employee'=>'secondary','manager'=>'warning','hrd'=>'info']; @endphp
                        <span class="badge bg-{{ $rc[$u->role]??'secondary' }} {{ $u->role==='manager'?'text-dark':'' }}">
                            {{ ucfirst($u->role) }}
                        </span>
                    </td>
                    <td style="font-size:.82rem;">{{ $u->position ?? '-' }}</td>
                    <td style="font-size:.8rem;color:#64748b;">{{ $u->join_date?->format('d M Y') ?? '-' }}</td>
                    <td style="font-size:.82rem;">
                        @php $q = $u->getOrCreateQuota(now()->year); @endphp
                        <span class="text-primary fw-bold">{{ $q->annual_quota }}</span>
                        / Sisa <span class="text-success fw-bold">{{ $q->remaining }}</span>
                    </td>
                    <td>
                        <span class="badge {{ $u->is_active ? 'bg-success' : 'bg-danger' }}">
                            {{ $u->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('admin.edit-user', $u) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.toggle-user', $u) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-sm {{ $u->is_active ? 'btn-outline-danger' : 'btn-outline-success' }}"
                                        onclick="return confirm('{{ $u->is_active ? 'Nonaktifkan' : 'Aktifkan' }} akun {{ $u->name }}?')">
                                    <i class="bi bi-{{ $u->is_active ? 'slash-circle' : 'check-circle' }}"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $users->appends(request()->query())->links() }}</div>
        @endif
    </div>
</div>
@endsection
