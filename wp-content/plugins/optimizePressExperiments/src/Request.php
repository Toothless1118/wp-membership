<?php

class OptimizePressStats_Request
{
    /**
     * Validation status.
     * @var string
     */
    protected static $status = 'valid';

    /**
     * RegExp with most bots.
     * @var string
     */
    protected $botRegex = '/Yahoo!\s+Slurp|YahooSeeker|facebookexternalhit|ia_archiver|Scrapy|http_requester|HTTrack|robot|crawler|spider|[a-z._]bot(.|_|\s|$)/i';

    /**
     * Init validation.
     */
    public function __construct()
    {
        $checks = array(
            array($this, 'httpRequestType'),
            array($this, 'userAgentBot')
        );

        $checks = apply_filters('optimizepress-stats/valid-request-checks', $checks);

        foreach ($checks as $check) {
            if ('valid' !== self::$status = call_user_func($check)) {
                break;
            }
        }
    }

    /**
     * Return validation status.
     * @return boolean
     */
    public static function isValid()
    {
        return 'valid' === self::$status;
    }

    /**
     * Return status.
     * @return string
     */
    public static function getStatus()
    {
        return self::$status;
    }

    /**
     * Check HTTP request type validity.
     * @return string
     */
    protected function httpRequestType()
    {
        if (in_array($_SERVER['REQUEST_METHOD'], apply_filters('optimizepress-stats/valid-request-methods', array('GET', 'POST')))) {
            return 'valid';
        }

        return 'invalid_request_method';
    }

    /**
     * Check User Agent against bot regex.
     * @return string
     */
    protected function userAgentBot()
    {
        if (preg_match($this->botRegex, $_SERVER['HTTP_USER_AGENT'])) {
            return 'invalid_user_agent';
        }

        return 'valid';
    }

}

new OptimizePressStats_Request;