<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 – Akses Ditolak</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f1f5f9; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        .error-card { background: #fff; border-radius: 16px; padding: 3rem 2.5rem; text-align: center; max-width: 480px; box-shadow: 0 4px 20px rgba(0,0,0,.08); }
        .error-icon { font-size: 4rem; color: #ef4444; margin-bottom: 1rem; }
        .error-code { font-size: 5rem; font-weight: 800; color: #1e293b; line-height: 1; }
    </style>
</head>
<body>
<div class="error-card">
    <div class="error-icon"><i class="bi bi-shield-x"></i></div>
    <div class="error-code">403</div>
    <h2 class="mt-2 mb-2" style="font-size:1.4rem;">Akses Ditolak</h2>
    <p class="text-muted mb-4">Anda tidak memiliki izin untuk mengakses halaman ini.</p>
    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary me-2">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
    <a href="{{ url('/') }}" class="btn btn-primary">
        <i class="bi bi-house me-1"></i>Dashboard
    </a>
</div>
</body>
</html>
