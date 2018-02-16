<?php

namespace App\Services\Network;

use Illuminate\Support\Facades\Log;

trait ReadInterfaces
{
    protected $commands = [
        'dns' => 'IP4.DNS',
        'gateway' => 'IP4.GATEWAY',
        'type' => 'GENERAL.TYPE',
        'mac' => 'GENERAL.HWADDR',
        'connection' => 'GENERAL.CONNECTION',
        'state' => 'GENERAL.STATE',
        'ip' => 'IP4.ADDRESS',
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
        $command = 'nmcli device show ' . $this->device . ' | grep ' . $this->commands[$value];
        Log::info($command);
        $output = is_local_envorioment() ? $this->commands[$value] . ':                            192.11.88.1' . PHP_EOL : shell_exec($command);
        Log::info($output);
        $output = explode(':', $output);
        if (isset($output[1])) return trim($output[1]);
        return '';
    }
}