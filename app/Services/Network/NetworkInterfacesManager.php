<?php

namespace App\Services\Network;

class NetworkInterfacesManager
{

    /**
     * Return all network interfaces and information.
     *
     * @return array
     */
    public function read()
    {
        return $this->getInterfaces();
    }

    public function write($interface, $data)
    {
        // Write new interface config and restar network service.
    }

    /**
     * Create a Network Interface object for each interface
     * and return an array with all interfaces.
     *
     * @return array
     */
    protected function getInterfaces()
    {
        $data = is_local_envorioment() ? $this->interfacesForDevelopment() : $this->interfacesFromSystem();
        $interfaces = explode(PHP_EOL, $data);
        $array = [];
        foreach ($interfaces as $interface) {
            $array[$interface] = new NetworkInterface($interface);
        }
        return $array;
    }

    /**
     * Get the name of all interfaces from the system.
     *
     * @return string
     */
    protected function interfacesFromSystem()
    {
        return shell_exec("ls -1 /sys/class/net | grep '" . $this->networkInterfacesPattern() . "'");
    }

    /**
     * Return a string for test the interface resopond from the shell command ip link show.
     *
     * @return string
     */
    protected function interfacesForDevelopment()
    {
        return <<<EOF
eth0
EOF;
    }

    /**
     * Return how the network interfaces names should be.
     *
     * @return string
     */
    protected function networkInterfacesPattern()
    {
        return 'eth[0-9]';
    }
}