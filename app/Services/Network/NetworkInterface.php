<?php

namespace App\Services\Network;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class NetworkInterface
{
    /**
     * Network interface name.
     *
     * @var $string
     */
    public $name;

    /**
     * Ethernet, Wireless, LTE.
     *
     * @var string
     */
    public $mode = '';

    /**
     * DHCP, Static.
     *
     * @var string
     */
    public $type = 'dhcp';

    /**
     * IP Address.
     *
     * @var string
     */
    public $ip_address = '';

    /**
     * Netmask.
     *
     * @var string
     */
    public $netmask = '';

    /**
     * Gateway.
     *
     * @var string
     */
    public $gateway = '';

    /**
     * DNS nameservers.
     *
     * @var string
     */
    public $dns = '';

    /**
     * Metric priority.
     *
     * @var string
     */
    public $metric = '';

    /**
     * MAC address.
     *
     * @var
     */
    public $mac;

    /**
     * Create a new instance of Network Interface object.
     *
     * @param $name
     */
    public function __construct($name)
    {
        $this->name = $name;
        $this->loadInterfaceConfiguration();
    }

    /**
     * Load the config information of the interface.
     */
    private function loadInterfaceConfiguration()
    {
        $command = 'sudo /sbin/ifconfig ' . $this->name;

        $output = is_local_envorioment() ? $this->interfaceOutputForDevelopment() : shell_exec($command);

        Log::info($output);

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
        $this->gateway = $this->interfaceGateway();
        $this->netmask = $regex[6];
        $this->metric = intval($regex[8]);
        $this->type = $this->interfaceType();
        $this->dns =$this->interfaceDNS();
    }

    protected function interfaceGateway()
    {
        $output = is_local_envorioment() ? 'IP4.GATEWAY:                            192.11.88.1' . PHP_EOL : shell_exec('nmcli device show ' . $this->name . ' | grep IP4.GATEWAY');
        Log::info($output);
        $output = explode(':', $output);
        if (isset($output[1])) return trim($output[1]);
        return '';
    }

    /**
     * Return the type of the interface DHCP or Static.
     *
     * @return string
     */
    protected function interfaceType()
    {
        $lines = (explode(PHP_EOL, File::get($this->interfaceFilePath())));
        $result = 'dhcp';
        foreach ($lines as $line) {
            if ("iface " . $this->name . " inet static" == $line) {
                $result = 'static';
            }
        }
        return $result;
    }

    /**
     * Read the dns-nameservers address line from the interface file.
     *
     * @return string
     */
    protected function interfaceDNS()
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
{$this->name}      Link encap:Ethernet  HWaddr aa:57:82:94:01:47  
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
     * Update a file on the /etc/network/interfaces.d/interface_{$this->name}
     * with the configuration for the interface.
     *
     * @param $data
     */
    public function update($data)
    {
        $content = '# Network Interface "' . $this->name . '" Configuration File' . PHP_EOL . PHP_EOL;
        $content .= 'auto ' . $this->name . PHP_EOL;
        $content .= 'iface ' . $this->name . ' inet ';

        if ($data['type'] == 'dhcp') {
            $content .= 'dhcp';
            $this->writeFile($content);
            return;
        }

        $content .= 'static' . PHP_EOL;
        $content .= 'address ' . $data['ip_address'] . PHP_EOL;
        $content .= 'netmask ' . $data['netmask'] . PHP_EOL;
        $content .= 'gateway ' . $data['gateway'] . PHP_EOL;
        $content .= 'dns-nameservers ' . $data['dns'];
        $this->writeFile($content);
//        $this->writeResolvConfigFile($data['dns']);
    }

    /**
     * Write the interfaces file.
     *
     * @param $content
     * @return int
     */
    protected function writeFile($content)
    {
        File::put($this->interfaceFilePath(), $content);
    }

    /**
     * Update the resolv.conf file.
     *
     * @param $dns
     */
    protected function writeResolvConfigFile($dns)
    {
        $path = (is_local_envorioment()) ? base_path('resources/stubs/resolv.conf') : '/etc/resolv.conf';
        File::put($path, 'nameserver ' . $dns);
    }

    /**
     * Return the path for the interfaces file.
     *
     * @return string
     */
    protected function interfaceFilePath()
    {
        $path = (is_local_envorioment()) ? base_path('resources/stubs/interface_' . $this->name) : '/etc/network/interfaces.d/interface_' . $this->name;
        if (!File::exists($path)) {
            File::put($path, '');
        }
        return $path;
    }
}