<?php

namespace App\Services\Web;

use Illuminate\Support\Facades\File;

class WebServer
{

    public function update($port)
    {
        $content = explode(PHP_EOL, File::get($this->configurationFile()));
        foreach($content as $line){
            if((preg_match("/listen/i \d+/default_server/$", $line))){
                return true;
            }
        }
    }

    /**
     * Return the webserver configuration file path.
     *
     * @return string
     */
    protected function configurationFile()
    {
        return is_local_envorioment() ? base_path('resources/stubs/webserver') : '/etc/nginx/sites-availables/default';
    }
}