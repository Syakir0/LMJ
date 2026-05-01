<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') | {{ config('app.name') }}</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #34495e;
            --accent-color: #3498db;
            --success-color: #2ecc71;
            --warning-color: #f1c40f;
            --danger-color: #e74c3c;
            --light-bg: #f8f9fa;
            --sidebar-width: 250px;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            display: flex;
            height: 100vh;
            background-color: var(--light-bg);
        }

        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            background-color: var(--primary-color);
            color: white;
            display: flex;
            flex-direction: column;
            transition: all 0.3s;
        }

        .sidebar-header {
            padding: 20px;
            text-align: center;
            background-color: #1a252f;
            font-size: 1.2rem;
            font-weight: bold;
        }

        .sidebar-menu {
            flex: 1;
            padding: 10px 0;
        }

        .menu-item {
            padding: 12px 20px;
            display: flex;
            align-items: center;
            color: #bdc3c7;
            text-decoration: none;
            transition: 0.2s;
        }

        .menu-item:hover, .menu-item.active {
            background-color: var(--secondary-color);
            color: white;
            border-left: 4px solid var(--accent-color);
        }

        .menu-item i {
            margin-right: 15px;
            width: 20px;
            text-align: center;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }

        .top-navbar {
            background-color: white;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .content-body {
            padding: 25px;
        }

        /* Utilities */
        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            padding: 20px;
            margin-bottom: 20px;
        }

        .btn {
            padding: 8px 16px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            font-weight: 600;
        }

        .btn-primary { background-color: var(--accent-color); color: white; }
        
        @yield('styles')
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <i class="fas fa-microchip"></i> LMJ-ISP CORE
        </div>
        <div class="sidebar-menu">
            <a href="{{ route('dashboard') }}" class="menu-item {{ Request::is('dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <a href="{{ route('customers.index') }}" class="menu-item {{ Request::is('customers*') ? 'active' : '' }}">
                <i class="fas fa-users"></i> Pelanggan
            </a>
            <a href="{{ route('packages.index') }}" class="menu-item {{ Request::is('packages*') ? 'active' : '' }}">
                <i class="fas fa-box"></i> Paket Layanan
            </a>
            <a href="{{ route('devices.index') }}" class="menu-item {{ Request::is('devices*') ? 'active' : '' }}">
                <i class="fas fa-server"></i> Perangkat Jaringan
            </a>
            <a href="{{ route('alerts.index') }}" class="menu-item {{ Request::is('alerts*') ? 'active' : '' }}">
                <i class="fas fa-bell"></i> Alerts & Log
            </a>
        </div>
        <div style="padding: 20px; font-size: 0.8rem; color: #7f8c8d; border-top: 1px solid #34495e;">
            LMJ-V2 v1.0.0
        </div>
    </div>

    <div class="main-content">
        <div class="top-navbar">
            <div class="breadcrumb">
                <span style="color: #7f8c8d;">@yield('breadcrumb', 'Home')</span>
            </div>
            <div class="user-actions" style="display: flex; align-items: center; gap: 20px;">
                <div class="dropdown" style="position: relative;">
                    <a href="{{ route('profile.edit') }}" style="text-decoration: none; color: var(--primary-color); display: flex; align-items: center; gap: 10px; font-weight: 600;">
                        <i class="fas fa-user-circle" style="font-size: 1.5rem;"></i>
                        <span>{{ Auth::user()->name }}</span>
                    </a>
                </div>
                <form method="POST" action="{{ route('logout') }}" id="logout-form" style="margin: 0;">
                    @csrf
                    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" style="color: var(--danger-color); text-decoration: none; font-size: 0.9rem; font-weight: 600;">
                        <i class="fas fa-sign-out-alt"></i> Keluar
                    </a>
                </form>
            </div>
        </div>

        <div class="content-body">
            @yield('content')
        </div>
    </div>

    @yield('scripts')
</body>
</html>
