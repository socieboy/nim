<?php


if (!function_exists('is_local')) {
    /**
     * Return if the app is on local enviroment.
     *
     * @return bool
     */
    function is_local_envorioment()
    {
        return app()->environment() == 'local';
    }
}

if (!function_exists('is_production')) {
    /**
     * Return if the app is on production enviroment.
     *
     * @return bool
     */
    function is_production_enviroment()
    {
        return app()->environment() == 'production';
    }
}
