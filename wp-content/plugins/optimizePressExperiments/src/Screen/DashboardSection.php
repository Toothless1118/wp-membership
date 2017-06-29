<?php

/**
 * Attach stats settings on Dashboard - General settings screen.
 */
class OptimizePressStats_Screen_DashboardSection
{
    /**
     * Init filters.
     */
    public function __construct()
    {
        add_filter('op_edit_sections_global_settings', array($this, 'addDashboardSection'));
    }

    /**
     * Add stats settings section to dasboard - general settings screen.
     * @param array $sections
     * @return array
     */
    public function addDashboardSection($sections)
    {
        $sections['stats'] = array(
            'title'         => __('Statistics', 'optimizepress-stats'),
            'action'        => array($this, 'showSection'),
            'save_action'   => array($this, 'saveSection')
        );

        return $sections;
    }

    /**
     * Display settings form.
     * @return void
     */
    public function showSection()
    {
    ?>
        <div class="op-bsw-grey-panel-content op-bsw-grey-panel-no-sidebar cf">
            <label for="op_sections_stats_register_pageview" class="form-title"><?php _e("Stats for ALL LiveEditor pages", 'optimizepress-stats'); ?></label>
            <p class="microcopy"><?php _e('Collect pageview stats for all LiveEditor pages on your site, not just current experiment pages (will increase your database table size)', 'optimizepress-stats'); ?></p>
            <?php op_checkbox_field('op[sections][stats][register_pageview]', 1, checked(1, op_get_option('stats_register_pageview'), false)); ?> <?php _e('Collect stats', 'optimizepress-stats'); ?>

            <label for="op_sections_stats_register_admin" class="form-title"><?php _e("Stats for admin users", 'optimizepress-stats'); ?></label>
            <p class="microcopy"><?php _e('Collect pageview stats for admin user views', 'optimizepress-stats'); ?></p>
            <?php op_checkbox_field('op[sections][stats][register_admin]', 1, checked(1, op_get_option('stats_register_admin'), false)); ?> <?php _e('Collect stats', 'optimizepress-stats'); ?>

            <div class="clear"></div>
        </div>
    <?php
    }

    /**
     * Save stats settings to wp_options table.
     * @param  array $op
     * @return void
     */
    public function saveSection($op)
    {
        if (isset($op['stats']) && ! empty($op['stats'])) {
            // Register pageview
            if (isset($op['stats']['register_pageview']) && ! empty($op['stats']['register_pageview'])) {
                op_update_option('stats_register_pageview', $op['stats']['register_pageview']);
            } else {
                op_delete_option('stats_register_pageview');
            }

            // Admin users
            if (isset($op['stats']['register_admin']) && ! empty($op['stats']['register_admin'])) {
                op_update_option('stats_register_admin', $op['stats']['register_admin']);
            } else {
                op_delete_option('stats_register_admin');
            }
        }
    }
}

new OptimizePressStats_Screen_DashboardSection;