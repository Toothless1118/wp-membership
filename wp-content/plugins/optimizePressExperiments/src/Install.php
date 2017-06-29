<?php

class OptimizePressStats_Install
{
    /**
     * Initialize hooks
     */
    public function __construct()
    {
        // Actions
        add_action('admin_init', array($this, 'addStatsTables'));
    }

    /**
     * Add stats tables
     * @return void
     */
    public function addStatsTables()
    {
        if (version_compare(get_option('optimizepress_stats_db_version', '0'), '1.0.0', '<')) {
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

            global $wpdb;

            // Experiments table
            $sql = "CREATE TABLE `{$wpdb->prefix}optimizepress_stats_experiments` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `page_id` bigint(20) unsigned NOT NULL,
                `name` varchar(200) NOT NULL DEFAULT '',
                `description` TEXT DEFAULT NULL,
                `goal_type` varchar(20) NOT NULL DEFAULT 'optin',
                `goal_page_id` bigint(20) unsigned NOT NULL DEFAULT '0',
                `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
                `start_date` date DEFAULT NULL,
                `end_date` date DEFAULT NULL,
                PRIMARY KEY(`id`)
            );";

            dbDelta($sql);

            // Experiment variants table
            $sql = "CREATE TABLE `{$wpdb->prefix}optimizepress_stats_experiment_variants` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `experiment_id` bigint(20) unsigned NOT NULL,
                `page_id` bigint(20) unsigned NOT NULL,
                `original_page_id` bigint(20) unsigned NOT NULL,
                `name` varchar(200) NOT NULL DEFAULT '',
                PRIMARY KEY(`id`)
            );";

            dbDelta($sql);

            // Stats table
            $sql = "CREATE TABLE `{$wpdb->prefix}optimizepress_stats_views` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `experiment_id` bigint(20) unsigned NOT NULL DEFAULT '0',
                `page_id` bigint(20) unsigned NOT NULL,
                `user_id` varchar(50) NOT NULL DEFAULT '',
                `template_id` varchar(100) NULL DEFAULT NULL,
                `variation_id` bigint(20) unsigned NOT NULL,
                `timestamp` datetime DEFAULT NULL,
                `country` char(2) NOT NULL DEFAULT '',
                `conversion` tinyint(1) unsigned NOT NULL DEFAULT '0',
                PRIMARY KEY (`id`)
            );";

            dbDelta($sql);

            // Aggregate sent to remote status
            $sql = "CREATE TABLE `{$wpdb->prefix}optimizepress_stats_remote_status` (
                `date` date DEFAULT NULL,
                `status` varchar(50) NOT NULL DEFAULT '',
                `timestamp` datetime DEFAULT NULL,
                PRIMARY KEY (`date`)
            );";

            dbDelta($sql);

            $wpdb->insert(
                $wpdb->prefix . 'optimizepress_stats_remote_status',
                array(
                    'date'      => date('Y-m-d'),
                    'status'    => 'start',
                    'timestamp' => date('Y-m-d H:i:s')
                ),
                array(
                    '%s',
                    '%s',
                    '%s'
                )
            );

            update_option('optimizepress_stats_db_version', '1.0.0');
        }
    }
}

new OptimizePressStats_Install();