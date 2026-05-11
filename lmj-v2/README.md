# LMJ-V2 (ISP Management System)

Sistem manajemen pelanggan ISP terpusat dengan monitoring perangkat jaringan dan notifikasi otomatis.

## Arsitektur Sistem (Dual OS)

### 1. Host OS (Windows - Laragon)
- **Fungsi**: Dashboard Laravel 11, Web Server, dan Development Environment.
- **IP Address**: `192.168.10.10` (Adapter Host-Only).
- **Service**: PHP 8.3, Composer, Node.js 20.

### 2. VM (Debian 12 - RADIUS Backend)
- **Fungsi**: Backend RADIUS, Database MariaDB, dan Redis.
- **IP Address**: `192.168.10.2`.
- **Service**: FreeRADIUS, MariaDB, Redis (bind to 192.168.10.2).

### 3. MikroTik RouterOS
- **Fungsi**: PPPoE Server & Management API.
- **IP Address**: `192.168.10.1`.

## Fitur Utama

- **Centralized User Management**: Kelola pelanggan PPPoE melalui dashboard Laravel yang terintegrasi dengan FreeRADIUS.
- **Automated Billing**: Pembuatan tagihan otomatis setiap bulan berdasarkan tanggal tagihan pelanggan.
- **Auto-Isolir (Suspension)**: Internet pelanggan otomatis diputus via MikroTik API jika tagihan melewati tanggal jatuh tempo.
- **Telegram Bot Integration**:
  - Pengiriman notifikasi tagihan otomatis.
  - Alert sistem dan monitoring ke Admin/NOC.
  - **Auto Chat ID Capture**: Pelanggan cukup chat `/start` dan bagikan kontak untuk menghubungkan akun secara otomatis.
- **Network Monitoring**: Monitoring status perangkat (Online/Offline) via ICMP dan SNMP.

## Setup & Instalasi

1. Clone repository.
2. Konfigurasi `.env` (Database, MikroTik API, Telegram Bot Token).
3. Jalankan `composer install` dan `npm install`.
4. Jalankan migrasi: `php artisan migrate`.
5. Set webhook Telegram: `php artisan telegram:set-webhook` (atau via URL `/telegram/webhook`).
6. Jalankan scheduler: `php artisan schedule:run` atau cron job untuk billing otomatis.

## Author
**Principal Network Architect & Senior Full-Stack Engineer**
*Project Magang ISP Malacca Net*
