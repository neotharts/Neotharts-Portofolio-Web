<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Dashboard | Neotharts</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    @vite('resources/css/admin.css')
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
    @yield('scripts')
</body>
</html>
