<?php

/**
 * Handle flow hooks attached to stats settings.
 */
class OptimizePressStats_Settings
{
    /**
     * Init hooks and filters.
     */
    public function __construct()
    {
        add_filter('optimizepress-stats/show-stats', array($this, 'disableStats'));
        add_filter('optimizepress-stats/show-page-stats', array($this, 'disablePageStats'));
        add_filter('optimizepress-stats/register-pageview', array($this, 'disablePageviewRecording'), 10, 2);
    }

    /**
     * Check "optimizepress_stats_show_stats_section" option whether to show main stats section.
     * @param  boolean $status
     * @return boolean
     */
    public function disableStats($status)
    {
        if ((int) get_option('optimizepress_stats_register_pageview') === 1) {
            return true;
        }

        return false;
    }

    /**
     * Check "optimizepress_stats_show_page_stats" option whether to show pageview stats on edit page screen.
     * @param  boolean $status
     * @return boolean
     */
    public function disablePageStats($status)
    {
        if ((int) get_option('optimizepress_stats_register_pageview') === 1) {
            return true;
        }

        return false;
    }

    /**
     * Disables pageview recording for admin users (if "optimizepress_stats_register_admin" option
     * is checked) and if "optimizepress_stats_register_pageview" option is set to only gather data
     * for pages that are part of active experiment.
     * @param  boolean $status
     * @param  integer $experimentId
     * @return boolean
     */
    public function disablePageviewRecording($status, $experimentId)
    {
        if (current_user_can('manage_options') && (int) get_option('optimizepress_stats_register_admin') !== 1) {
            return false;
        }

        if ((int) $experimentId === 0 && (int) get_option('optimizepress_stats_register_pageview') !== 1) {
            return false;
        }

        return true;
    }
}

new OptimizePressStats_Settings;