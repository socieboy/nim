<?php

namespace App\Services\Network;

use Illuminate\Support\Facades\Log;

class NetworkInterfacesManager
{

    /**
     * Create a Network Interface object for each interface
     * and return an array with all interfaces.
     *
     * @return array
     */
    public function read()
    {
        $interfaces = is_local_envorioment() ? $this->interfacesForDev() : $this->interfacesForProduction();
        $array = [];
        foreach ($interfaces as $interface) {
            $array[$interface[0]] = new NetworkInterface($interface[0]);
        }
        return $array;
    }

    /**
     * Find the interface privided and update the settings.
     *
     * @param $interface
     * @param $data
     * @return int
     */
    public function write($interface, $data)
    {
        try {
            $interface = new NetworkInterface($interface);
            $interface->update($data);
            shell_exec('sudo ip addr flush ' . $interface->name);
            shell_exec('sudo service networking restart');
        } catch (\Exception $exception) {
            Log::info($exception);
            return false;
        }
    }

    /**
     * Get the name of all interfaces from the system.
     *
     * @return string
     */
    protected function interfacesForProduction()
    {
        $output = shell_exec("nmcli device status");
        return $this->parseOutput($output);
//        $pattern = config('nim.interfaces.pattern');
//        $output = shell_exec("ls -1 /sys/class/net | grep '{$pattern}'");
//        if (empty($output)) return [];
//        $output = explode(PHP_EOL, $output);
//        $array = [];
//        foreach ($output as $line) {
//            if (!empty($line)) $array[] = $line;
//        }
//        return $array;
    }

    protected function interfacesForDev()
    {
        $output = <<<EHF
DEVICE  TYPE      STATE        CONNECTION        
enp1s0  ethernet  connected    Ifupdown (enp1s0) 
enp2s0  ethernet  connected    Ifupdown (enp2s0) 
enp3s0  ethernet  unavailable  --                
lo      loopback  unmanaged    --
EHF;
        return $this->parseOutput($output);
    }

    protected function parseOutput($output)
    {
        $output = explode(PHP_EOL, $output);
        unset($output[0]);
        unset($output[count($output)]);
        $array = [];
        foreach ($output as $key => $line) {
            $out = (explode('  ', $line));
            foreach ($out as $i => $x) {
                if (empty($x)) unset($out[$i]);
            }
            $array[] = $out;
        }
        return $array;
    }

    public function ping($interface, $endpoint)
    {
        exec('ping -I ' . $interface . ' -w 10 -c 1 ' . $endpoint, $result);
        $result = implode(' ', $result);
        return (str_contains($result, ' bytes from ') && str_contains($result, '0% packet loss')) ? true : false;
    }
}