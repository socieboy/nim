<?php

namespace App\Services\Network;

class NetworkInterface
{
    protected $name;

    protected $type = 'dhcp';

    protected $ip_address = '';

    protected $mask = '';

    protected $gateway = '';

    protected $dns = '';

    public function __construct($name)
    {
        $this->name = $name;
        $this->resolveInterface();
    }

    public function getType()
    {
        return $this->type;
    }

    public function getIpAddress()
    {
        return $this->ip_address;
    }

    public function getMask()
    {
        return $this->mask;
    }

    public function getGateway()
    {
        return $this->gateway;
    }

    public function getDns()
    {
        return $this->dns;
    }

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
        $interface = null;
        if (!empty($regex)) {
            $interface = array();
            $interface['name'] = $regex[1];
            $interface['type'] = $regex[2];
            $interface['mac'] = $regex[3];
            $interface['ip'] = $regex[4];
            $interface['broadcast'] = $regex[5];
            $interface['netmask'] = $regex[6];
            $interface['mtu'] = $regex[7];
            $interface['metric'] = intval($regex[8]);
            $interface['rx']['packets'] = (int)$regex[9];
            $interface['rx']['errors'] = (int)$regex[10];
            $interface['rx']['dropped'] = (int)$regex[11];
            $interface['rx']['overruns'] = (int)$regex[12];
            $interface['rx']['frame'] = (int)$regex[13];
            $interface['rx']['bytes'] = (int)$regex[19];
            $interface['rx']['hbytes'] = (int)$regex[20];
            $interface['tx']['packets'] = (int)$regex[14];
            $interface['tx']['errors'] = (int)$regex[15];
            $interface['tx']['dropped'] = (int)$regex[16];
            $interface['tx']['overruns'] = (int)$regex[17];
            $interface['tx']['carrier'] = (int)$regex[18];
            $interface['tx']['bytes'] = (int)$regex[21];
            $interface['tx']['hbytes'] = (int)$regex[22];
        }
        $this->ip_address = $interface['ip'];
        $this->gateway = $interface['broadcast'];
        $this->mask = $interface['netmask'];
        $this->dns = '';
    }

    public function inArray()
    {
        return [
            'name' => $this->name,
            'type' => $this->type,
            'ip_address' => $this->ip_address,
            'mask' => $this->mask,
            'gateway' => $this->gateway,
            'dns' => $this->dns,
        ];
    }

    protected function interfaceOutputForDevelopment()
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
}