<?php

class OptimizePressStats_Cookie
{
    const COOKIE_PREFIX = 'optimizepress_stats_';

    /**
     * Return cookie value
     * @param  string $key
     * @return mixed
     */
    public static function get($key)
    {
        if (isset($_COOKIE[self::COOKIE_PREFIX . $key])) {
            return $_COOKIE[self::COOKIE_PREFIX . $key];
        } else {
            return null;
        }
    }

    /**
     * Set cookie
     * @param string  $key
     * @param mixed  $value
     * @param integer $expire
     * @return boolean
     */
    public static function set($key, $value, $expire = 0)
    {
        return setcookie(self::COOKIE_PREFIX . $key, $value, time() + $expire, "/");
    }
}