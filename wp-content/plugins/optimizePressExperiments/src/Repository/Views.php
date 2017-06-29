<?php

/**
 * Repository class for Stats
 */
class OptimizePressStats_Repository_Views
{
    /**
     * Return sum of aggregated views between $start and $end dates.
     * @param   string $start expects date in Y-m-d format
     * @param   string $end   expects date in Y-m-d format
     * @param   integer $offset
     * @param   integer $limit
     * @return  array
     */
    public function getAggregatedViewsSum($start, $end, $offset = 0, $limit = 20)
    {
        global $wpdb;

        $results = $wpdb->get_results(
            $wpdb->prepare("
                SELECT `variation_id` AS `page`, COUNT(`id`) AS `views`, COUNT(DISTINCT `user_id`) AS `unique`, SUM(`conversion`) AS `conversions`
                FROM `{$wpdb->prefix}optimizepress_stats_views`
                WHERE DATE_FORMAT(`timestamp`, '%%Y-%%m-%%d') >= '%s' AND DATE_FORMAT(`timestamp`, '%%Y-%%m-%%d') <= '%s'
                GROUP BY `variation_id`
                ORDER BY `views` DESC
                LIMIT %d, %d
            ", $start, $end, $offset, $limit),
            OBJECT_K
        );

        return $results;
    }

    /**
     * Return number of pages with stats for give date range. Used in pagination.
     * @param  string $start expects date in Y-m-d format
     * @param  string $end   expects date in Y-m-d format
     * @return integer
     */
    public function getAggregatedViewsCount($start, $end)
    {
        global $wpdb;

        return (int) $wpdb->get_var(
            $wpdb->prepare("
                SELECT COUNT(DISTINCT `variation_id`)
                FROM `{$wpdb->prefix}optimizepress_stats_views`
                WHERE DATE_FORMAT(`timestamp`, '%%Y-%%m-%%d') >= '%s' AND DATE_FORMAT(`timestamp`, '%%Y-%%m-%%d') <= '%s'
            ", $start, $end)
        );
    }

    /**
     * Return aggregated views between $start and $end dates grouped by $period.
     * @param  string $start expects date in Y-m-d format
     * @param  string $end   expects date in Y-m-d format
     * @param  string $period
     * @return array
     */
    public function getAggregatedViews($start, $end, $period = 'day')
    {
        switch ($period) {
            case 'month':
                $dateFormat = '%%Y-%%m';
                break;
            case 'week':
                $dateFormat = '%%Y-%%v';
                break;
            case 'day':
            default:
                $dateFormat = '%%Y-%%m-%%d';
        }

        global $wpdb;

        $results = $wpdb->get_results(
            $wpdb->prepare("
                SELECT COUNT(id) AS views, COUNT(DISTINCT user_id) AS `unique`, SUM(conversion) AS conversions, page_id AS page, DATE_FORMAT(timestamp, %s) AS `date`
                FROM `{$wpdb->prefix}optimizepress_stats_views`
                WHERE DATE_FORMAT(timestamp, '%%Y-%%m-%%d') >= '%s' AND DATE_FORMAT(timestamp, '%%Y-%%m-%%d') <= '%s'
                GROUP BY page_id, `date`
            ", $dateFormat, $start, $end),
            OBJECT
        );

        return $results;
    }

    /**
     * Return experiments stats between given dates.
     * @param  integer $experimentId
     * @param  string $start        expects date in Y-m-d format
     * @param  string $end          expects date in Y-m-d format
     * @return array
     */
    public function getExperimentViews($experimentId, $start, $end)
    {
        global $wpdb;

        $results = $wpdb->get_results(
            $wpdb->prepare("
                SELECT COUNT(`id`) AS `views`, COUNT(DISTINCT `user_id`) AS `unique`, SUM(`conversion`) AS `conversions`, `page_id` AS `page`, DATE_FORMAT(`timestamp`, '%%Y-%%m-%%d') AS `date`, `variation_id` AS `variation`
                FROM `{$wpdb->prefix}optimizepress_stats_views`
                WHERE `experiment_id` = %d AND DATE_FORMAT(`timestamp`, '%%Y-%%m-%%d') >= '%s' AND DATE_FORMAT(`timestamp`, '%%Y-%%m-%%d') <= '%s'
                GROUP BY `variation`, `date`
            ", $experimentId, $start, $end),
            OBJECT
        );

        return $results;
    }

    /**
     * Return summarized experiment stats between given dates.
     * @param  integer $experimentId
     * @param  string $start        expects date in Y-m-d format
     * @param  string $end          expects date in Y-m-d format
     * @return array
     */
    public function getExperimentViewsSum($experimentId, $start = null, $end = null)
    {
        global $wpdb;

        if (null === $start) {
            $start = '2016-01-01';
        }

        if (null === $end) {
            $end = date('Y-m-d');
        }

        $results = $wpdb->get_results(
            $wpdb->prepare("
                SELECT `variation_id` AS `variation`, COUNT(`id`) AS `views`, COUNT(DISTINCT `user_id`) AS `unique`, SUM(`conversion`) AS `conversions`, `page_id` AS `page`
                FROM `{$wpdb->prefix}optimizepress_stats_views`
                WHERE `experiment_id` = %d AND DATE_FORMAT(`timestamp`, '%%Y-%%m-%%d') >= '%s' AND DATE_FORMAT(`timestamp`, '%%Y-%%m-%%d') <= '%s'
                GROUP BY `variation`
            ", $experimentId, $start, $end),
            OBJECT_K
        );

        return $results;
    }

    /**
     * Return best converting (conversion rate) alternative for given experiment.
     * @param  integer $experimentId
     * @return object
     */
    public function getExperimentBestAlternative($experimentId)
    {
        global $wpdb;

        $bestAlternative = $wpdb->get_row(
            $wpdb->prepare("
                SELECT `variation_id` AS `variation`, COUNT(`id`) AS `views`, COUNT(DISTINCT `user_id`) AS `unique`, SUM(`conversion`) AS `conversions`, (SUM(`conversion`) / COUNT(`id`) * 100) AS `conversion_rate`, `page_id` AS `page`
                FROM `{$wpdb->prefix}optimizepress_stats_views`
                WHERE `experiment_id` = %d
                GROUP BY `variation`
                ORDER BY `conversion_rate` DESC
                LIMIT 1
            ", $experimentId)
        );

        return $bestAlternative;
    }

    /**
     * Return aggregated stats for given page between given start and end dates.
     * @param  integer $pageId
     * @param  string $start  expects date('Y-m-d')
     * @param  string $end    expects date('Y-m-d')
     * @param  string $period
     * @return array
     */
    public function getPageAggregatedViews($pageId, $start, $end, $period = 'day')
    {
        switch ($period) {
            case 'month':
                $dateFormat = '%Y-%m';
                break;
            case 'week':
                $dateFormat = '%Y-%v';
                break;
            case 'day':
            default:
                $dateFormat = '%Y-%m-%d';
        }

        global $wpdb;

        $results = $wpdb->get_results(
            $wpdb->prepare("
                SELECT COUNT(id) AS views, COUNT(DISTINCT user_id) AS `unique`, SUM(conversion) AS conversions, DATE_FORMAT(timestamp, %s) AS `date`
                FROM `{$wpdb->prefix}optimizepress_stats_views`
                WHERE page_id = %d AND DATE_FORMAT(timestamp, '%%Y-%%m-%%d') >= '%s' AND DATE_FORMAT(timestamp, '%%Y-%%m-%%d') <= '%s'
                GROUP BY `date`
            ", $dateFormat, $pageId, $start, $end),
            OBJECT
        );

        return $results;
    }

    /**
     * Record "visit" conversion for given experiment and unique user ID.
     * @param  integer $experimentId
     * @param  string $userId
     * @return integer|boolean
     */
    public function recordVisitConversion($experimentId, $userId)
    {
        global $wpdb;

        return $wpdb->query(
            $wpdb->prepare(
                "UPDATE `{$wpdb->prefix}optimizepress_stats_views` SET `conversion` = 1 WHERE `experiment_id` = '%d' AND `user_id` = '%s' ORDER BY `timestamp` DESC LIMIT 1",
                $experimentId,
                $userId
            )
        );
    }

    /**
     * Record "optin" conversion for given record ID.
     * @param  integer $recordId
     * @return integer|boolean
     */
    public function recordOptinConversion($recordId)
    {
        global $wpdb;

        return $wpdb->query(
            $wpdb->prepare(
                "UPDATE `{$wpdb->prefix}optimizepress_stats_views` SET `conversion` = 1 WHERE `id` = '%d'",
                $recordId
            )
        );
    }

    /**
     * Record page view.
     * @param  integer $experimentId
     * @param  integer $pageId
     * @param  integer $templateId
     * @param  string $userId
     * @param  string $country
     * @param  integer $variationId
     * @param  string $timestamp
     * @return integer
     */
    public function recordPageview($experimentId, $pageId, $templateId, $userId, $country, $variationId, $timestamp)
    {
        global $wpdb;

        $wpdb->query($wpdb->prepare(
            "INSERT INTO `{$wpdb->prefix}optimizepress_stats_views` (`experiment_id`, `page_id`, `template_id`, `user_id`, `country`, `variation_id`, `timestamp`) VALUES (%d, %d, '%s', '%s', '%s', %d, '%s')",
            $experimentId,
            $pageId,
            $templateId,
            $userId,
            $country,
            $variationId,
            $timestamp
        ));

        return $wpdb->insert_id;
    }

    /**
     * Delete views resulting form given experiment.
     * @param  integer $experimentId
     * @return boolean
     */
    public function deleteExperimentViews($experimentId)
    {
        global $wpdb;
        // Delete its variants
        $wpdb->query($wpdb->prepare(
            "DELETE FROM `{$wpdb->prefix}optimizepress_stats_views` WHERE `experiment_id` = '%d'",
            $experimentId
        ));
    }
}