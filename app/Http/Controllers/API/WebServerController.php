<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Facades\App\Services\Web\WebServer;

class WebServerController extends Controller
{
    public function index()
    {
        return [
            'name' => config('app.name'),
            'port' => 80,
        ];
    }

    /**
     * Update the webserver listening port.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function update()
    {
        request()->validate(['port' => 'required|numeric']);
        $status = WebServer::update(request('port'));

        return response(['status' => $status]);
    }
}
