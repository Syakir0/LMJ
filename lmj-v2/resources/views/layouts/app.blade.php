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
