@php $role = Auth::user()->role; @endphp

@if($role === 'employee')
    <p class="nav-section">Menu</p>
    <a href="{{ route('employee.dashboard') }}" class="nav-link {{ request()->routeIs('employee.dashboard') ? 'active' : '' }}">
        <i class="bi bi-speedometer2"></i> Dashboard
    </a>
    <a href="{{ route('employee.index') }}" class="nav-link {{ request()->routeIs('employee.index','employee.show') ? 'active' : '' }}">
        <i class="bi bi-list-ul"></i> Riwayat Pengajuan
    </a>
    <a href="{{ route('employee.create') }}" class="nav-link {{ request()->routeIs('employee.create') ? 'active' : '' }}">
        <i class="bi bi-plus-circle"></i> Ajukan Cuti
    </a>
@endif

@if($role === 'manager')
    <p class="nav-section">Menu</p>
    <a href="{{ route('manager.dashboard') }}" class="nav-link {{ request()->routeIs('manager.dashboard') ? 'active' : '' }}">
        <i class="bi bi-speedometer2"></i> Dashboard
    </a>
    <p class="nav-section">Persetujuan</p>
    <a href="{{ route('manager.employee-leaves') }}" class="nav-link {{ request()->routeIs('manager.employee-leaves*') ? 'active' : '' }}">
        <i class="bi bi-person-check"></i> Persetujuan Karyawan
        @php $pending = \App\Models\LeaveApplication::forEmployee()->pending()->count(); @endphp
        @if($pending > 0)
            <span class="badge bg-danger ms-auto">{{ $pending }}</span>
        @endif
    </a>
    <p class="nav-section">Cuti Saya</p>
    <a href="{{ route('manager.my-leaves') }}" class="nav-link {{ request()->routeIs('manager.my-leaves') ? 'active' : '' }}">
        <i class="bi bi-list-ul"></i> Riwayat Cuti
    </a>
    <a href="{{ route('manager.create-leave') }}" class="nav-link {{ request()->routeIs('manager.create-leave') ? 'active' : '' }}">
        <i class="bi bi-plus-circle"></i> Ajukan Cuti
    </a>
@endif

@if($role === 'hrd')
    <p class="nav-section">Menu</p>
    <a href="{{ route('hrd.dashboard') }}" class="nav-link {{ request()->routeIs('hrd.dashboard') ? 'active' : '' }}">
        <i class="bi bi-speedometer2"></i> Dashboard
    </a>
    <p class="nav-section">Persetujuan</p>
    <a href="{{ route('hrd.all-leaves') }}" class="nav-link {{ request()->routeIs('hrd.all-leaves*') ? 'active' : '' }}">
        <i class="bi bi-journal-check"></i> Semua Pengajuan
    </a>
    <a href="{{ route('hrd.all-leaves', ['filter'=>'awaiting']) }}" class="nav-link">
        <i class="bi bi-hourglass-split"></i> Menunggu Saya
    </a>
    <p class="nav-section">Laporan</p>
    <a href="{{ route('hrd.report') }}" class="nav-link {{ request()->routeIs('hrd.report*') ? 'active' : '' }}">
        <i class="bi bi-file-earmark-pdf"></i> Cetak Laporan PDF
    </a>
@endif

@if($role === 'admin')
    <p class="nav-section">Menu</p>
    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <i class="bi bi-speedometer2"></i> Dashboard
    </a>
    <p class="nav-section">Manajemen</p>
    <a href="{{ route('admin.users') }}" class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
        <i class="bi bi-people"></i> Pengguna
    </a>
    <a href="{{ route('admin.create-user') }}" class="nav-link {{ request()->routeIs('admin.create-user') ? 'active' : '' }}">
        <i class="bi bi-person-plus"></i> Tambah Pengguna
    </a>
@endif
