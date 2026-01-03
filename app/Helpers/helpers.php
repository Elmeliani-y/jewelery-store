<?php

if (!function_exists('prefixed_url')) {
    /**
     * Generate a URL with the app prefix
     *
     * @param string $path
     * @param mixed $parameters
     * @param bool|null $secure
     * @return string
     */
    function prefixed_url($path = '', $parameters = [], $secure = null)
    {
        $prefix = config('app.url_prefix', 'b75/n95uk');
        $path = ltrim($path, '/');
        $fullPath = $prefix . ($path ? '/' . $path : '');
        
        return url($fullPath, $parameters, $secure);
    }
}

if (!function_exists('prefixed_route')) {
    /**
     * Generate a route URL with the app prefix
     *
     * @param string $name
     * @param mixed $parameters
     * @param bool $absolute
     * @return string
     */
    function prefixed_route($name, $parameters = [], $absolute = true)
    {
        return route($name, $parameters, $absolute);
    }
}

if (!function_exists('app_prefix')) {
    /**
     * Get the app URL prefix
     *
     * @return string
     */
    function app_prefix()
    {
        return config('app.url_prefix', 'b75/n95uk');
    }
}
