<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Facades\App\Services\Network\NetworkInterfacesManager;

class NetworkInterfaceController extends Controller
{

    public function index()
    {
        return NetworkInterfacesManager::read();
    }

    public function store()
    {
        $data = request()->validate([
            'type' => 'required|in:dhcp,static',
            'ip' => 'required_if:type,static|ip',
            'mask' => 'required_if:type,static|ip',
            'gateway' => 'required_if:type,static|ip',
            'dns' => 'required_if:type,static|ip',
        ]);
        return NetworkInterfacesManager::write($data);
    }
}