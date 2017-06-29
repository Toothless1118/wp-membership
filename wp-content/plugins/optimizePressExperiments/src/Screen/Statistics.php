<?php

class OptimizePressStats_Screen_Statistics
{
    /**
     * Init hooks and filters.
     */
    public function __construct()
    {
        add_action('admin_menu', array($this, 'adminMenu'), 100);
        add_action('admin_enqueue_scripts', array($this, 'enqueueScripts'));
    }

    /**
     * Enqueue scripts and styles for stats user interface.
     * @param  string $hook
     * @return void
     */
    public function enqueueScripts($hook)
    {
        if ('optimizepress_page_optimizepress-statistics' !== $hook && 'post.php' !== $hook) {
            return;
        }

        wp_enqueue_style('optimizepress-stats-ui', OP_S_BASE_URL . 'css/ui' . OP_SCRIPT_DEBUG . '.css', array(), OP_S_VERSION);
    }

    /**
     * Add submenu item under the OP.
     * @return void
     */
    public function adminMenu()
    {
        // Remove stats screen from the OP theme/plugin
        remove_submenu_page(OP_SN, OP_SN . '-stats');

        // Add new stats submenu page
        add_submenu_page(OP_SN, __('Experiments', 'optimizepress-stats'), __('Experiments', 'optimizepress-stats'), 'edit_theme_options', OP_SN . '-statistics', array($this, 'renderScreen'));
    }

    /**
     * Render stats screen.
     * @return void
     */
    public function renderScreen()
    {
        $activeSection = isset($_GET['section']) ? sanitize_text_field($_GET['section']) : 'experiments';
        ?>
<div class="op-bsw-settings">
    <div class="op-bsw-wizard op-bsw-wizard-full">
    <div class="op-bsw-content cf">
        <div class="op-bsw-header cf op-bsw-header-full">
            <div class="op-logo">
                <img src="<?php op_img() ?>logo-optimizepress.png" alt="<?php esc_attr_e('OptimizePress', 'optimizepress-stats'); ?>" height="50" class="animated flipInY" />
            </div>
            <ul>
                <li>
                    <a href="#" target="_blank">
                        <img src="<?php echo OP_IMG ?>live_editor/le_help_bg.png" onmouseover="this.src='<?php echo OP_IMG ?>live_editor/le_help_icon.png'" onmouseout="this.src='<?php echo OP_IMG ?>live_editor/le_help_bg.png'" alt="<?php esc_attr_e('Help', 'optimizepress-stats') ?>" class="tooltip animated pulse" title="<?php esc_attr_e('Help', 'optimizepress-stats') ?>" />
                    </a>
                </li>
            </ul>
            <script src='<?php echo OP_JS ?>tooltipster.min.js'></script>
            <script>
            opjq(document).ready(function($) {
                $('.tooltip').tooltipster({animation: 'grow'});
            });
            </script>
        </div>
        <div class="op-bsw-main-content">
            <div class="op-info-box">
                <p>
                    <strong><?php _e('Need help testing?','optimizepress-stats');?></strong> <a class="op-info-box-getting-started-link" id="js-op-info-box-getting-started-link" href="https://optimizepress.zendesk.com/hc/en-us/sections/206281408-OptimizePress-Experiments" target="_blank"><?php _e('Click here to watch the training videos','optimizepress-stats');?></a>
                </p>
                <div class="op-video-container">
                    <div class="op-getting-started-video">
                        <iframe class="op-getting-started-iframe" src="https://player.vimeo.com/video/125436273?color=ffffff&title=0&byline=0&portrait=0" width="850" height="478" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
                    </div>
                </div>
            </div>

            <div class="op-info-box-panel">
                <ul class="op-info-box-list-icons">
                    <li<?php if ($activeSection === 'experiments') echo ' class="active"'; ?>>
                        <a href="<?php echo esc_url(add_query_arg('section', 'experiments')); ?>" class="op-icon op-icon-experiments"><?php _e('Experiments','optimizepress-stats'); ?></a>
                    </li>
                    <?php
                        // Check whether to show pageview statistics
                        if (apply_filters('optimizepress-stats/show-stats', true)) :
                    ?>
                    <li<?php if ($activeSection === 'stats') echo ' class="active"'; ?>>
                        <a href="<?php echo esc_url(add_query_arg('section', 'stats')); ?>" class="op-icon op-icon-statistics"><?php _e('Stats','optimizepress-stats'); ?></a>
                    </li>
                    <?php
                        endif;
                    ?>
                </ul>
            </div>
            <?php do_action('optimizepress_stats/statistics_page', $activeSection); ?>
        </div>
    </div>
</div>
<script type="text/javascript" src="//www.google.com/jsapi"></script>
<script type="text/javascript">
google.load('webfont','1');
</script>
        <?php
    }
}

new OptimizePressStats_Screen_Statistics;