<?php

namespace App\Services\Network;

use Illuminate\Support\Facades\File;

class NetworkInterface
{
    public $name;

    public $mode = '';

    public $type = '';

    public $ip_address = '';

    public $netmask = '';

    public $gateway = '';

    public $dns = '';

    public $metric = '';

    public $mac;

    public function __construct($name)
    {
        $this->name = $name;
        $this->resolveInterface();
    }

    /**
     * Assign the information for the interface name provide.
     */
    private function resolveInterface()
    {
        $output = is_local_envorioment() ? $this->interfaceOutputForDevelopment() : exec('ifconfig ' . $this->name);

        $regex = [];

        preg_match("/^({$this->name})\s+Link\s+encap:([A-z]*)\s+HWaddr\s+([A-z0-9:]*).*" .
            "inet addr:([0-9.]+).*Bcast:([0-9.]+).*Mask:([0-9.]+).*" .
            "MTU:([0-9.]+).*Metric:([0-9.]+).*" .
            "RX packets:([0-9.]+).*errors:([0-9.]+).*dropped:([0-9.]+).*overruns:([0-9.]+).*frame:([0-9.]+).*" .
            "TX packets:([0-9.]+).*errors:([0-9.]+).*dropped:([0-9.]+).*overruns:([0-9.]+).*carrier:([0-9.]+).*" .
            "RX bytes:([0-9.]+).*\((.*)\).*TX bytes:([0-9.]+).*\((.*)\)" .
            "/ims", $output, $regex);

        if (empty($regex)) return;

        $this->mode = $regex[2];
        $this->mac = $regex[3];
        $this->ip_address = $regex[4];
        $this->gateway = $regex[5];
        $this->netmask = $regex[6];
        $this->mtu = $regex[7];
        $this->metric = intval($regex[8]);
        $this->type = $this->getInterfaceType();
        $this->dns = '';
    }

    /**
     * Return the type of the interface DHCP or Static.
     *
     * @return string
     */
    protected function getInterfaceType()
    {
        $lines = (explode(PHP_EOL, File::get($this->getInterfacesPath())));
        $result = 'dhcp';
        foreach ($lines as $line) {
            if ("iface " . $this->name . " inet static" == $line) {
                $result = 'static';
            }
        }
        return $result;
    }

    /**
     * Get a example output of the command ifconfig {interfaz_name} to work with in local enviroment.
     *
     * @return string
     */
    protected function  interfaceOutputForDevelopment()
    {
        return <<<EOF
eth0      Link encap:Ethernet  HWaddr aa:57:82:94:01:47  
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
    protected function getInterfacesPath()
    {
        return (is_local_envorioment()) ? base_path('resources/stubs/interfaces') : '/etc/network/interfaces.d/interfaces';
    }
}