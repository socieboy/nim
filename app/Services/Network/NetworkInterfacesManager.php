<?php

namespace App\Services\Network;

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
        $interfaces = is_local_envorioment() ? ['eth0'] : $this->interfacesFromSystem();
        $array = [];
        foreach ($interfaces as $interface) {
            $array[$interface] = new NetworkInterface($interface);
        }
        return $array;
    }

    public function write($interface, $data)
    {
        // Write new interface config and restar network service.
    }

    /**
     * Get the name of all interfaces from the system.
     *
     * @return string
     */
    protected function interfacesFromSystem()
    {
        $pattern = config('nim.interfaces.pattern');
        $output = shell_exec("ls -1 /sys/class/net | grep '{$pattern}'");
        if (empty($output)) return [];
        return explode(PHP_EOL, $output);
    }
}