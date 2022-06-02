<?php


if (!function_exists('dev_config')) {
    /*
     * Returns the name of the guard defined
     * by the application config
     */
    function dev_config($stage, $key, $default = null)
    {

        
        $basekey = 'kda.backpack.devtools';
        $staged = config($basekey . '.stages.' . $stage . '.' . $key);
        dump($basekey . '.stages.' . $stage . '.' . $key, $staged);
        if ($staged === null) {
            $value = config($basekey . '.' . $key, $default);
            dump($basekey . '.' . $key,$value);

            return $value;
        }
        return $staged;
    }
}
