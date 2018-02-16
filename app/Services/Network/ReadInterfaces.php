<?php

namespace App\Services\Network;

use Illuminate\Support\Facades\Log;

trait ReadInterfaces
{
    protected $commands = [
        'dns'        => 'IP4.DNS',
        'ip'         => 'IP4.ADDRESS',
        'gateway'    => 'IP4.GATEWAY',
        'type'       => 'GENERAL.TYPE',
        'state'      => 'GENERAL.STATE',
        'mac'        => 'GENERAL.HWADDR',
        'connection' => 'GENERAL.CONNECTION',
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
//        $output = is_local_envorioment() ? $this->commands[$value] . ':                            192.11.88.1' . PHP_EOL : shell_exec('nmcli device show ' . $this->device . ' | grep ' . $this->commands[$value]);
        $output = shell_exec('nmcli device show ' . $this->device);
        $output = array_map('trim', explode(':', $output)[1]);
        Log::info($output);
        return $output;
    }
}