<?php

class OptimizePressStats_User
{
    const COOKIE_LIFETIME = 86400;

    protected static $instance = null;

    private function __construct()
    {}

    /**
     * Return user object
     *
     * Singleton pattern
     * @return OptimizePressStats_User
     */
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Return user ID
     * @return string
     */
    public function getId()
    {
        $userId = OptimizePressStats_Cookie::get('user_id');
        if (null === $userId) {
            $userId = $this->generateId();
        }

        return $userId;
    }

    /**
     * Generates user ID (using uniqid function) and save it to cookies
     * @return string
     */
    protected function generateId()
    {
        $userId = uniqid('ui_', true);

        OptimizePressStats_Cookie::set('user_id', $userId, self::COOKIE_LIFETIME);

        return $userId;
    }

    /**
     * Return country code name from ccokie
     * @return string
     */
    public function getCountry()
    {
        $countryCode = OptimizePressStats_Cookie::get('user_country');
        if (null === $countryCode) {
            $countryCode = $this->determineCountryFromIp();
        }

        return $countryCode;
    }

    /**
     * Find country code name from IP and save it to cookies
     * @return string
     */
    protected function determineCountryFromIp()
    {
        $countryCode = OptimizePressStats_Country::getCountryCode(op_get_client_ip_env());

        OptimizePressStats_Cookie::set('user_country', $countryCode, self::COOKIE_LIFETIME);

        return $countryCode;
    }
}