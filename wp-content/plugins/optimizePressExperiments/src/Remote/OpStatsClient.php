<?php

class OptimizePressStats_Remote_OpStatsClient implements OptimizePressStats_Remote_ClientInterface
{
    /**
     * URL to OP stats platform
     * @var string
     */
    protected $url = 'http://opstats.dev/aggregate/index';

    /**
     * Send daily aggregated data to OP stats remote installation
     * @param  array $data
     * @return boolean
     */
    public function sendDailyAggregates($data)
    {
        $response = wp_remote_post($this->url, array(
            'headers' => array(
                OptimizePress_Sl_Api::HEADER_API_KEY_PARAM          => op_sl_get_key(),
                OptimizePress_Sl_Api::HEADER_INSTALLATION_URL_PARAM => op_sl_get_url(),
                'Content-Type'                                      => 'application/json',
            ),
            'body' => json_encode($data),
        ));

        if (is_wp_error($response)) {
            return false;
        }

        if (wp_remote_retrieve_response_code($response) !== 200) {
            return false;
        }

        return true;
    }
}