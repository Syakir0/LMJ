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
            --primary-color: #1e293b;
            --secondary-color: #334155;
            --accent-color: #3b82f6;
            --sidebar-width: 260px;
            --navbar-height: 70px;
            --bg-body: #f1f5f9;
        }

        body {
            font-family: 'Inter', 'Segoe UI', sans-serif;
            margin: 0;
            background-color: var(--bg-body);
            color: #1e293b;
            display: flex;
        }

        /* Sidebar - Fixed Left */
        .sidebar {
            width: var(--sidebar-width);
            background-color: var(--primary-color);
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            color: white;
            display: flex;
            flex-direction: column;
            z-index: 1100;
            box-shadow: 4px 0 10px rgba(0,0,0,0.1);
        }

        .sidebar-header {
            height: var(--navbar-height);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            font-weight: 800;
            letter-spacing: 1px;
            background-color: #0f172a;
            border-bottom: 1px solid #334155;
            gap: 10px;
        }

        .sidebar-menu {
            flex: 1;
            padding: 20px 0;
            overflow-y: auto;
        }

        .menu-item {
            padding: 14px 25px;
            display: flex;
            align-items: center;
            color: #94a3b8;
            text-decoration: none;
            font-size: 0.95rem;
            font-weight: 500;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-left: 4px solid transparent;
        }

        .menu-item:hover {
            color: white;
            background-color: rgba(255,255,255,0.05);
        }

        .menu-item.active {
            color: white;
            background-color: rgba(59, 130, 246, 0.1);
            border-left-color: var(--accent-color);
        }

        .menu-item i {
            margin-right: 15px;
            font-size: 1.1rem;
            width: 24px;
            text-align: center;
        }

        /* Main Content Area */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            width: calc(100% - var(--sidebar-width));
        }

        /* Fixed Top Navbar */
        .top-navbar {
            height: var(--navbar-height);
            background: white;
            position: sticky;
            top: 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            z-index: 1000;
        }

        .breadcrumb-container {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #64748b;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .user-nav {
            display: flex;
            align-items: center;
            gap: 25px;
        }

        .profile-link {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: #1e293b;
            font-weight: 600;
            font-size: 0.95rem;
            padding: 8px 12px;
            border-radius: 8px;
            transition: background 0.2s;
        }

        .profile-link:hover {
            background: #f1f5f9;
        }

        .btn-logout {
            background: #fee2e2;
            color: #dc2626;
            padding: 8px 15px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 700;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .btn-logout:hover {
            background: #fecaca;
        }

        .content-body {
            padding: 30px;
            flex: 1;
        }

        /* Standard Card Overwrite for Consistency */
        .card {
            background: white;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            padding: 24px;
            margin-bottom: 24px;
        }

        @yield('styles')
    </style>
    </head>
    <body>
    <div class="sidebar">
        <div class="sidebar-header">
            <i class="fas fa-shield-virus" style="color: var(--accent-color);"></i>
            <span>LMJ-ISP</span>
        </div>
        <div class="sidebar-menu">
            <a href="{{ route('dashboard') }}" class="menu-item {{ Request::is('dashboard') ? 'active' : '' }}">
                <i class="fas fa-grid-2"></i> Dashboard
            </a>
            <a href="{{ route('customers.index') }}" class="menu-item {{ Request::is('customers*') ? 'active' : '' }}">
                <i class="fas fa-user-group"></i> Pelanggan
            </a>
            <a href="{{ route('invoices.index') }}" class="menu-item {{ Request::is('invoices*') ? 'active' : '' }}">
                <i class="fas fa-file-invoice-dollar"></i> Tagihan & Pembayaran
            </a>
            <a href="{{ route('packages.index') }}" class="menu-item {{ Request::is('packages*') ? 'active' : '' }}">
                <i class="fas fa-layer-group"></i> Paket Layanan
            </a>
            <a href="{{ route('devices.index') }}" class="menu-item {{ Request::is('devices*') ? 'active' : '' }}">
                <i class="fas fa-server"></i> Network Devices
            </a>
            <a href="{{ route('alerts.index') }}" class="menu-item {{ Request::is('alerts*') ? 'active' : '' }}">
                <i class="fas fa-circle-exclamation"></i> Alerts & Log
            </a>
        </div>
        <div style="padding: 20px; font-size: 0.75rem; color: #475569; border-top: 1px solid #334155; text-align: center;">
            LMJ-V2 v1.1.0 &copy; 2026
        </div>
    </div>

    <div class="main-wrapper">
        <div class="top-navbar">
            <div class="breadcrumb-container">
                <i class="fas fa-house-chimney" style="font-size: 0.8rem;"></i>
                <span>/</span>
                <span>@yield('breadcrumb', 'Dashboard')</span>
            </div>

            <div class="user-nav">
                <a href="{{ route('profile.edit') }}" class="profile-link">
                    <div style="background: #e2e8f0; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-user" style="color: #64748b; font-size: 0.9rem;"></i>
                    </div>
                    <span>{{ Auth::user()->name }}</span>
                </a>

                <form method="POST" action="{{ route('logout') }}" id="logout-form" style="margin: 0;">
                    @csrf
                    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="btn-logout">
                        <i class="fas fa-power-off"></i> Keluar
                    </a>
                </form>
            </div>
        </div>

        <div class="content-body">
            @yield('content')
        </div>
    </div>
    <!-- Notification Popup -->
    <div id="web-notifier" style="position: fixed; bottom: 20px; right: 20px; z-index: 9999; display: flex; flex-direction: column; gap: 10px;"></div>
    
    <audio id="notif-sound" src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3" preload="auto"></audio>

    <script>
        let lastNotifId = {{ \App\Models\Alert::max('id') ?? 0 }};
        console.log('Notification system initialized. Last ID:', lastNotifId);

        function checkWebNotifications() {
            fetch('{{ route('alerts.latest') }}?last_id=' + lastNotifId)
                .then(response => response.json())
                .then(alerts => {
                    if (alerts.length > 0) {
                        const sound = document.getElementById('notif-sound');
                        
                        alerts.forEach(alert => {
                            console.log('New notification received:', alert.title);
                            showToast(alert);
                            if (alert.id > lastNotifId) {
                                lastNotifId = alert.id;
                            }
                        });
                        
                        // Play sound once if there are new alerts
                        if(sound) sound.play().catch(e => console.log('Autoplay blocked'));
                    }
                })
                .catch(err => console.error('Notification error:', err));
        }

        function showToast(alert) {
            const container = document.getElementById('web-notifier');
            const toast = document.createElement('div');
            const color = alert.level === 'critical' ? '#e74c3c' : '#3498db';
            const icon = alert.level === 'critical' ? 'fa-exclamation-triangle' : 'fa-check-circle';
            
            toast.style.cssText = `
                background: white;
                border-left: 6px solid ${color};
                padding: 18px 25px;
                border-radius: 10px;
                box-shadow: 0 10px 30px rgba(0,0,0,0.2);
                min-width: 320px;
                margin-top: 10px;
                opacity: 0;
                transform: translateY(20px);
                transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
                display: flex;
                align-items: center;
                gap: 15px;
            `;

            toast.innerHTML = `
                <div style="background: ${color}; color: white; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <i class="fas ${icon}" style="font-size: 1.2rem;"></i>
                </div>
                <div style="flex: 1;">
                    <div style="font-weight: 800; color: #1a1a1a; font-size: 0.95rem; margin-bottom: 3px;">${alert.title}</div>
                    <div style="font-size: 0.85rem; color: #555; line-height: 1.4;">${alert.message}</div>
                </div>
                <button onclick="this.parentElement.remove()" style="background:none; border:none; color:#ccc; cursor:pointer; font-size:1.2rem;">&times;</button>
            `;

            container.appendChild(toast);
            
            // Trigger Animation
            setTimeout(() => {
                toast.style.opacity = '1';
                toast.style.transform = 'translateY(0)';
            }, 100);
            
            // Auto-remove
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(50px)';
                setTimeout(() => toast.remove(), 500);
            }, 10000);
        }

        setInterval(checkWebNotifications, 3000);
    </script>

    @yield('scripts')
</body>
</html>
