<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the dashboard.
 *
 * @link       http://mythemeshop.com
 * @since      1.0.0
 *
 * @package    MTSNB
 * @subpackage MTSNB/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, dashboard-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    MTSNB
 * @subpackage MTSNB/includes
 * @author     Your Name <email@example.com>
 */
class MTSNB {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      MTSNB_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the Dashboard and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'mts-notification-bar';
		$this->version = '1.1.5';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->define_shared_hooks();
		$this->reset_cookies_bulk_action();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - MTSNB_Loader. Orchestrates the hooks of the plugin.
	 * - MTSNB_i18n. Defines internationalization functionality.
	 * - MTSNB_Admin. Defines all hooks for the dashboard.
	 * - MTSNB_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-mts-notification-bar-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-mts-notification-bar-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the Dashboard.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-mts-notification-bar-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-mts-notification-bar-public.php';

		/**
		 * The class responsible for defining all actions that occur in both sides of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-mts-notification-bar-shared.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/custom-bulk-actions.php';

		$this->loader = new MTSNB_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the MTSNB_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new MTSNB_i18n();
		$plugin_i18n->set_domain( $this->get_plugin_name() );

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Register all of the hooks related to the dashboard functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new MTSNB_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_init', $plugin_admin, 'check_version' );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		// Add the options page and dashboard menu item.
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'plugin_admin_menu' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'settings_api_init' );
		// Register our post type
		$this->loader->add_action( 'init', $plugin_admin, 'mts_notification_cpt' );
		// Add columns to post type tables
		$this->loader->add_filter( 'manage_mts_notification_bar_posts_columns', $plugin_admin, 'mtsnb_ad_columns_head', 10 );
		$this->loader->add_action( 'manage_mts_notification_bar_posts_custom_column', $plugin_admin, 'mtsnb_ad_column_content', 10, 2 );
		// Bar updated messages
		$this->loader->add_filter( 'post_updated_messages', $plugin_admin, 'mtsnb_update_messages' );
		// Metaboxes
		$this->loader->add_action( 'add_meta_boxes', $plugin_admin, 'add_custom_meta_box' );
		$this->loader->add_action( 'save_post', $plugin_admin, 'save_custom_meta' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'set_select_options' );
		// Add preview button to poblish metabox
		$this->loader->add_action( 'post_submitbox_misc_actions', $plugin_admin, 'add_preview_button' );

		// Force notification bar metabox
		$this->loader->add_action( 'add_meta_boxes', $plugin_admin, 'mtsnb_select_metabox_insert' );
		$this->loader->add_action( 'save_post', $plugin_admin, 'mtsnb_select_metabox_save' );
		$this->loader->add_action( 'wp_ajax_mtsnb_get_bars', $plugin_admin, 'mtsnb_get_bars' );
		$this->loader->add_action( 'wp_ajax_mtsnb_get_bar_titles', $plugin_admin, 'mtsnb_get_bar_titles' );

		// Newsletter lists
		$this->loader->add_action( 'wp_ajax_mtsnb_get_mailchimp_lists', $plugin_admin, 'get_mailchimp_lists' );
		$this->loader->add_action( 'wp_ajax_mtsnb_get_aweber_lists', $plugin_admin, 'get_aweber_lists' );
		$this->loader->add_action( 'wp_ajax_mtsnb_get_getresponse_lists', $plugin_admin, 'get_getresponse_lists' );
		$this->loader->add_action( 'wp_ajax_mtsnb_get_campaignmonitor_lists', $plugin_admin, 'get_campaignmonitor_lists' );
		$this->loader->add_action( 'wp_ajax_mtsnb_update_campaignmonitor_lists', $plugin_admin, 'update_campaignmonitor_lists' );
		$this->loader->add_action( 'wp_ajax_mtsnb_get_madmimi_lists', $plugin_admin, 'get_madmimi_lists' );

		// Reset A/B test stats
		$this->loader->add_action( 'wp_ajax_mtsnb_reset_ab_stats', $plugin_admin, 'reset_ab_stats' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new MTSNB_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'init', $plugin_public, 'add_image_sizes' );

		$this->loader->add_action( 'wp_ajax_mtsnb_add_email', $plugin_public, 'add_email' );
		$this->loader->add_action( 'wp_ajax_nopriv_mtsnb_add_email', $plugin_public, 'add_email' );

		$this->loader->add_action( 'wp_ajax_mtsnb_add_impression', $plugin_public, 'add_impression' );
		$this->loader->add_action( 'wp_ajax_nopriv_mtsnb_add_impression', $plugin_public, 'add_impression' );
		$this->loader->add_action( 'wp_ajax_mtsnb_add_click', $plugin_public, 'add_click' );
		$this->loader->add_action( 'wp_ajax_nopriv_mtsnb_add_click', $plugin_public, 'add_click' );
	}

	/**
	 * Register all of the hooks related to both public and dashboard functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_shared_hooks() {

		$plugin_shared = new MTSNB_Shared( $this->get_plugin_name(), $this->get_version() );

		// get/set bar settings
		$this->loader->add_action( 'wp', $plugin_shared, 'get_notification_bar_data' );
		// Display bar on front end
		$this->loader->add_action( 'wp_footer', $plugin_shared, 'display_bar' );
		// Display hidden divs needed for "Show after N times" condition
		$this->loader->add_action( 'wp_footer', $plugin_shared, 'display_hidden_bars' );
		// Ajax Preview on backend
		$this->loader->add_action( 'wp_ajax_preview_bar', $plugin_shared, 'preview_bar' );
		// Scripts & styles
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_shared, 'enqueue_styles', -1 );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_shared, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_shared, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_shared, 'enqueue_scripts' );
	}

	private function reset_cookies_bulk_action() {

		//Define bulk action
		$bulk_actions = new Seravo_Custom_Bulk_Action(array('post_type' => 'mts_notification_bar'));

		$bulk_actions->register_bulk_action(
			array(
				'menu_text'=> __( 'Reset Cookies', $this->plugin_name ),
				'admin_notice'=>__( 'Notification bar cookies updated', $this->plugin_name ),
				'action_name'=>'reset_cookies',
				'callback' => array( $this, 'callback')
			)
		);

		//Finally init actions
		$bulk_actions->init();
	}

	public function callback( $post_ids ) {

		if ( !empty( $post_ids ) ) {

			foreach ( $post_ids as $post_id ) {

				if ( isset( $_COOKIE['mtsnb_seen_'.$post_id] ) ) {
					unset( $_COOKIE['mtsnb_seen_'.$post_id] );
					setcookie( 'mtsnb_seen_'.$post_id, '0', time() - 3600, '/' ); // empty value and old timestamp
				}

				if ( isset( $_COOKIE['mtsnb_'.$post_id.'_after'] ) ) {
					unset( $_COOKIE['mtsnb_'.$post_id.'_after'] );
					setcookie( 'mtsnb_'.$post_id.'_after', '', time() - 3600, '/' ); // empty value and old timestamp
				}

				if ( isset( $_COOKIE['mtsnb_ab_'.$post_id] ) ) {
					unset( $_COOKIE['mtsnb_ab_'.$post_id] );
					$secure = ( 'https' === parse_url( home_url(), PHP_URL_SCHEME ) );// maybe not needed
					setcookie( 'mtsnb_ab_'.$post_id, '', time() - 3600, COOKIEPATH, COOKIE_DOMAIN, $secure );
				}
			}
		}

		return true;
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    MTSNB_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
