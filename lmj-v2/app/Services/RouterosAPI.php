<?php

namespace App\Services;

/**
 * RouterOS API Class v1.6
 *
 * This class is a modified version of the original MikroTik PHP API
 * updated for PHP 8.2+ compatibility (including PHP 8.5).
 */
class RouterosAPI
{
    public $debug = false;
    public $connected = false;
    public $port = 8728;
    public $timeout = 3;
    public $attempts = 5;
    public $delay = 3;

    protected $socket;
    protected $error_no;
    protected $error_str;

    /**
     * Connect to RouterOS
     *
     * @param string $host
     * @param string $username
     * @param string $password
     * @return bool
     */
    public function connect(string $host, string $username, string $password): bool
    {
        for ($attempt = 1; $attempt <= $this->attempts; $attempt++) {
            $this->socket = @fsockopen($host, $this->port, $this->error_no, $this->error_str, $this->timeout);
            
            if ($this->socket) {
                socket_set_timeout($this->socket, $this->timeout);
                
                if ($this->login($username, $password)) {
                    $this->connected = true;
                    return true;
                }
                
                fclose($this->socket);
            }
            
            if ($attempt < $this->attempts) {
                sleep($this->delay);
            }
        }
        
        return false;
    }

    /**
     * Disconnect from RouterOS
     */
    public function disconnect(): void
    {
        if (is_resource($this->socket)) {
            fclose($this->socket);
        }
        $this->connected = false;
    }

    /**
     * Login to RouterOS (Support for v6.43+ login method)
     *
     * @param string $username
     * @param string $password
     * @return bool
     */
    protected function login(string $username, string $password): bool
    {
        $this->write('/login', false);
        $this->write('=name=' . $username, false);
        $this->write('=password=' . $password);
        
        $response = $this->read(false);
        
        if (isset($response[0]) && $response[0] === '!done') {
            if (isset($response[1]) && str_starts_with($response[1], '=ret=')) {
                // Old login method (v6.42 and below)
                $salt = hex2bin(substr($response[1], 5));
                $hash = md5(chr(0) . $password . $salt);
                
                $this->write('/login', false);
                $this->write('=name=' . $username, false);
                $this->write('=response=00' . bin2hex($hash));
                
                $response = $this->read(false);
                if (isset($response[0]) && $response[0] === '!done') {
                    return true;
                }
            } else {
                // New login method (v6.43+)
                return true;
            }
        }
        
        return false;
    }

    /**
     * Write command to RouterOS
     *
     * @param string $command
     * @param bool $last
     * @return int|bool
     */
    public function write(string $command, bool $last = true): int|bool
    {
        if (empty($command)) return false;
        
        $data = explode("\n", $command);
        foreach ($data as $com) {
            $com = trim($com);
            $this->encodeLength(strlen($com));
            fwrite($this->socket, $com);
        }
        
        if ($last) {
            fwrite($this->socket, chr(0));
        }
        
        return true;
    }

    /**
     * Read response from RouterOS
     *
     * @param bool $parse
     * @return array
     */
    public function read(bool $parse = true): array
    {
        $responses = [];
        $done = false;
        
        while (!$done) {
            while (true) {
                $length = $this->decodeLength();
                if ($length === 0) {
                    break;
                }
                
                $response = "";
                $left = $length;
                while ($left > 0) {
                    $read = fread($this->socket, $left);
                    if ($read === false || $read === "") break;
                    $response .= $read;
                    $left -= strlen($read);
                }
                
                $responses[] = $response;
                
                if ($response === '!done' || $response === '!trap' || $response === '!fatal') {
                    $done = true;
                }
            }
            
            if ($done) break;
            if (feof($this->socket)) break;
        }
        
        return $parse ? $this->parseResponse($responses) : $responses;
    }

    /**
     * Parse RouterOS response into associative array
     *
     * @param array $responses
     * @return array
     */
    protected function parseResponse(array $responses): array
    {
        $result = [];
        $entry = [];
        
        foreach ($responses as $response) {
            if ($response === '!re') {
                if (!empty($entry)) {
                    $result[] = $entry;
                }
                $entry = [];
            } elseif (str_starts_with($response, '=')) {
                $parts = explode('=', $response, 3);
                if (count($parts) >= 3) {
                    $entry[$parts[1]] = $parts[2];
                }
            } elseif ($response === '!done' || $response === '!trap' || $response === '!fatal') {
                if (!empty($entry)) {
                    $result[] = $entry;
                    $entry = [];
                }
            }
        }
        
        return $result;
    }

    /**
     * Encode length for RouterOS API
     *
     * @param int $length
     */
    protected function encodeLength(int $length): void
    {
        if ($length < 0x80) {
            fwrite($this->socket, chr($length));
        } elseif ($length < 0x4000) {
            fwrite($this->socket, chr(($length >> 8) | 0x80));
            fwrite($this->socket, chr($length & 0xFF));
        } elseif ($length < 0x200000) {
            fwrite($this->socket, chr(($length >> 16) | 0xC0));
            fwrite($this->socket, chr(($length >> 8) & 0xFF));
            fwrite($this->socket, chr($length & 0xFF));
        } elseif ($length < 0x10000000) {
            fwrite($this->socket, chr(($length >> 24) | 0xE0));
            fwrite($this->socket, chr(($length >> 16) & 0xFF));
            fwrite($this->socket, chr(($length >> 8) & 0xFF));
            fwrite($this->socket, chr($length & 0xFF));
        } else {
            fwrite($this->socket, chr(0xF0));
            fwrite($this->socket, chr(($length >> 24) & 0xFF));
            fwrite($this->socket, chr(($length >> 16) & 0xFF));
            fwrite($this->socket, chr(($length >> 8) & 0xFF));
            fwrite($this->socket, chr($length & 0xFF));
        }
    }

    /**
     * Decode length from RouterOS API
     *
     * @return int
     */
    protected function decodeLength(): int
    {
        $byte = $this->readByte();
        if ($byte === null) return 0;
        
        if (($byte & 0x80) === 0x00) {
            return $byte;
        } elseif (($byte & 0xC0) === 0x80) {
            $byte &= ~0xC0;
            $byte2 = $this->readByte();
            return ($byte << 8) + ($byte2 ?? 0);
        } elseif (($byte & 0xE0) === 0xC0) {
            $byte &= ~0xE0;
            $byte2 = $this->readByte();
            $byte3 = $this->readByte();
            return ($byte << 16) + (($byte2 ?? 0) << 8) + ($byte3 ?? 0);
        } elseif (($byte & 0xF0) === 0xE0) {
            $byte &= ~0xF0;
            $byte2 = $this->readByte();
            $byte3 = $this->readByte();
            $byte4 = $this->readByte();
            return ($byte << 24) + (($byte2 ?? 0) << 16) + (($byte3 ?? 0) << 8) + ($byte4 ?? 0);
        } elseif (($byte & 0xF8) === 0xF0) {
            $byte2 = $this->readByte();
            $byte3 = $this->readByte();
            $byte4 = $this->readByte();
            $byte5 = $this->readByte();
            return (($byte2 ?? 0) << 24) + (($byte3 ?? 0) << 16) + (($byte4 ?? 0) << 8) + ($byte5 ?? 0);
        }
        
        return 0;
    }

    /**
     * Read a single byte safely
     *
     * @return int|null
     */
    protected function readByte(): ?int
    {
        $byte = fread($this->socket, 1);
        if ($byte === false || $byte === "") return null;
        return ord($byte);
    }

    /**
     * Execute a command and return results
     *
     * @param string $command
     * @param array $params
     * @return array
     */
    public function comm(string $command, array $params = []): array
    {
        $this->write($command, false);
        foreach ($params as $key => $value) {
            if (is_numeric($key)) {
                $this->write($value, false);
            } else {
                $this->write('=' . $key . '=' . $value, false);
            }
        }
        fwrite($this->socket, chr(0));
        
        return $this->read();
    }
}
