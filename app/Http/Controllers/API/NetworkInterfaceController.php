<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Facades\App\Services\Network\NetworkInterfacesManager;

class NetworkInterfaceController extends Controller
{
    /**
     * Return all network interfaces.
     *
     * @return mixed
     */
    public function index()
    {
        return NetworkInterfacesManager::read();
    }

    /*
     * Write the new network interfaces settings.
     */
    public function store()
    {
        $data = request()->validate([
            'type' => 'required|in:dhcp,static',
            'ip' => 'required_if:type,static|ip',
            'netmask' => 'required_if:type,static|ip',
            'gateway' => 'required_if:type,static|ip',
            'dns' => 'required_if:type,static|ip',
        ]);
        dd($data);
        return NetworkInterfacesManager::write($data);
    }
}