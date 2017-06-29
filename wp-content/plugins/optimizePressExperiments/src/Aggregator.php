<?php

class OptimizePressStats_Aggregator
{
    /**
     * Attach hooks and filters
     */
    public function __construct()
    {
        add_action('wp', array($this, 'scheduleCronjobs'));
        add_action('optimizepress_stats/send_daily_aggregates', array($this, 'sendDailyAggregates'));
    }

    /**
     * Schedule cronjobs needed by the aggregate worker
     * @return void
     */
    public function scheduleCronjobs()
    {
        // Schedule sending of daily aggregates
        if (!wp_next_scheduled('optimizepress_stats/send_daily_aggregates')) {
            wp_schedule_event(time(), 'twicedaily', 'optimizepress_stats/send_daily_aggregates');
        }
    }

    /**
     * Calculate correct date and send daily aggregate as well as daily aggregates for previous unsuccessfull dates
     *
     * This will be triggered with cron twice daily
     *
     * @return void
     */
    public function sendDailyAggregates()
    {
        // Fetch correct date
        $date = date('Y-m-d', apply_filters('optimizepress_stats/days_stored_locally', strtotime('-15 day')));

        // Fetch aggregates and send them to remote
        $status = $this->dailyAggregates($date);

        if (apply_filters('optimizepress_stats/check_missing_dates', true)) {
            // Run for three previous days (in case that process didn't complete successfully for them or there weren't any visitors)
            for ($a = 1; $a < 4; $a += 1) {
                $newDate = date_format(date_sub(date_create($date), date_interval_create_from_date_string($a . ' days')), 'Y-m-d');
                $status = $this->dailyAggregates($newDate);
            }
        }

        if (apply_filters('optimizepress_stats/check_started_dates', true)) {
            // We should probably fetch 3 days with status "started"
            $unsuccessfullDates = $this->getDatesWithStatus('started', 3);
            if (count($unsuccessfullDates) > 0) {
                foreach ($unsuccessfullDates as $newDate) {
                    $status = $this->dailyAggregates($newDate);
                }
            }
        }
    }

    /**
     * Calculate and save daily aggregates to remote system
     * @param  string $date
     * @return boolean
     */
    public function dailyAggregates($date)
    {
        $status = $this->getDateStatus($date);
        if ($status !== 'sent' && $status !== 'no_data') {
            $aggregateData = $this->getDailyVisitsData($date);

            if (count($aggregateData)) {
                $status = $this->setDateStatus($date, 'started');
                if ($this->sendToRemote($aggregateData)) {
                    $status = $this->setDateStatus($date, 'sent');
                }
            } else {
                $status = $this->setDateStatus($date, 'no_data');
            }
        }

        return $status;
    }

    /**
     * Return daily aggregated visits data
     * @param  string $date
     * @return array
     */
    protected function getDailyVisitsData($date)
    {
        global $wpdb;

        $results = $wpdb->get_results(
            $wpdb->prepare("
                SELECT COUNT(id) AS views, COUNT(DISTINCT user_id) AS `unique`, SUM(conversion) AS conversions, page_id AS page, country, variation_id AS variation, template_id AS template, DATE_FORMAT(timestamp, '%%Y-%%m-%%d') AS date, '%s' AS `key`
                FROM `{$wpdb->prefix}optimizepress_stats_views`
                WHERE DATE_FORMAT(timestamp, '%%Y-%%m-%%d') = '%s'
                GROUP BY page_id, country, variation_id
            ", str_replace('-', '', op_sl_get_key()), $date),
            OBJECT
        );

        return $results;
    }

    /**
     * Return OP stats remote client
     * @return OptimizePressStats_Remote_ClientInterface
     */
    public function getRemoteClient()
    {
        return new OptimizePressStats_Remote_OpStatsClient();
    }

    /**
     * Send aggregated data to remote
     * @param  array $data
     * @return boolean
     */
    public function sendToRemote($data)
    {
        return $this->getRemoteClient()->sendDailyAggregates($data);
    }

    /**
     * Return remote aggregate data status for defined $date
     *
     * Available statuses:
     * - no_data - no data for current date
     * - sent - data has been sent to central platform
     * - started - aggregated data has been prepared
     *
     * @param  string $date
     * @return mixed Status string if it exists or NULL if it doesn't
     */
    protected function getDateStatus($date)
    {
        global $wpdb;

        $status = $wpdb->get_var(
            $wpdb->prepare("
                SELECT status
                FROM `{$wpdb->prefix}optimizepress_stats_remote_status`
                WHERE date = '%s'
            ", $date)
        );

        return $status;
    }

    /**
     * Set remote aggregate data status for defined $date
     * @param string $date
     * @param string $status
     * @return string
     */
    protected function setDateStatus($date, $status)
    {
        global $wpdb;

        $wpdb->replace(
            $wpdb->prefix . 'optimizepress_stats_remote_status',
            array(
                'date' => $date,
                'status' => $status,
                'timestamp' => date('Y-m-d H:i:s')
            ),
            array('%s', '%s', '%s')
        );

        return $status;
    }

    /**
     * Fetch defined $num of latest dates with defined $status
     * @param  string  $status
     * @param  integer $num
     * @return array
     */
    protected function getDatesWithStatus($status, $num = 3)
    {
        global $wpdb;

        $dates = $wpdb->get_col(
            $wpdb->prepare("
                SELECT date
                FROM `{$wpdb->prefix}optimizepress_stats_remote_status`
                WHERE status = '%s'
                ORDER BY date DESC
                LIMIT %d
            ", $status, $num)
        );

        return $dates;
    }
}

new OptimizePressStats_Aggregator();