<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <meta name="googlebot" content="noindex, nofollow">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Dashboard | Neotharts</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    @stack('styles')
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
                <a href="{{ route('admin.messages.index') }}" class="nav-item {{ request()->routeIs('admin.messages.*') ? 'active' : '' }}">
                    <span class="material-icons-outlined">mail</span>
                    <span>Messages</span>
                </a>
                <a href="{{ route('admin.invoices.index') }}" class="nav-item {{ request()->routeIs('admin.invoices.*') ? 'active' : '' }}">
                    <span class="material-icons-outlined">receipt_long</span>
                    <span>Invoice</span>
                </a>
                <a href="{{ route('admin.tos.edit') }}" class="nav-item {{ request()->routeIs('admin.tos.*') ? 'active' : '' }}">
                    <span class="material-icons-outlined">description</span>
                    <span>TOS</span>
                </a>
                <a href="{{ route('admin.profile.edit') }}" class="nav-item {{ request()->routeIs('admin.profile.*') ? 'active' : '' }}">
                    <span class="material-icons-outlined">manage_accounts</span>
                    <span>Profil</span>
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
                @if(session('success'))
                    <div class="alert alert-success" style="margin: 1rem; padding: 1rem; background: #dcfce7; color: #166534; border-radius: 10px; border-left: 4px solid #22c55e;">
                        <strong>Berhasil!</strong> {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-error" style="margin: 1rem; padding: 1rem; background: #fee2e2; color: #dc2626; border-radius: 10px; border-left: 4px solid #ef4444;">
                        <strong>Gagal!</strong> {{ session('error') }}
                    </div>
                @endif
                @yield('content')
            </section>
        </main>
    </div>
    @stack('scripts')
</body>
</html>
