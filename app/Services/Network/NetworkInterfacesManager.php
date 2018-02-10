<?php

namespace App\Services\Network;

use Illuminate\Support\Facades\File;

class NetworkInterfacesManager
{

    public function read()
    {
        return $this->readInterfaces();
    }

    public function write()
    {
    }

    protected function readInterfaces()
    {
        $interfaces = $this->getInterfaces() ;

        $array = [];

        foreach ($interfaces as $interface) {
            $array[$interface] = (new NetworkInterface($interface))->inArray();
        }

        return $array;

//       $path = $this->getInterfacesPath();
//
//        $file = File::get($path);
//
//        $result = (explode(PHP_EOL, $file));

//
//        return $result;
    }

    protected function getInterfaces()
    {
        $data = is_local_envorioment() ? $this->interfacesForDevelopment() : shell_exec('ls -1 /sys/class/net');
        return explode(PHP_EOL, $data);
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
lo
EOF;
    }

    /**
     * Return the path for the interfaces file.
     *
     * @return string
     */
    protected function getInterfacesPath()
    {
        return (is_local_envorioment()) ? base_path('resources/stubs/interfaces') : '/etc/network/interfaces.d/interfaces';
    }
}