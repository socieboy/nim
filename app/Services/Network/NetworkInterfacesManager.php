<?php

namespace App\Services\Network;

use Illuminate\Support\Facades\Log;

class NetworkInterfacesManager
{
    use ReadInterfaces;

    /**
     * Create a Network Interface object for each interface device readed.
     *
     * @return array
     */
    public function read()
    {
        $devices = is_local_envorioment() ? ['eth0', 'eth1'] : $this->readDevices();
        $array = [];
        foreach ($devices as $device) {
            $array[$device] = new NetworkInterface($device);
        }

        return $array;
    }

    /**
     * Find the interface provided and update the settings.
     *
     * @param $interface
     * @param $data
     *
     * @return int
     */
    public function write($device, $data)
    {
        try {
            $interface = new NetworkInterface($device);
            $interface->update($data)->apply();

            return true;
        } catch (\Exception $exception) {
            Log::info($exception);

            return false;
        }
    }
}
