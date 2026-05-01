<?php

namespace App\Services;

use MikrotikAPI\Talker;
use MikrotikAPI\Entity\RouterOS;
use Exception;

class MikroTikService
{
    protected $talker;
    protected $router;

    public function connect()
    {
        $host = env('MIKROTIK_HOST');
        $user = env('MIKROTIK_USERNAME');
        $pass = env('MIKROTIK_PASSWORD');
        $port = env('MIKROTIK_PORT', 8728);

        try {
            $this->talker = new Talker($host, $port);
            $this->router = new RouterOS($this->talker);
            
            if ($this->router->login($user, $pass)) {
                return true;
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public function getActivePppoe()
    {
        try {
            if (!$this->connect()) {
                throw new Exception("Gagal terhubung ke MikroTik API. Periksa IP, Username, atau Password.");
            }
            return $this->router->send("/ppp/active/print");
        } catch (Exception $e) {
            Log::error("MikroTik getActivePppoe Error: " . $e->getMessage());
            return [];
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
