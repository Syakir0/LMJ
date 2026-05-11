# LMJ V3 - Layanan Manajemen Jaringan (Malacca Net)

**LMJ V3** adalah sistem manajemen ISP skala mikro yang mengintegrasikan **MikroTik**, **FreeRADIUS**, dan **MariaDB** dengan Dashboard berbasis **Laravel 11**. Proyek ini dirancang untuk memfasilitasi autentikasi terpusat, billing otomatis, dan monitoring real-time pelanggan PPPoE.

## 🚀 Fitur Utama
- **Autentikasi Terpusat**: Menggunakan protokol AAA (Radius) untuk validasi user PPPoE.
- **Dynamic Billing**: Pencatatan otomatis durasi dan pemakaian data ke database MariaDB.
- **Real-time Monitoring**: Integrasi API MikroTik untuk memantau status perangkat dan trafik.
- **Manajemen Bandwidth**: Limitasi kecepatan otomatis (Queues) melalui atribut Radius.

## 🛠️ Stack Teknologi
| Komponen | Teknologi |
| :--- | :--- |
| **Router OS** | MikroTik RB951Ui-2nD |
| **Server** | Debian 13 (Trixie) |
| **Database** | MariaDB 11.8 |
| **Radius Server** | FreeRADIUS 3.0 |
| **Backend/Web** | Laravel 11 |
| **Frontend** | Tailwind CSS & Livewire |

## 📐 Topologi & Konfigurasi
- **WAN Interface**: DHCP Client / Static IP pada `ether1`.
- **PPPoE Server**: Berjalan pada `bridge-pppoe`.
- **AAA System**: MikroTik diarahkan ke Radius Server (Debian) pada port 1812/1813.
- **Database Schema**: Menggunakan tabel standar `radcheck` (auth) dan `radacct` (accounting).

## ⚙️ Langkah Instalasi Cepat

### 1. Konfigurasi MikroTik
```bash
/interface pppoe-server server add interface=bridge-pppoe service-name=service-pppoe
/radius add address=192.168.10.2 secret=testing123 service=ppp
/ppp aaa set use-radius=yes