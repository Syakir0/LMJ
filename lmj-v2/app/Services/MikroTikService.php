<?php

namespace App\Services;

use App\Services\RouterosAPI;
use Exception;
use Illuminate\Support\Facades\Log;

class MikroTikService
{
    protected $api;

    public function __construct()
    {
        $this->api = new RouterosAPI();
    }

    public function connect()
    {
        $host = config('services.mikrotik.host');
        $user = config('services.mikrotik.user');
        $pass = config('services.mikrotik.pass');
        $port = (int) config('services.mikrotik.port', 8728);

        try {
            if ($this->api->connect($host, $user, $pass)) {
                return true;
            }
            throw new Exception("Koneksi API Gagal.");
        } catch (Exception $e) {
            Log::error("MikroTik Connection Error: " . $e->getMessage());
            return false;
        }
    }

    public function getSystemResource()
    {
        try {
            if (!$this->connect()) {
                throw new Exception("Gagal terhubung ke MikroTik API.");
            }
            
            $resource = $this->api->comm('/system/resource/print');
            $this->api->disconnect();
            
            return $resource[0] ?? [];
        } catch (Exception $e) {
            Log::error("MikroTik getSystemResource Error: " . $e->getMessage());
            return [];
        }
    }

    public function getInterfaces()
    {
        try {
            if (!$this->connect()) {
                throw new Exception("Gagal terhubung ke MikroTik API.");
            }
            
            $interfaces = $this->api->comm('/interface/print');
            $this->api->disconnect();
            
            return is_array($interfaces) ? $interfaces : [];
        } catch (Exception $e) {
            Log::error("MikroTik getInterfaces Error: " . $e->getMessage());
            return [];
        }
    }

    public function getActivePppoe()
    {
        try {
            if (!$this->connect()) {
                throw new Exception("Gagal terhubung ke MikroTik API.");
            }
            
            $sessions = $this->api->comm('/ppp/active/print');
            $this->api->disconnect();
            
            return is_array($sessions) ? $sessions : [];
        } catch (Exception $e) {
            Log::error("MikroTik getActivePppoe Error: " . $e->getMessage());
            return [];
        }
    }

    public function disconnectUser($id)
    {
        try {
            if (!$this->connect()) {
                throw new Exception("Gagal terhubung ke MikroTik API.");
            }
            
            $result = $this->api->comm('/ppp/active/remove', [
                '.id' => $id
            ]);
            $this->api->disconnect();
            
            return true;
        } catch (Exception $e) {
            Log::error("MikroTik disconnectUser Error: " . $e->getMessage());
            return false;
        }
    }

    public function suspendUser($username)
    {
        try {
            if (!$this->connect()) return false;
            
            // 1. Disable the secret
            $secrets = $this->api->comm('/ppp/secret/print', ['?name' => $username]);
            if (!empty($secrets)) {
                $this->api->comm('/ppp/secret/set', [
                    '.id' => $secrets[0]['.id'],
                    'disabled' => 'yes'
                ]);
            }

            // 2. Kick from active sessions
            $active = $this->api->comm('/ppp/active/print', ['?name' => $username]);
            if (!empty($active)) {
                $this->api->comm('/ppp/active/remove', ['.id' => $active[0]['.id']]);
            }

            $this->api->disconnect();
            return true;
        } catch (Exception $e) {
            Log::error("MikroTik suspendUser Error: " . $e->getMessage());
            return false;
        }
    }

    public function reactivateUser($username)
    {
        try {
            if (!$this->connect()) return false;
            
            // Enable the secret
            $secrets = $this->api->comm('/ppp/secret/print', ['?name' => $username]);
            if (!empty($secrets)) {
                $this->api->comm('/ppp/secret/set', [
                    '.id' => $secrets[0]['.id'],
                    'disabled' => 'no'
                ]);
            }

            $this->api->disconnect();
            return true;
        } catch (Exception $e) {
            Log::error("MikroTik reactivateUser Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Cross-platform Ping Monitoring
     */
    public function ping($ip)
    {
        $os = PHP_OS_FAMILY;
        $cmd = $os === 'Windows' 
            ? "ping -n 1 -w 1000 {$ip}" 
            : "fping -c 1 -t 1000 {$ip}";
        
        exec($cmd, $output, $resultCode);
        
        return $resultCode === 0;
    }
}
