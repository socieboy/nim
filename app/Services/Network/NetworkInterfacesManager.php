<?php

namespace App\Services\Network;

use Illuminate\Support\Facades\Log;

class NetworkInterfacesManager
{

    /**
     * Create a Network Interface object for each interface
     * and return an array with all interfaces.
     *
     * @return array
     */
    public function read()
    {
        return $interfaces = is_local_envorioment() ? ['eth0', 'eth1'] : $this->interfacesForProduction();
//        $array = [];
//        foreach ($interfaces as $interface) {
//            $array[$interface] = new NetworkInterface($interface);
//        }
        return $array;
    }

    /**
     * Find the interface privided and update the settings.
     *
     * @param $interface
     * @param $data
     * @return int
     */
    public function write($interface, $data)
    {
        try {
            $interface = new NetworkInterface($interface);
            $interface->update($data);
            shell_exec('sudo ip addr flush ' . $interface->name);
            shell_exec('sudo service networking restart');
        } catch (\Exception $exception) {
            Log::info($exception);
            return false;
        }
    }

    /**
     * Get the name of all interfaces from the system.
     *
     * @return string
     */
    protected function interfacesForProduction()
    {
        $output = shell_exec("nmcli device status");
        $output = explode(PHP_EOL, $output);
        $interfaces = [];
        foreach ($output as $key => $line) {
            if ($this->isValidInterface($line)) {
                $out = $this->cleanInterfaceOutput($line);
                $interfaces[] = $out[0];
            }
        }
        return $interfaces;

//        $pattern = config('nim.interfaces.pattern');
//        $output = shell_exec("ls -1 /sys/class/net | grep '{$pattern}'");
//        if (empty($output)) return [];
//        $output = explode(PHP_EOL, $output);
//        $array = [];
//        foreach ($output as $line) {
//            if (!empty($line)) $array[] = $line;
//        }
//        return $array;
    }

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

    protected function isValidInterface($line)
    {
        return !empty($line) && !str_contains($line, 'DEVICE  TYPE      STATE        CONNECTION') && !str_contains($line, 'lo      loopback');
    }
}