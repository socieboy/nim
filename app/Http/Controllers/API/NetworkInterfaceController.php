<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\NetworkInterfaceRequest;
use App\Services\Network\NetworkInterface;
use Facades\App\Services\Network\NetworkInterfacesManager;

class NetworkInterfaceController extends Controller
{
    /**
     * Return all network interfaces.
     *
     * @return array
     */
    public function index()
    {
        return NetworkInterfacesManager::read();
    }

    /**
     * Write the new interface settings.
     *
     * @param NetworkInterfaceRequest $request
     * @param $interface
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function store(NetworkInterfaceRequest $request, $interface)
    {
        $status = NetworkInterfacesManager::write($interface, $request->all());

        return response(['status' => $status]);
    }

    /**
     * Ping with network interface provided.
     *
     * @param $interface
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function show($interface)
    {
        $status = (new NetworkInterface($interface))->ping();

        return response(['status' => $status]);
    }
}
