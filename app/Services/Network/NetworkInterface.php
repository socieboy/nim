<?php

namespace App\Services\Network;

class NetworkInterface
{
    protected $name;

    protected $ip_address;

    protected $mask;

    protected $gateway;

    protected $dns;

    public function __construct($name)
    {
        $this->name = $name;
        $this->resolveInterface();
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
        $this->ip_address = '192.168.1.2';
        $this->mask = '255.255.255.0';
        $this->gateway = '192.168.1.1';
        $this->dns = '8.8.8.8';
    }

    public function inArray()
    {
        return [
            'ip_address' => $this->ip_address,
            'mask' => $this->mask,
            'gateway' => $this->gateway,
            'dns' => $this->dns,
        ];
    }
}