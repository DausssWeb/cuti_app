<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Cuti {{ $year }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 10px; color: #1e293b; background: #fff; }

        /* Header */
        .header { background: #1e40af; color: #fff; padding: 16px 20px; margin-bottom: 16px; }
        .header-top { display: flex; justify-content: space-between; align-items: center; }
        .company-name { font-size: 16px; font-weight: 700; }
        .report-title { font-size: 13px; font-weight: 600; margin-top: 4px; opacity: .9; }
        .header-meta { font-size: 9px; opacity: .8; text-align: right; }

        /* Filter info */
        .filter-bar { background: #f0f7ff; border: 1px solid #bfdbfe; border-radius: 6px; padding: 8px 12px; margin-bottom: 14px; font-size: 9px; color: #1e40af; }
        .filter-bar strong { margin-right: 16px; }

        /* Section title */
        .section-title { font-size: 11px; font-weight: 700; color: #1e40af; border-bottom: 2px solid #1e40af; padding-bottom: 4px; margin-bottom: 10px; margin-top: 16px; }

        /* Summary table */
        .summary-table { width: 100%; border-collapse: collapse; margin-bottom: 16px; font-size: 9px; }
        .summary-table th { background: #1e40af; color: #fff; padding: 6px 8px; text-align: left; font-weight: 600; }
        .summary-table td { padding: 5px 8px; border-bottom: 1px solid #e2e8f0; }
        .summary-table tr:nth-child(even) td { background: #f8fafc; }
        .summary-table .num { text-align: center; }

        /* Detail table */
        .detail-table { width: 100%; border-collapse: collapse; margin-bottom: 16px; font-size: 8.5px; }
        .detail-table th { background: #334155; color: #fff; padding: 5px 6px; text-align: left; font-weight: 600; }
        .detail-table td { padding: 4px 6px; border-bottom: 1px solid #e2e8f0; vertical-align: top; }
        .detail-table tr:nth-child(even) td { background: #f8fafc; }

        /* Badges */
        .badge { display: inline-block; padding: 2px 6px; border-radius: 4px; font-size: 8px; font-weight: 700; }
        .badge-success  { background: #d1fae5; color: #065f46; }
        .badge-danger   { background: #fee2e2; color: #991b1b; }
        .badge-warning  { background: #fef3c7; color: #92400e; }
        .badge-info     { background: #dbeafe; color: #1e40af; }
        .badge-primary  { background: #eff6ff; color: #1d4ed8; }
        .badge-secondary{ background: #f1f5f9; color: #475569; }

        /* Stats row */
        .stats-row { display: flex; gap: 12px; margin-bottom: 14px; }
        .stat-box { flex: 1; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 8px 10px; text-align: center; }
        .stat-box .val { font-size: 16px; font-weight: 700; color: #1e40af; }
        .stat-box .lbl { font-size: 8px; color: #64748b; margin-top: 2px; }

        /* Footer */
        .footer { margin-top: 20px; border-top: 1px solid #e2e8f0; padding-top: 8px; font-size: 8px; color: #94a3b8; display: flex; justify-content: space-between; }

        .text-success { color: #059669; }
        .text-danger  { color: #dc2626; }
        .text-warning { color: #d97706; }
        .text-muted   { color: #94a3b8; }
        .fw-bold      { font-weight: 700; }
    </style>
</head>
<body>

{{-- Header --}}
<div class="header">
    <div class="header-top">
        <div>
            <div class="company-name">🏢 Leave Management System</div>
            <div class="report-title">Laporan Rekapitulasi Cuti Karyawan Tahun {{ $year }}</div>
        </div>
        <div class="header-meta">
            Dicetak: {{ now()->isoFormat('D MMMM YYYY, HH:mm') }}<br>
            Oleh: {{ Auth::user()->name }} (HRD)<br>
            Halaman <span class="page"></span>
        </div>
    </div>
</div>

{{-- Filter info --}}
<div class="filter-bar">
    <strong>Tahun: {{ $year }}</strong>
    <strong>Jenis: {{ $request->leave_type ? ($request->leave_type === 'annual' ? 'Cuti Tahunan' : 'Cuti Sakit') : 'Semua' }}</strong>
    <strong>Status: {{ $request->status ? $request->status : 'Semua' }}</strong>
    <strong>Total Pengajuan: {{ $applications->count() }}</strong>
</div>

{{-- Overview stats --}}
<div class="stats-row">
    <div class="stat-box">
        <div class="val">{{ $applications->count() }}</div>
        <div class="lbl">Total Pengajuan</div>
    </div>
    <div class="stat-box">
        <div class="val" style="color:#059669;">{{ $applications->where('status','hrd_approved')->count() }}</div>
        <div class="lbl">Disetujui</div>
    </div>
    <div class="stat-box">
        <div class="val" style="color:#dc2626;">{{ $applications->whereIn('status',['manager_rejected','hrd_rejected'])->count() }}</div>
        <div class="lbl">Ditolak</div>
    </div>
    <div class="stat-box">
        <div class="val" style="color:#d97706;">{{ $applications->whereIn('status',['pending','manager_approved'])->count() }}</div>
        <div class="lbl">Dalam Proses</div>
    </div>
    <div class="stat-box">
        <div class="val">{{ $applications->where('status','hrd_approved')->sum('total_days') }}</div>
        <div class="lbl">Total Hari Disetujui</div>
    </div>
    <div class="stat-box">
        <div class="val">{{ $applications->where('leave_type','annual')->where('status','hrd_approved')->sum('total_days') }}</div>
        <div class="lbl">Hari Cuti Tahunan</div>
    </div>
    <div class="stat-box">
        <div class="val">{{ $applications->where('leave_type','sick')->where('status','hrd_approved')->sum('total_days') }}</div>
        <div class="lbl">Hari Cuti Sakit</div>
    </div>
</div>

{{-- Summary per karyawan --}}
<div class="section-title">📋 Ringkasan Kuota Cuti Per Karyawan</div>
<table class="summary-table">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Karyawan</th>
            <th>ID</th>
            <th>Jabatan</th>
            <th>Role</th>
            <th class="num">Kuota Tahunan</th>
            <th class="num">Cuti Tahunan Terpakai</th>
            <th class="num">Sisa Cuti</th>
            <th class="num">Cuti Sakit</th>
            <th class="num">Total Pengajuan</th>
            <th class="num">Disetujui</th>
            <th class="num">Ditolak</th>
            <th class="num">Pending</th>
        </tr>
    </thead>
    <tbody>
    @foreach($summary as $i => $row)
    <tr>
        <td>{{ $i+1 }}</td>
        <td class="fw-bold">{{ $row['user']->name }}</td>
        <td>{{ $row['user']->employee_id }}</td>
        <td>{{ $row['user']->position ?? '-' }}</td>
        <td>
            <span class="badge {{ $row['user']->role === 'manager' ? 'badge-warning' : 'badge-secondary' }}">
                {{ ucfirst($row['user']->role) }}
            </span>
        </td>
        <td class="num">{{ $row['annual_quota'] }}</td>
        <td class="num">{{ $row['annual_used'] }}</td>
        <td class="num fw-bold {{ $row['annual_remain'] <= 3 ? 'text-danger' : 'text-success' }}">{{ $row['annual_remain'] }}</td>
        <td class="num">{{ $row['sick_used'] }}</td>
        <td class="num">{{ $row['total_leaves'] }}</td>
        <td class="num text-success fw-bold">{{ $row['approved'] }}</td>
        <td class="num text-danger">{{ $row['rejected'] }}</td>
        <td class="num text-warning">{{ $row['pending'] }}</td>
    </tr>
    @endforeach
    @if($summary->isEmpty())
    <tr><td colspan="13" style="text-align:center;color:#94a3b8;padding:16px;">Tidak ada data karyawan.</td></tr>
    @endif
    </tbody>
</table>

{{-- Detail per pengajuan --}}
<div class="section-title">📄 Detail Seluruh Pengajuan Cuti</div>
<table class="detail-table">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama</th>
            <th>ID</th>
            <th>Role</th>
            <th>Jenis</th>
            <th>Tgl Mulai</th>
            <th>Tgl Selesai</th>
            <th>Hari</th>
            <th>Alasan</th>
            <th>Status</th>
            <th>Manager</th>
            <th>Tgl Diajukan</th>
            <th>HRD</th>
            <th>Tgl Disetujui</th>
        </tr>
    </thead>
    <tbody>
    @forelse($applications as $i => $app)
    <tr>
        <td>{{ $i+1 }}</td>
        <td class="fw-bold">{{ $app->user->name }}</td>
        <td>{{ $app->user->employee_id }}</td>
        <td>
            <span class="badge {{ $app->user->role==='manager' ? 'badge-warning' : 'badge-secondary' }}">
                {{ ucfirst($app->user->role) }}
            </span>
        </td>
        <td>
            <span class="badge {{ $app->leave_type==='annual' ? 'badge-primary' : 'badge-info' }}">
                {{ $app->leave_type_label }}
            </span>
        </td>
        <td>{{ $app->start_date->format('d/m/Y') }}</td>
        <td>{{ $app->end_date->format('d/m/Y') }}</td>
        <td style="text-align:center;font-weight:700;">{{ $app->total_days }}</td>
        <td style="max-width:120px;">{{ Str::limit($app->reason, 60) }}</td>
        <td>
            @php
                $bc = match($app->status) {
                    'hrd_approved'     => 'badge-success',
                    'hrd_rejected',
                    'manager_rejected' => 'badge-danger',
                    'manager_approved' => 'badge-info',
                    default            => 'badge-warning',
                };
            @endphp
            <span class="badge {{ $bc }}">{{ $app->status_label }}</span>
        </td>
        <td>{{ $app->managerApprover?->name ?? ($app->user->role === 'manager' ? '(HRD langsung)' : '-') }}</td>
        <td>{{ $app->created_at->format('d/m/Y') }}</td>
        <td>{{ $app->hrdApprover?->name ?? '-' }}</td>
        <td>{{ $app->hrd_approved_at?->format('d/m/Y') ?? '-' }}</td>
    </tr>
    @empty
    <tr><td colspan="14" style="text-align:center;color:#94a3b8;padding:16px;">Tidak ada pengajuan cuti dalam periode ini.</td></tr>
    @endforelse
    </tbody>
</table>

{{-- Footer --}}
<div class="footer">
    <div>Leave Management System — Laporan Cuti Tahun {{ $year }} — Dicetak {{ now()->isoFormat('D MMMM YYYY') }}</div>
    <div>Dokumen ini dibuat secara otomatis oleh sistem. TTD HRD: _________________________</div>
</div>

</body>
</html>
