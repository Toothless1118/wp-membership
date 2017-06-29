<?php
/*
Plugin Name: OptimizePress Experiments
Plugin URI: http://www.optimizepress.com/
Version: 1.0.2
Description: Experiments and A/B tests for OptimizePress Live Editor pages
Author: OptimizePress
Author URI: http://www.optimizepress.com/
Text Domain: optimizepress-stats
Domain Path: /languages
*/

/*
 * If this file is called directly, abort
 */
if (! defined('WPINC')) {
    die;
}

define('OP_S_VERSION', '1.0.2');
define('OP_S_BASE_DIR', plugin_dir_path(__FILE__));
define('OP_S_BASE_URL', plugin_dir_url(__FILE__));
define('OP_S_PLUGIN_SLUG', plugin_basename(__FILE__));

class OptimizePress_Stats
{
    /**
     * Initialize the plugin by setting localization, filters, and administration functions.
     * @since     1.0.0
     */
    public function __construct()
    {
        if ($this->shouldLoadStats()) {
            $this->loadStats();
        }

        /*
         * Actions
         */
        add_action('init', array($this, 'loadPluginTextdomain'));

        /*
         * Maintenance hooks and filters
         */
        add_action('optimizepress_stats_ping_pong', 'op_sl_ping', 10, 2);
        add_filter('pre_set_site_transient_update_plugins', array($this, 'checkUpdate'));
        add_filter('site_transient_update_plugins', array($this, 'checkS3Update'));
    }

    /**
     * Load stats files
     * @return void
     */
    protected function loadStats()
    {
        require_once 'vendor/autoload.php';

        require_once 'src/Request.php';
        require_once 'src/Cookie.php';
        require_once 'src/Country.php';
        require_once 'src/Install.php';
        require_once 'src/User.php';
        require_once 'src/Settings.php';

        // require_once 'src/Remote/ClientInterface.php';
        require_once 'src/Strategy/Experiment/StrategyInterface.php';

        // Switch page
        require_once 'src/Charting/Helper.php';
        require_once 'src/Experiment.php';
        require_once 'src/Repository/Experiments.php';
        require_once 'src/Repository/Variants.php';
        require_once 'src/Repository/Views.php';
        require_once 'src/Strategy/Experiment/Random.php';
        require_once 'src/Strategy/Experiment/RoundRobin.php';

        // Experiments & stats UI
        require_once 'src/Screen/Ajax.php';
        require_once 'src/Screen/EditPageExperiment.php';
        require_once 'src/Screen/EditPageExperimentStats.php';
        require_once 'src/Screen/EditPagePageViewStats.php';
        require_once 'src/Screen/Experiments.php';
        require_once 'src/Screen/PageViews.php';
        require_once 'src/Screen/Statistics.php';
        require_once 'src/Screen/DashboardSection.php';

        // Record pagevisit
        require_once 'src/Conversion.php';
        require_once 'src/Pageview.php';
    }

    /**
     * Load the plugin text domain for translation.
     * @since    1.0.0
     */
    public function loadPluginTextdomain()
    {
        $domain = 'optimizepress-stats';
        $locale = apply_filters('plugin_locale', get_locale(), $domain);

        load_textdomain($domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo');
        load_plugin_textdomain($domain, FALSE, OP_S_BASE_DIR . '/lang/');
    }

    /**
     * Scheduling addon SL ping action
     * @return void
     */
    public static function activate()
    {
        if ( ! wp_next_scheduled('optimizepress_stats_ping_pong')) {
            wp_schedule_event(time(), 'daily', 'optimizepress_stats_ping_pong', array('optimizepress_stats', OP_S_VERSION));
        }
    }

    /**
     * Unscheduling addon SL ping action
     * @return void
     */
    public static function deactivate()
    {
        wp_clear_scheduled_hook('optimizepress_stats_ping_pong', array('optimizepress_stats', OP_S_VERSION));
    }

    /**
     * Check SL service for new version
     * @param array $transient existing WordPress transient array
     * @return bool|WP_Error
     */
    public function checkUpdate($transient)
    {
        if ( ! defined('OP_FUNC')) {
            return $transient;
        }

        if (!function_exists('op_sl_update')) {
            require_once OP_FUNC.'options.php';
            require_once OP_FUNC.'sl_api.php';
        }
        $apiResponse = op_sl_update('optimizepress_stats');

        if (is_wp_error($apiResponse)) {
            return $transient;
        }

        if (version_compare(OP_S_VERSION, $apiResponse->new_version, '<')) {
            $obj                    = new stdClass();
            $obj->name              = __('OptimizePress Experiments', 'optimizepress-stats');
            $obj->slug              = OP_S_PLUGIN_SLUG;
            $obj->version           = $apiResponse->new_version;
            $obj->new_version       = $apiResponse->new_version;
            $obj->homepage          = $apiResponse->url;
            $obj->url               = $apiResponse->url;
            $obj->download_url      = $apiResponse->package;
            $obj->package           = $apiResponse->package;
            $obj->requires          = '3.5';
            $obj->tested            = '4.6';
            $obj->sections          = array(
                'description'       => $apiResponse->section->description,
                'changelog'         => $apiResponse->section->changelog,
            );

            $transient->response[OP_S_PLUGIN_SLUG] = $obj;

            // set transient for 12 hours
            set_transient('optimizepress_stats_update', $obj, (HOUR_IN_SECONDS * 12));
        }
        return $transient;
    }

    /**
     * Fetches new presigned S3 link just before download will occur
     * @param  array $transient
     * @return array
     */
    public function checkS3Update($transient)
    {
        if ( ! defined('OP_FUNC')) {
            return $transient;
        }

        list($t1, $t2) = explode('/', OP_S_PLUGIN_SLUG);

        /*
         * We are only going on SL to get freshly presigned S3 link if the preconditions are OK
         */
        $do = false;
        if (isset($_SERVER['SCRIPT_NAME']) && strpos($_SERVER['SCRIPT_NAME'], 'update.php')
        && isset($_GET['action']) && $_GET['action'] === 'upgrade-plugin'
        && isset($_GET['plugin']) && $_GET['plugin'] === OP_S_PLUGIN_SLUG) {
            // single plugin upgrade
            $do = true;
        } elseif (isset($_SERVER['SCRIPT_NAME']) && strpos($_SERVER['SCRIPT_NAME'], 'update.php')
        && isset($_GET['action']) && $_GET['action'] === 'update-selected'
        && isset($_GET['plugins']) && in_array(OP_S_PLUGIN_SLUG, explode(',', $_GET['plugins']))) {
            // multi plugin upgrade
            $do = true;
        } elseif (isset($_SERVER['SCRIPT_NAME']) && strpos($_SERVER['SCRIPT_NAME'], 'plugins.php')
        && ((isset($_POST['action']) && $_POST['action'] === 'update-selected') || (isset($_POST['action2']) && $_POST['action2'] === 'update-selected'))
        && isset($_POST['checked']) && in_array(OP_S_PLUGIN_SLUG, $_POST['checked'])) {
            // multi plugin upgrade
            $do = true;
        } elseif (isset($_SERVER['SCRIPT_NAME']) && strpos($_SERVER['SCRIPT_NAME'], 'update-core.php')
        && isset($_GET['action']) && $_GET['action'] === 'do-plugin-upgrade'
        && isset($_POST['checked']) && in_array(OP_S_PLUGIN_SLUG, $_POST['checked'])) {
            // update from dashboard
            $do = true;
        } elseif (defined('DOING_AJAX') && DOING_AJAX
        && isset($_POST['action']) && $_POST['action'] === 'update-plugin'
        && isset($_POST['slug']) && $_POST['slug'] === $t1) {
            // AJAX update
            $do = true;
        }

        if (false === $do) {
            return $transient;
        }

        if (!function_exists('op_sl_update')) {
            require_once OP_FUNC . 'options.php';
            require_once OP_FUNC . 'sl_api.php';
        }
        $apiResponse = op_sl_update('optimizepress_stats');

        if (is_wp_error($apiResponse)) {
            return $response;
        }

        $obj                    = new stdClass();
        $obj->name              = __('OptimizePress Experiments', 'optimizepress-stats');
        $obj->slug              = OP_S_PLUGIN_SLUG;
        $obj->version           = $apiResponse->new_version;
        $obj->new_version       = $apiResponse->new_version;
        $obj->homepage          = $apiResponse->url;
        $obj->url               = $apiResponse->url;
        $obj->download_url      = $apiResponse->s3_package;
        $obj->package           = $apiResponse->s3_package;
        $obj->requires          = '3.5';
        $obj->tested            = '4.6';
        $obj->sections          = array(
            'description'       => $apiResponse->section->description,
            'changelog'         => $apiResponse->section->changelog,
        );

        $transient->response[OP_S_PLUGIN_SLUG] = $obj;

        return $transient;
    }

    /**
     * Check whether to load stats & A/B tests package.
     * @return void
     */
    protected function shouldLoadStats()
    {
        $customer   = get_option('optimizepress_sl_customer');
        $shouldLoad = false;

        if (false !== $customer) {
            $customer = maybe_unserialize($customer);
            if (is_array($customer) && isset($customer['pack']) && $customer['pack'] === 'plus') {
                $shouldLoad = true;
            }
        }

        return $shouldLoad;
    }
}

// Registering activation and deactivation hooks
register_activation_hook(__FILE__, array('OptimizePress_Stats', 'activate'));
register_deactivation_hook(__FILE__, array('OptimizePress_Stats', 'deactivate'));

new OptimizePress_Stats;