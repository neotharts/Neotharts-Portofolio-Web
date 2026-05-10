<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login Admin | Neotharts</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    @vite('resources/css/admin.css')
</head>
<body class="auth-page">
    <main class="auth-card">
        <div class="auth-brand">
            <div>
                <p class="eyebrow">Admin Login</p>
                <h1>Masuk ke Neotharts Dashboard</h1>
                <p class="muted-text">Gunakan akun admin untuk mengelola artwork, monitor visitor, dan kontrol publikasi.</p>
            </div>
            <div class="auth-logo">
                <span class="material-icons-outlined">lock</span>
            </div>
        </div>

        @if($errors->any())
            <div class="alert alert-error">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('login.submit') }}" method="POST" class="auth-form">
            @csrf

            <div class="form-group">
                <label for="email">Email Admin</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="admin@neotharts.com" required autofocus>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input id="password" type="password" name="password" placeholder="Masukkan kata sandi" required>
            </div>

            <div class="form-group form-checkbox">
                <label>
                    <input type="checkbox" name="remember"> Ingat Saya
                </label>
            </div>

            <button type="submit" class="button button-primary">Login Sekarang</button>
        </form>
    </main>
</body>
</html>
