<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * AMI Status Library
 * Gets Asterisk status information via AMI
 */
class Ami_status {

    private $CI;
    private $ami_host;
    private $ami_port;
    private $ami_username;
    private $ami_password;
    private $cache = null; // Cache for current request

    public function __construct() {
        $this->CI =& get_instance();

        // Load AMI configuration from daemon config
        $daemon_config = require FCPATH . 'ami-daemon/config.php';

        $this->ami_host = $daemon_config['ami']['host'];
        $this->ami_port = $daemon_config['ami']['port'];
        $this->ami_username = $daemon_config['ami']['username'];
        $this->ami_password = $daemon_config['ami']['password'];
    }

    /**
     * Get all status info in ONE connection (fast!)
     * Uses cache to avoid multiple connections in same request
     */
    public function get_all_status() {
        // Return cached result if available
        if ($this->cache !== null) {
            return $this->cache;
        }

        try {
            $socket = $this->connect_ami();
            if (!$socket) {
                $this->cache = [
                    'success' => false,
                    'status' => 'offline',
                    'version' => 'Unknown',
                    'uptime' => 'Unknown',
                    'active_channels' => 0,
                    'channels_list' => []
                ];
                return $this->cache;
            }

            $this->ami_login($socket);

            // Get everything in one connection
            $version = $this->get_asterisk_version($socket);
            $channels_info = $this->get_channels_info($socket);

            fclose($socket);

            $this->cache = [
                'success' => true,
                'status' => 'online',
                'version' => $version,
                'uptime' => 'N/A', // Skip uptime command for speed
                'active_channels' => $channels_info['count'],
                'channels_list' => $channels_info['list']
            ];

            return $this->cache;

        } catch (Exception $e) {
            log_message('error', 'AMI Status error: ' . $e->getMessage());
            $this->cache = [
                'success' => false,
                'status' => 'offline',
                'version' => 'Unknown',
                'uptime' => 'Unknown',
                'active_channels' => 0,
                'channels_list' => []
            ];
            return $this->cache;
        }
    }

    /**
     * Check AMI connection status
     */
    public function get_status() {
        $all = $this->get_all_status();
        return [
            'success' => $all['success'],
            'status' => $all['status'],
            'version' => $all['version'],
            'uptime' => $all['uptime']
        ];
    }

    /**
     * Get channels info (count and list) in one call
     */
    private function get_channels_info($socket) {
        try {
            // Send CoreShowChannels action
            $this->send_action($socket, [
                'Action' => 'CoreShowChannels'
            ]);

            $response = $this->read_response($socket);

            // Parse channel count
            $count = 0;
            if (preg_match('/(\d+)\s+active channels?/i', $response, $matches)) {
                $count = (int)$matches[1];
            }

            // Parse channels list
            $channels = [];
            $lines = explode("\n", $response);

            foreach ($lines as $line) {
                if (preg_match('/^Event:\s+CoreShowChannel/i', $line)) {
                    $channel = [];
                    while ($line = array_shift($lines)) {
                        if (trim($line) === '') break;
                        if (preg_match('/^(\w+):\s*(.+)$/i', $line, $matches)) {
                            $channel[strtolower($matches[1])] = trim($matches[2]);
                        }
                    }
                    if (!empty($channel)) {
                        $channels[] = $channel;
                    }
                }
            }

            return ['count' => $count, 'list' => $channels];

        } catch (Exception $e) {
            return ['count' => 0, 'list' => []];
        }
    }

    /**
     * Get active channels count
     */
    public function get_active_channels_count() {
        $all = $this->get_all_status();
        return $all['active_channels'];
    }

    /**
     * Get active channels list
     */
    public function get_active_channels() {
        $all = $this->get_all_status();
        return $all['channels_list'];
    }

    /**
     * Get Asterisk version
     */
    private function get_asterisk_version($socket) {
        $this->send_action($socket, [
            'Action' => 'Command',
            'Command' => 'core show version'
        ]);

        $response = $this->read_command_response($socket);

        if (preg_match('/Asterisk\s+([\d\.]+)/i', $response, $matches)) {
            return $matches[1];
        }

        return 'Unknown';
    }

    /**
     * Get system uptime
     */
    private function get_system_uptime($socket) {
        $this->send_action($socket, [
            'Action' => 'Command',
            'Command' => 'core show uptime'
        ]);

        $response = $this->read_command_response($socket);

        if (preg_match('/System uptime:\s*(.+)/i', $response, $matches)) {
            return trim($matches[1]);
        }

        return 'Unknown';
    }

    /**
     * Connect to AMI
     */
    private function connect_ami() {
        $socket = @fsockopen($this->ami_host, $this->ami_port, $errno, $errstr, 1);

        if (!$socket) {
            log_message('error', "AMI Status: Failed to connect to AMI: $errstr ($errno)");
            return false;
        }

        stream_set_blocking($socket, true);
        stream_set_timeout($socket, 1);

        // Read welcome message
        $welcome = '';
        $timeout = time() + 1;
        while (time() < $timeout) {
            $line = fgets($socket);
            if ($line === false) break;
            $welcome .= $line;
            if (strpos($welcome, "\r\n\r\n") !== false) break;
        }

        return $socket;
    }

    /**
     * Login to AMI
     */
    private function ami_login($socket) {
        $this->send_action($socket, [
            'Action' => 'Login',
            'Username' => $this->ami_username,
            'Secret' => $this->ami_password
        ]);

        $response = '';
        $timeout = time() + 1;
        while (time() < $timeout) {
            $line = fgets($socket);
            if ($line === false) break;
            $response .= $line;
            if (strpos($response, "\r\n\r\n") !== false) break;
        }

        if (stripos($response, 'Success') === false) {
            throw new Exception("AMI login failed");
        }
    }

    /**
     * Send AMI action
     */
    private function send_action($socket, $params) {
        $message = '';
        foreach ($params as $key => $value) {
            $message .= "$key: $value\r\n";
        }
        $message .= "\r\n";

        fwrite($socket, $message);
    }

    /**
     * Read standard response
     */
    private function read_response($socket) {
        $response = '';
        $timeout = time() + 1;

        while (time() < $timeout) {
            $line = fgets($socket);
            if ($line === false) {
                usleep(1000);
                continue;
            }

            $response .= $line;

            if (strpos($response, "\r\n\r\n") !== false) {
                break;
            }
        }

        return $response;
    }

    /**
     * Read command response (waits for multiple empty lines)
     */
    private function read_command_response($socket) {
        $response = '';
        $timeout = time() + 1;
        $emptyLines = 0;

        while (time() < $timeout) {
            $line = fgets($socket);
            if ($line === false) {
                usleep(1000);
                continue;
            }

            $response .= $line;

            if (trim($line) === '') {
                $emptyLines++;
                if ($emptyLines >= 2) {
                    break;
                }
            } else {
                $emptyLines = 0;
            }
        }

        return $response;
    }
}
