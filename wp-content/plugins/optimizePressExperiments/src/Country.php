<?php

class OptimizePressStats_Country
{
    protected static $geoip;

    /**
     * Get country code
     * @param  string $ipAddress
     * @return string
     */
    public static function getCountryCode($ipAddress)
    {
        return op_geoip_country_code_by_addr(self::getGeoIp(), $ipAddress);
    }

    /**
     * Return geoIP object
     * @return GeoIP
     */
    protected static function getGeoIp()
    {
        if (self::$geoip === null) {
            self::$geoip = op_geoip_open(__DIR__ . "/../data/GeoIP.dat", OP_GEOIP_STANDARD);
        }

        return self::$geoip;
    }
}