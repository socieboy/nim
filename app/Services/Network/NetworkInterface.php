<?php

namespace App\Services\Network;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class NetworkInterface
{
    use ReadInterfaces;

    /**
     * Network interface name.
     *
     * @var $string
     */
    public $device;

    /**
     * Ethernet, Wireless, LTE.
     *
     * @var string
     */
    public $type = '';

    /**
     * DHCP, Static.
     *
     * @var string
     */
    public $conf_type = 'dhcp';

    /**
     * State of the connection.
     *
     * @var string
     */
    public $state = '';

    /**
     * Hardware MAC Address.
     *
     * @var string
     */
    public $mac = '';

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
     * Create a new instance of Network Interface object.
     *
     * @param $device
     */
    public function __construct($device)
    {
        $this->device = $device;
        $this->loadInterfaceConfiguration();
    }

    /**
     * Load the config information of the interface.
     */
    private function loadInterfaceConfiguration()
    {
        $command = 'sudo /sbin/ifconfig ' . $this->device;

        $output = is_local_envorioment() ? $this->interfaceOutputForDevelopment() : shell_exec($command);

        $regex = [];

        preg_match("/^({$this->device})\s+Link\s+encap:([A-z]*)\s+HWaddr\s+([A-z0-9:]*).*" .
            "inet addr:([0-9.]+).*Bcast:([0-9.]+).*Mask:([0-9.]+).*" .
            "MTU:([0-9.]+).*Metric:([0-9.]+).*" .
            "RX packets:([0-9.]+).*errors:([0-9.]+).*dropped:([0-9.]+).*overruns:([0-9.]+).*frame:([0-9.]+).*" .
            "TX packets:([0-9.]+).*errors:([0-9.]+).*dropped:([0-9.]+).*overruns:([0-9.]+).*carrier:([0-9.]+).*" .
            "RX bytes:([0-9.]+).*\((.*)\).*TX bytes:([0-9.]+).*\((.*)\)" .
            "/ims", $output, $regex);

        if (empty($regex)) return;

        $this->mac = $regex[3];
        $this->ip_address = $regex[4];
        $this->gateway = $this->gateway();
        $this->netmask = $regex[6];
        $this->metric = intval($regex[8]);
        $this->conf_type = $this->confType();
        $this->dns =$this->dns();
        $this->type = $this->type();
        $this->state = $this->state();
    }

    /**
     * Update the configuration fiel of the interface.
     *
     * @param $data
     * @return $this;
     */
    public function update($data)
    {
        $content = '# Network Interface "' . $this->device . '" Configuration File' . PHP_EOL . PHP_EOL;
        $content .= 'auto ' . $this->device . PHP_EOL;
        $content .= 'iface ' . $this->device . ' inet ';

        if ($data['conf_type'] == 'dhcp') {
            $content .= 'dhcp';
            return $this->writeFile($content);
        }

        $content .= 'static' . PHP_EOL;
        $content .= 'address ' . $data['ip_address'] . PHP_EOL;
        $content .= 'netmask ' . $data['netmask'] . PHP_EOL;
        $content .= 'gateway ' . $data['gateway'] . PHP_EOL;
        $content .= 'dns-nameservers ' . $data['dns'];
        return $this->writeFile($content);
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
        return $this;
    }

    /**
     * Return true of false if we can hit the endpoint with the current interface.
     *
     * @param string $endpoint
     * @return bool
     */
    public function ping($endpoint = 'google.com')
    {
        $command = 'ping -I ' . $this->device . ' -w 10 -c 1 ' . $endpoint;
        exec($command, $result);
        $result = implode(' ', $result);
        return (str_contains($result, ' bytes from ' . $this->ip_address . ' ' . $this->device . ':') && str_contains($result, '0% packet loss')) ? true : false;
    }

    /**
     * Apply the changes on the interface by reseting the networking service.
     *
     * @return $this
     */
    public function apply()
    {
        shell_exec('sudo ip addr flush ' . $this->device);
        shell_exec('sudo service networking restart');
        return $this;
    }
}