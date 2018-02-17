<?php

namespace App\Services\Network;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

trait ReadInterfaces
{
    /**
     * Available properties from nmcli command.
     *
     * @var array
     */
    protected $requiredValues = [
        'IP4.DNS[1]' => 'dns',
        'IP4.ADDRESS[1]' => 'ip',
        'IP4.GATEWAY' => 'gateway',
        'GENERAL.TYPE' => 'conf',
        'GENERAL.STATE' => 'state',
        'GENERAL.HWADDR' => 'mac',
        'GENERAL.CONNECTION' => 'connection',
    ];

    /**
     * Get the name of all interfaces from the system.
     *
     * @return string
     */
    protected function readDevices()
    {
        $output = shell_exec("nmcli device status");
        Log::info($output);
        $output = explode(PHP_EOL, $output);
        $interfaces = [];
        foreach ($output as $key => $line) {
            if ($this->isValidInterface($line)) {
                $out = $this->cleanInterfaceOutput($line);
                $interfaces[] = $out[0];
            }
        }
        return $interfaces;
    }

    /**
     * Check if the line is a interface information.
     *
     * @param $line
     * @return bool
     */
    protected function isValidInterface($line)
    {
        return !empty($line) && !str_contains($line, 'DEVICE') && !str_contains($line, 'loopback') && !str_contains($line, 'wlp');
    }

    /**
     * Clean the line output for the interface.
     *
     * @param $line
     * @return array
     */
    protected function cleanInterfaceOutput($line)
    {
        $out = explode('  ', $line);
        $new = [];
        foreach ($out as $i => $x) {
            if (!empty($x)) {
                $new[] = $out[$i];
            }
        }
        return $new;
    }

    /**
     * Return the type of interface.
     *
     * @return string
     */
    protected function type()
    {
        $output = is_local_envorioment() ? 'GENERAL.TYPE:                           ethernet' . PHP_EOL : shell_exec('nmcli device show ' . $this->device . ' | grep GENERAL.TYPE');
        $output = explode(':', $output);
        if (isset($output[1])) return trim($output[1]);
        return '';
    }

    /**
     * Return the gateway.
     *
     * @return string
     */
    protected function gateway()
    {
        $output = is_local_envorioment() ? 'IP4.GATEWAY:                            192.11.88.1' . PHP_EOL : shell_exec('nmcli device show ' . $this->device . ' | grep IP4.GATEWAY');
        $output = explode(':', $output);
        if (isset($output[1])) return trim($output[1]);
        return '';
    }

    /**
     * State
     * @return mixed
     */
    protected function state()
    {
        $output = is_local_envorioment() ? 'GENERAL.STATE:                          100 (connected)' . PHP_EOL : shell_exec('nmcli device show ' . $this->device . ' | grep GENERAL.STATE');
        preg_match('#\((.*?)\)#', $output, $state);
        return $state[1];
    }

    /**
     * Return the configuration type of the interface DHCP or Static.
     *
     * @return string
     */
    protected function confType()
    {
        $lines = (explode(PHP_EOL, File::get($this->interfaceFilePath())));
        $result = 'dhcp';
        foreach ($lines as $line) {
            if ("iface " . $this->device . " inet static" == $line) {
                $result = 'static';
            }
        }
        return $result;
    }

    protected function metric()
    {
        $lines = (explode(PHP_EOL, File::get($this->interfaceFilePath())));
        $out = '1000';
        foreach ($lines as $line) {
            if (str_contains($line, 'metric')) {
                $out = explode(' ', $line)[1];
            }
        }
        return $out;
    }

    /**
     * Read the dns-nameservers address line from the interface file.
     *
     * @return string
     */
    protected function dns()
    {
        $lines = (explode(PHP_EOL, File::get($this->interfaceFilePath())));
        foreach ($lines as $line) {
            if (str_contains($line, 'dns-nameservers')) {
                $data = explode(' ', $line);
                return isset($data[1]) ? $data[1] : '';
            }
        }
        return '';
    }

    /**
     * Get a example output of the command ifconfig {interfaz_name} to work with in local enviroment.
     *
     * @return string
     */
    protected function  interfaceOutputForDevelopment()
    {
        return <<<EOF
{$this->device}      Link encap:Ethernet  HWaddr aa:57:82:94:01:47  
          inet addr:165.227.63.109  Bcast:165.227.63.255  Mask:255.255.240.0
          inet6 addr: fe80::a857:82ff:fe94:147/64 Scope:Link
          UP BROADCAST RUNNING MULTICAST  MTU:1500  Metric:1
          RX packets:31129519 errors:0 dropped:0 overruns:0 frame:0
          TX packets:29833946 errors:0 dropped:0 overruns:0 carrier:0
          collisions:0 txqueuelen:1000 
          RX bytes:12172234716 (12.1 GB)  TX bytes:88545983630 (88.5 GB) 
EOF;
    }

    /**
     * Return the path for the interfaces file.
     *
     * @return string
     */
    protected function interfaceFilePath()
    {
        $path = (is_local_envorioment()) ? base_path('resources/stubs/interface_' . $this->device) : '/etc/network/interfaces.d/interface_' . $this->device;
        if (!File::exists($path)) {
            File::put($path, '');
        }
        return $path;
    }
}