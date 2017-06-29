<?php
/*
Plugin Name: OptimizePress PlusPack
Plugin URI: http://www.optimizepress.com/
Version: 1.1.3
Description: Additional features for pro & plus packages
Author: OptimizePress
Author URI: http://www.optimizepress.com/
*/

/*
 * If this file is called directly, abort
 */
if (! defined('WPINC')) {
    die;
}

define('OPPP_VERSION', '1.1.3');
define('OPPP_BASE_DIR', plugin_dir_path(__FILE__));
define('OPPP_BASE_URL', plugin_dir_url(__FILE__));
define('OPPP_PLUGIN_SLUG', plugin_basename(__FILE__));

class Oppp
{
    /**
     * Instance of this class.
     * @since    1.0.0
     * @var      Oppp
     */
    protected static $instance = null;

    /**
     * Key (used when pinging SL platform)
     * @var string
     */
    protected static $key = 'oppp';

    /**
     * Slug of the plugin screen.
     * @since    1.0.0
     * @var      string
     */
    protected $pluginScreenHookSuffix = null;

    /**
     * Initialize the plugin by setting localization, filters, and administration functions.
     * @since     1.0.0
     */
    private function __construct()
    {

        /*
         * Actions
         */
        add_action('init', array($this, 'loadPluginTextdomain'));
        add_action('admin_enqueue_scripts', array($this, 'enqueueScripts'));

        add_action('admin_enqueue_scripts', array($this, 'enqueueAllScripts'));
        add_action('wp_enqueue_scripts', array($this, 'enqueueAllScripts'));

        add_action('admin_print_styles', array($this, 'enqueueAllStyles'));
        add_action('wp_print_styles', array($this, 'enqueueAllStyles'));
        // add_action('admin_enqueue_scripts', array($this, 'enqueueAllStyles'));
        // add_action('wp_enqueue_scripts', array($this, 'enqueueAllStyles'));

        // Require element files
        $this->loadElements();

        /*
         * Maintenance hooks and filters
         */
        add_action('oppp_ping_pong', 'op_sl_ping', 10, 2);
        add_filter('pre_set_site_transient_update_plugins', array($this, 'checkUpdate'));
        add_filter('site_transient_update_plugins', array($this, 'checkS3Update'));

        // Take over the Plugin info screen
        add_filter('plugins_api', array($this, 'pluginScreen'), 10, 3);
        add_action('admin_notices', array($this, 'updateNagScreen'));
        add_action('admin_init', array($this, 'loadThickbox'));

        // OP Javascript object
        add_filter('optimizepress-script-localize', array($this, 'javascriptData'));

        // Add css style to tinymce
        add_filter('mce_css', array($this, 'addTinyMceEditorStyles'));

        add_action('admin_print_scripts-post-new.php', array($this, 'addEditPostStyles'));
        add_action('admin_print_scripts-post.php', array($this, 'addEditPostStyles'));

        // Add js to tinymce
        add_action('wp_enqueue_editor', array($this, 'addTinyMceEditorScripts'));
    }

    /**
     * Load element files
     * @return void
     */
    protected function loadElements()
    {
        // New elements
        require_once 'src/Elements/Slider.php';
        require_once 'src/Elements/VideoBackground.php';
        require_once 'src/Elements/AnimatedElements.php';
        require_once 'src/Elements/AdvancedHeadline.php';
        require_once 'src/Elements/EvergreenCountdown.php';
        require_once 'src/Elements/WpCommentsDuplicator.php';
        require_once 'src/Elements/ScrollEnhancer.php';
        require_once 'src/Elements/TestimonialSlider.php';
        require_once 'src/Elements/ComparisonTable.php';
        require_once 'src/Elements/ProductShowcase.php';

        // Overriden elements
        require_once 'src/Elements/GuaranteeBox.php';
        require_once 'src/Elements/CountdownTimer.php';
        require_once 'src/Elements/PricingTable.php';
        require_once 'src/Elements/FileDownload.php';
        require_once 'src/Elements/ProgressBar.php';
        require_once 'src/Elements/SocialSharing.php';
        require_once 'src/Elements/SocialNetworking.php';
        require_once 'src/Elements/CalendarDate.php';
        require_once 'src/Elements/QNAElement.php';
        require_once 'src/Elements/NewsBar.php';
        require_once 'src/Elements/OptinBox.php';
        require_once 'src/Elements/Tabs.php';
        require_once 'src/Elements/Testimonials.php';

        // Additional module styles
        require_once 'src/Modules/SignupForm.php';
    }

    /**
     * Return an instance of this class.
     * @since     1.0.0
     * @return    Oppp
     */
    public static function getInstance()
    {
        // If the single instance hasn't been set, set it now.
        if (null == self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Load the plugin text domain for translation.
     * @since    1.0.0
     */
    public function loadPluginTextdomain()
    {
        $domain = OP_SN . '-plus-pack';
        $locale = apply_filters('plugin_locale', get_locale(), $domain);

        load_textdomain($domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo');
        load_plugin_textdomain($domain, FALSE, OPPP_BASE_DIR . '/lang/');
    }

    /**
     * Enqueues JS file needed for AJAX log calls
     * @since  1.0.0
     * @param  string $hookSuffix
     * @return void
     */
    public function enqueueScripts($hookSuffix)
    {
        if ($this->pluginScreenHookSuffix !== $hookSuffix) {
            return;
        }
    }

    /**
     * Enqueues all scripts for production
     * @since  1.0.0
     * @return void
     */
    public function enqueueAllScripts()
    {
        if (OP_SCRIPT_DEBUG !== '') {
            if (defined('OP_LIVEEDITOR')) {
                // Localize the OPPP plugin url, it's needed later
                wp_localize_script( OP_SN . 'plus-pack-js-back-all', 'oppp_path', OPPP_BASE_URL );

                wp_enqueue_script(OP_SN . 'plus-pack-js-back-all', OPPP_BASE_URL . 'js/elements/opplus-back-all' . OP_SCRIPT_DEBUG . '.js', array(OP_SN . '-op-jquery-base-all', 'jquery-ui-accordion'), OPPP_VERSION, true);
            } else {
                wp_enqueue_script(OP_SN . 'plus-pack-js-front-all', OPPP_BASE_URL . 'js/elements/opplus-front-all' . OP_SCRIPT_DEBUG . '.js', array(OP_SN . '-op-jquery-base-all', 'jquery-ui-accordion', OP_SN.'-op-front-all'), OPPP_VERSION, true);
            }
        }
    }

    /**
     * Enqueues all styles for production
     * @since  1.0.0
     * @return void
     */
    public function enqueueAllStyles()
    {
        if (OP_SCRIPT_DEBUG !== '') {
            wp_enqueue_style(OP_SN . 'plus-pack-css-front-all', OPPP_BASE_URL . 'css/elements/opplus-front-all' . OP_SCRIPT_DEBUG . '.css', array(), OPPP_VERSION);

            if (defined('OP_LIVEEDITOR')) {
                wp_enqueue_style(OP_SN . 'plus-pack-css-back-all', OPPP_BASE_URL . 'css/elements/opplus-back-all' . OP_SCRIPT_DEBUG . '.css', array(OP_SN . 'plus-pack-css-front-all'), OPPP_VERSION);
            }
        }
    }

    /**
     * Scheduling addon SL ping action
     * @return void
     */
    public static function activate()
    {
        if (!wp_next_scheduled('oppp_ping_pong')) {
            wp_schedule_event(time(), 'daily', 'oppp_ping_pong', array('oppp', OPPP_VERSION));
        }
    }

    /**
     * Unscheduling addon SL ping action
     * @return void
     */
    public static function deactivate()
    {
        wp_clear_scheduled_hook('oppp_ping_pong', array('oppp', OPPP_VERSION));
    }

    /**
     * Add OPPP version to a OptimizePress JavaScript object
     * @param  array $data
     * @return array
     */
    public function javascriptData($data)
    {
        $data['oppp']['version'] = OPPP_VERSION;
        return $data;
    }

    /**
     * Check and display nag notice for plugin update.
     * @return void
     */
    public function updateNagScreen()
    {
        //PLUGIN
        $response = get_transient('op_opppplugin_update');

        $plugin_version = OPPP_VERSION;
        $plugin_slug = OPPP_PLUGIN_SLUG;
        list ($t1, $t2) = explode('/', $plugin_slug);
        $pluginName = str_replace('.php', '', $t2);

        if (false === $response)
            return;

        $update_url = wp_nonce_url( 'update.php?action=upgrade-plugin&amp;plugin=' . urlencode($plugin_slug), 'upgrade-plugin_' . $plugin_slug);
        $update_onclick = '';

        if (isset($response->new_version) &&  version_compare( $plugin_version, $response->new_version, '<' ) ) {
            echo '<div id="update-nag">';
            printf( '<strong>%1$s %2$s</strong> is available. <a href="%3$s" class="thickbox" title="%4s">Check out what\'s new</a> or <a href="%5$s"%6$s>update now</a>.',
                'OptimizePress PlusPack',
                $response->new_version,
                admin_url('plugin-install.php?tab=plugin-information&plugin=' . OPPP_PLUGIN_SLUG  .'&section=changelog&TB_iframe=true&width=640'),
                'OptimizePress PlusPack',
                $update_url,
                $update_onclick
            );
            echo '</div>';
            echo '<div id="' . $pluginName . '_' . 'changelog" style="display:none;">';
            echo wpautop($response->sections['changelog']);
            echo '</div>';
        }
    }

    /**
     * Server transient object for plugin install API request.
     * @param  mixed $result
     * @param  string $action
     * @param  array $args
     * @return mixed
     */
    public function pluginScreen($result, $action, $args)
    {
        if ($args->slug !== OPPP_PLUGIN_SLUG) {
            return false;
        }

        $obj = get_transient('op_opppplugin_update');

        if (false !== $obj) {
            $result = $obj;
        }

        return $result;
    }

    /**
     * Load thickbox.
     * @return void
     */
    public function loadThickbox()
    {
        add_thickbox();
    }


    /**
     * Check SL service for new version
     * @param array $transient existing WordPress transient array
     * @return bool|WP_Error
     */
    public function checkUpdate($transient)
    {
        if (!defined('OP_FUNC')) {
            return $transient;
        }

        if (!function_exists('op_sl_update')) {
            require_once OP_FUNC.'options.php';
            require_once OP_FUNC.'sl_api.php';
        }
        $apiResponse = op_sl_update('oppp');

        if (is_wp_error($apiResponse)) {
            return $transient;
        }

        if (version_compare(OPPP_VERSION, $apiResponse->new_version, '<')) {
            $obj                    = new stdClass();
            $obj->name              = __('OptimizePress PlusPack', 'optimizepress-plus-pack');
            $obj->slug              = OPPP_PLUGIN_SLUG;
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

            $transient->response[OPPP_PLUGIN_SLUG] = $obj;

            // set transient for 12 hours
            set_transient('op_opppplugin_update', $obj, (HOUR_IN_SECONDS * 12));
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
        if (!defined('OP_FUNC')) {
            return $transient;
        }

        list($t1, $t2) = explode('/', OPPP_PLUGIN_SLUG);

        /*
         * We are only going on SL to get freshly presigned S3 link if the preconditions are OK
         */
        $do = false;
        if (isset($_SERVER['SCRIPT_NAME']) && strpos($_SERVER['SCRIPT_NAME'], 'update.php')
        && isset($_GET['action']) && $_GET['action'] === 'upgrade-plugin'
        && isset($_GET['plugin']) && $_GET['plugin'] === OPPP_PLUGIN_SLUG) {
            // single plugin upgrade
            $do = true;
        } elseif (isset($_SERVER['SCRIPT_NAME']) && strpos($_SERVER['SCRIPT_NAME'], 'update.php')
        && isset($_GET['action']) && $_GET['action'] === 'update-selected'
        && isset($_GET['plugins']) && in_array(OPPP_PLUGIN_SLUG, explode(',', $_GET['plugins']))) {
            // multi plugin upgrade
            $do = true;
        } elseif (isset($_SERVER['SCRIPT_NAME']) && strpos($_SERVER['SCRIPT_NAME'], 'plugins.php')
        && ((isset($_POST['action']) && $_POST['action'] === 'update-selected') || (isset($_POST['action2']) && $_POST['action2'] === 'update-selected'))
        && isset($_POST['checked']) && in_array(OPPP_PLUGIN_SLUG, $_POST['checked'])) {
            // multi plugin upgrade
            $do = true;
        } elseif (isset($_SERVER['SCRIPT_NAME']) && strpos($_SERVER['SCRIPT_NAME'], 'update-core.php')
        && isset($_GET['action']) && $_GET['action'] === 'do-plugin-upgrade'
        && isset($_POST['checked']) && in_array(OPPP_PLUGIN_SLUG, $_POST['checked'])) {
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
        $apiResponse = op_sl_update('oppp');

        if (is_wp_error($apiResponse)) {
            return $response;
        }

        $obj                    = new stdClass();
        $obj->name              = __('OptimizePress PlusPack', 'optimizepress-plus-pack');
        $obj->slug              = OPPP_PLUGIN_SLUG;
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

        $transient->response[OPPP_PLUGIN_SLUG] = $obj;

        // set transient for 12 hours
        set_transient('op_opppplugin_update', $obj, (HOUR_IN_SECONDS * 12));

        return $transient;
    }

    /**
     * Add styles to TinyMce editor.
     * @param array $stylesheets
     * @return array
     */
    function addTinyMceEditorStyles($stylesheets)
    {
        // Utilize only on page edit screen
        if ($this->isViableToUse() && defined('OPPP_VERSION')) {

            // We just load the entire css at the moment.
            // if (OP_SCRIPT_DEBUG === '') {
            //     $files = glob(__dir__.'/css/elements/*.css');
            //     preg_match_all('/[^\\|\/]*\.css/m', implode("\n", $files), $fileNamesWithExtension);
            //     foreach ($fileNamesWithExtension[0] as $fileName) {
            //         if (false !== strpos($fileName, '.min')) {
            //             $temp = explode(".", $fileName);
            //             $stylesheets .= ',' . OPPP_BASE_URL . 'css/elements/' . $temp[0] . OP_SCRIPT_DEBUG . '.css';
            //         }
            //     }
            // } else {

            $stylesheets .= ',' . OPPP_BASE_URL . 'css/elements/opplus-front-all' . OP_SCRIPT_DEBUG . '.css';
            $stylesheets .= ',' . OPPP_BASE_URL . 'css/elements/opplus-back-all' . OP_SCRIPT_DEBUG . '.css';

            // }
            return $stylesheets;
        }
        // add_editor_style( OP_ASSETS_URL .'default' . OP_SCRIPT_DEBUG . '.css');
    }

    /**
     * Add styles to TinyMce editor.
     * @param array $stylesheets
     * @return array
     */
    function addEditPostStyles($stylesheets)
    {
        if ($this->isViableToUse() && defined('OPPP_VERSION')) {
            wp_enqueue_style(OP_SN . 'plus-pack-css-front-all', OPPP_BASE_URL . 'css/elements/opplus-front-all' . OP_SCRIPT_DEBUG . '.css', array(), OPPP_VERSION);
            wp_enqueue_style(OP_SN . 'plus-pack-css-back-all', OPPP_BASE_URL . 'css/elements/opplus-back-all' . OP_SCRIPT_DEBUG . '.css', array(), OPPP_VERSION);
        }
    }


    /**
     * Add styles to TinyMce editor.
     * @param array $stylesheets
     * @return array
     */
    function addTinyMceEditorScripts($scripts)
    {
        if ($this->isViableToUse() && defined('OPPP_VERSION')) {
            // wp_enqueue_script(OP_SN . '-editor-shortcodes', OP_JS . 'editor_shortcodes' . OP_SCRIPT_DEBUG . '.js', false, OP_VERSION, true);
            // op_enqueue_base_scripts();
            // op_enqueue_frontend_scripts();
            // wp_localize_script( OP_SN . 'plus-pack-js-back-all', 'oppp_path', OPPP_BASE_URL );
            // wp_enqueue_script(OP_SN . 'plus-pack-js-back-all', OPPP_BASE_URL . 'js/elements/opplus-back-all' . OP_SCRIPT_DEBUG . '.js', array(OP_SN . '-op-jquery-base-all', 'jquery-ui-accordion'), OPPP_VERSION, true);
            wp_enqueue_script(OP_SN . 'plus-pack-js-back-all', OPPP_BASE_URL . 'js/elements/opplus-back-all' . OP_SCRIPT_DEBUG . '.js', array(OP_SN . '-op-jquery-base-all', 'jquery-ui-accordion'), OPPP_VERSION, true);
        }
    }

    /**
     * Check if we are on the screen that should utilize editor shortcodes.
     * This way we won't have any issues with LE TinyMce instances.
     *
     * @return boolean
     */
    protected function isViableToUse()
    {
	    // in some cases we received get_current_screen is not defined
	    if (!function_exists('get_current_screen')) {
		    require_once(ABSPATH . 'wp-admin/includes/screen.php');
	    }

	    $screen = get_current_screen();

        // Utilize only on page edit screen
        if ($screen instanceof WP_Screen && $screen->base === 'post' && ($screen->post_type === 'page' || $screen->post_type === 'post')) {
            return true;
        }

        return false;
    }
}

// Registering activation and deactivation hooks
register_activation_hook(__FILE__, array('Oppp', 'activate'));
register_deactivation_hook(__FILE__, array('Oppp', 'deactivate'));

// Initializing plugin
add_action('plugins_loaded', array('Oppp', 'getInstance'));
