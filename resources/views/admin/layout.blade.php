<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <meta name="googlebot" content="noindex, nofollow">
    <meta http-equiv="X-Content-Type-Options" content="nosniff">
    <meta http-equiv="X-Frame-Options" content="DENY">
    <meta http-equiv="X-XSS-Protection" content="1; mode=block">
    <meta http-equiv="Referrer-Policy" content="strict-origin-when-cross-origin">
    <meta http-equiv="Content-Security-Policy" content="default-src 'self'; script-src 'self' 'unsafe-inline' https://unpkg.com https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; img-src 'self' data: https://images.unsplash.com https://storage.googleapis.com; connect-src 'self';">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Dashboard | Neotharts</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <style>
        /* Prevent autocomplete styling for security */
        input:-webkit-autofill,
        input:-webkit-autofill:hover,
        input:-webkit-autofill:focus {
            -webkit-box-shadow: 0 0 0 1000px #1e1e2e inset !important;
            -webkit-text-fill-color: #fff !important;
        }
    </style>
</head>
<body>
    <div class="admin-shell">
        <aside class="sidebar">
            <div class="brand">
                <div class="brand-mark">N</div>
                <div>
                    <p class="brand-label">Neotharts</p>
                    <span class="brand-subtitle">Admin Panel</span>
                </div>
            </div>

            <nav class="sidebar-nav">
                <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <span class="material-icons-outlined">dashboard</span>
                    <span>Beranda</span>
                </a>
                <a href="{{ route('admin.artworks.index') }}" class="nav-item {{ request()->routeIs('admin.artworks.*') ? 'active' : '' }}">
                    <span class="material-icons-outlined">palette</span>
                    <span>Artwork</span>
                </a>
                <a href="{{ route('admin.artworks.create') }}" class="nav-item {{ request()->routeIs('admin.artworks.create') ? 'active' : '' }}">
                    <span class="material-icons-outlined">add_circle_outline</span>
                    <span>Tambah Artwork</span>
                </a>
                <a href="{{ route('admin.services.index') }}" class="nav-item {{ request()->routeIs('admin.services.*') ? 'active' : '' }}">
                    <span class="material-icons-outlined">miscellaneous_services</span>
                    <span>Services</span>
                </a>
                <a href="{{ route('admin.services.create') }}" class="nav-item {{ request()->routeIs('admin.services.create') ? 'active' : '' }}">
                    <span class="material-icons-outlined">add_circle_outline</span>
                    <span>Tambah Service</span>
                </a>
            </nav>

            <div class="sidebar-footer">
                <form action="{{ route('logout') }}" method="POST" style="width: 100%;">
                    @csrf
                    <button type="submit" class="nav-item logout-btn">
                        <span class="material-icons-outlined">logout</span>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        <main class="main-content">
            <header class="topbar">
                <div>
                    <p class="eyebrow">Admin Dashboard</p>
                    <h1>@yield('pageTitle', 'Overview')</h1>
                </div>
                <div class="profile-card">
                    <div class="profile-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                    <div>
                        <p class="profile-name">{{ auth()->user()->name }}</p>
                        <span class="profile-role">Admin</span>
                    </div>
                </div>
            </header>

            <section class="page-content">
                @yield('content')
            </section>
        </main>
    </div>
</body>
</html>
