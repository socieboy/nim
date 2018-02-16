<?php

namespace App\Services\Network;

trait ReadInterfaces
{
    protected $commands = [
        'dns' => 'IP4.DNS[1]:',
        'gateway' => 'IP4.GATEWAY:',
        'type' => 'GENERAL.TYPE:',
        'mac' => 'GENERAL.HWADDR:',
        'connection' => 'GENERAL.CONNECTION:',
        'state' => 'GENERAL.STATE:',
    ];
    /**
     * Get the name of all interfaces from the system.
     *
     * @return string
     */
    protected function readDeviceNames()
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
    }

    /**
     * Check if the line is a interface information.
     *
     * @param $line
     * @return bool
     */
    protected function isValidInterface($line)
    {
        return !empty($line) && !str_contains($line, 'DEVICE  TYPE      STATE        CONNECTION') && !str_contains($line, 'lo      loopback');
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
     * Read a specific value of the network manager device show command.
     *
     * @param $value
     * @return string
     */
    protected function interfaceValue($value)
    {
        $output = is_local_envorioment() ? $this->commands[$value] . ':                            192.11.88.1' . PHP_EOL : shell_exec('nmcli device show ' . $this->device . ' | grep ' . $value);
        $output = explode(':', $output);
        if (isset($output[1])) return trim($output[1]);
        return '';
    }
}