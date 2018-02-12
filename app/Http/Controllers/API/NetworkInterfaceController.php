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


    /**
     * Write the new interface settings.
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory
     */
    public function store($interface)
    {
        $data = request()->validate([
            'type' => 'required|in:dhcp,static',
            'ip_address' => 'required_if:type,static',
            'netmask' => 'required_if:type,static',
            'gateway' => 'required_if:type,static',
            'dns' => 'sometimes',
        ]);
        $status = NetworkInterfacesManager::write($interface, $data);
        return response([
            'status' => $status
        ]);
    }
}