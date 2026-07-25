<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login – Leave Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #2563eb; }
        * { box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 50%, #0f172a 100%);
            min-height: 100vh; display: flex; align-items: center; justify-content: center;
        }
        .login-wrapper { width: 100%; max-width: 420px; padding: 1rem; }
        .login-card {
            background: #fff; border-radius: 16px; overflow: hidden;
            box-shadow: 0 25px 60px rgba(0,0,0,.35);
        }
        .login-header {
            background: linear-gradient(135deg, #1e40af, #2563eb);
            padding: 2rem; text-align: center; color: #fff;
        }
        .login-header .logo {
            width: 64px; height: 64px; background: rgba(255,255,255,.15);
            border-radius: 16px; display: flex; align-items: center;
            justify-content: center; margin: 0 auto 1rem;
            font-size: 1.75rem; backdrop-filter: blur(4px);
        }
        .login-header h4 { font-weight: 700; margin: 0; font-size: 1.25rem; }
        .login-header p { margin: .25rem 0 0; opacity: .85; font-size: .85rem; }
        .login-body { padding: 2rem; }
        .form-label { font-size: .85rem; font-weight: 500; color: #374151; }
        .form-control {
            border-color: #d1d5db; border-radius: 10px; font-size: .875rem; padding: .6rem .9rem;
        }
        .form-control:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(37,99,235,.12); }
        .input-group-text { border-color: #d1d5db; background: #f9fafb; }
        .btn-login {
            background: linear-gradient(135deg, #1e40af, #2563eb);
            border: none; border-radius: 10px; font-weight: 600;
            padding: .65rem; font-size: .9rem; transition: opacity .2s;
        }
        .btn-login:hover { opacity: .9; }
        .demo-accounts { background: #f8fafc; border-radius: 10px; padding: 1rem; margin-top: 1.25rem; }
        .demo-accounts h6 { font-size: .75rem; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: .05em; margin-bottom: .6rem; }
        .demo-item { display: flex; justify-content: space-between; align-items: center; font-size: .78rem; color: #475569; padding: .2rem 0; }
        .demo-item .role-badge { font-size: .68rem; background: #e2e8f0; border-radius: 4px; padding: 1px 6px; color: #334155; font-weight: 600; }
        .alert { border-radius: 10px; font-size: .85rem; }
    </style>
</head>
<body>
<div class="login-wrapper">
    <div class="login-card">
        <div class="login-header">
            <div class="logo"><i class="bi bi-calendar-check-fill"></i></div>
            <h4>PT Jaya Abadi</h4>
            <p>Sistem Manajemen Cuti Karyawan</p>
        </div>
        <div class="login-body">
            @if($errors->any())
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Alamat Email</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope text-muted"></i></span>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email') }}" placeholder="nama@perusahaan.com" required autofocus>
                    </div>
                    @error('email')
                        <div class="text-danger mt-1" style="font-size:.8rem;"><i class="bi bi-x-circle me-1"></i>{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock text-muted"></i></span>
                        <input type="password" name="password" id="pwdField"
                               class="form-control @error('password') is-invalid @enderror"
                               placeholder="••••••••" required>
                        <button type="button" class="input-group-text bg-white" onclick="togglePwd()">
                            <i class="bi bi-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                    @error('password')
                        <div class="text-danger mt-1" style="font-size:.8rem;"><i class="bi bi-x-circle me-1"></i>{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-4 d-flex align-items-center justify-content-between">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
                        <label class="form-check-label" for="remember" style="font-size:.85rem;">Ingat Saya</label>
                    </div>
                </div>                          
                <button type="submit" class="btn btn-login btn-primary w-100 text-white">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
                </button>
            </form>

             <div class="demo-accounts">
                <h6></h6><i class="bi bi-info-circle me-1"></i>Akun Demo</h6>(semua akun passwordnya: password)
                <div class="demo-item"><span>admin@gmail.com</span><span class="role-badge">Admin</span></div>
                <div class="demo-item"><span>hrd@gmail.com</span><span class="role-badge">HRD</span></div>
                <div class="demo-item"><span>manager@gmail.com</span><span class="role-badge">Manager</span></div>
                <div class="demo-item"><span>faisal@gmail.com</span><span class="role-badge">Employee</span></div>
                <div class="demo-item"><span>fitri@gmail.com</span><span class="role-badge">Employee</span></div>
                <div class="demo-item"><span>ady@gmail.com</span><span class="role-badge">Employee</span></div>
                <div class="demo-item"><span>ina@gmail.com</span><span class="role-badge">Employee</span></div>
            </div>
        </div>
    </div>
    <p class="text-center text-white-50 mt-3" style="font-size:.75rem;">© {{ date('Y') }} Leave Management System. All rights reserved.</p>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function togglePwd() {
    const f = document.getElementById('pwdField');
    const e = document.getElementById('eyeIcon');
    if (f.type === 'password') { f.type = 'text'; e.className = 'bi bi-eye-slash'; }
    else { f.type = 'password'; e.className = 'bi bi-eye'; }
}
</script>
</body>
</html>
