<?php
/**
 * Plugin Name: AffiliateWP - Zapier - Automated Tasks
 * Plugin URI: https://affiliatewp.com
 * Description: Add Zapier triggers to AffiliateWP
 * Author: AffiliateWP
 * Author URI: https://affiliatewp.com
 * Version: 1.1
 * Text Domain: affiliatewp-zapier
 * Domain Path: languages
 *
 * AffiliateWP is distributed under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * AffiliateWP is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with AffiliateWP. If not, see <http://www.gnu.org/licenses/>.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'AffiliateWP_Zapier' ) ) {

	final class AffiliateWP_Zapier {

		/**
		 * Holds the instance
		 *
		 * Ensures that only one instance of AffiliateWP_Zapier exists in memory at any one
		 * time and it also prevents needing to define globals all over the place.
		 *
		 * TL;DR This is a static property property that holds the singleton instance.
		 *
		 * @var object
		 * @static
		 * @since 1.0
		 */
		private static $instance;


		/**
		 * The version number of AffiliateWP - Zapier
		 *
		 * @since 1.0
		 */
		private $version = '1.1';

		/**
		 * The logs instance variable.
		 *
		 * @var   Affiliate_WP_Zapier_Logs_DB
		 * @since 1.0
		 */
		public $logs;

		/**
		 * Debug variable.
		 *
		 * @var boolean True if debug is active.
		 */
		public $debug;

		/**
		 * An array of error messages.
		 *
		 * @access public
		 * @since  1.0
		 * @var    array
		 */
		public $errors;

		/**
		 * Error-logging class object
		 *
		 * @access public
		 * @since  1.0
		 * @var    Affiliate_WP_Logging
		 */
		public $error;

		/**
		 * Main AffiliateWP_Zapier Instance
		 *
		 * @since  1.0
		 * @static var array $instance
		 * @return The one true AffiliateWP_Zapier
		 */
		public static function instance() {

			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof AffiliateWP_Zapier ) ) {

				self::$instance = new AffiliateWP_Zapier;
				self::$instance->setup_constants();
				self::$instance->load_textdomain();
				self::$instance->includes();
				self::$instance->init();
				self::$instance->logs = new Affiliate_WP_Zapier_Logs_DB;
				self::$instance->install();
				self::$instance->hooks();


			}

			return self::$instance;
		}

		/**
		 * Throw error on object clone
		 *
		 * The whole idea of the singleton design pattern is that there is a single
		 * object therefore, we don't want the object to be cloned.
		 *
		 * @since 1.0
		 * @access protected
		 * @return void
		 */
		public function __clone() {
			// Cloning instances of the class is forbidden
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'affiliatewp-zapier' ), '1.0' );
		}

		/**
		 * Disable unserializing of the class
		 *
		 * @since 1.0
		 * @access protected
		 * @return void
		 */
		public function __wakeup() {
			// Unserializing instances of the class is forbidden
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'affiliatewp-zapier' ), '1.0' );
		}

		/**
		 * Constructor Function
		 *
		 * @since 1.0
		 * @access private
		 */
		private function __construct() {
			self::$instance = $this;

			$this->errors = $this->errors();
			$this->debug = (bool) affiliate_wp()->settings->get( 'debug_mode', false );


			if( $this->debug ) {
				$this->error = new Affiliate_WP_Logging;
			}
		}

		/**
		 * Reset the instance of the class
		 *
		 * @since 1.0
		 * @access public
		 * @static
		 */
		public static function reset() {
			self::$instance = null;
		}

		/**
		 * Setup plugin constants
		 *
		 * @access private
		 * @since 1.0
		 * @return void
		 */
		private function setup_constants() {
			// Plugin version
			if ( ! defined( 'AFFWP_ZAPIER_VERSION' ) ) {
				define( 'AFFWP_ZAPIER_VERSION', $this->version );
			}

			// Plugin Folder Path
			if ( ! defined( 'AFFWP_ZAPIER_PLUGIN_DIR' ) ) {
				define( 'AFFWP_ZAPIER_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
			}

			// Plugin Folder URL
			if ( ! defined( 'AFFWP_ZAPIER_PLUGIN_URL' ) ) {
				define( 'AFFWP_ZAPIER_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
			}

			// Plugin Root File
			if ( ! defined( 'AFFWP_ZAPIER_PLUGIN_FILE' ) ) {
				define( 'AFFWP_ZAPIER_PLUGIN_FILE', __FILE__ );
			}
		}

		/**
		 * Loads the plugin language files
		 *
		 * @access public
		 * @since 1.0
		 * @return void
		 */
		public function load_textdomain() {

			// Set filter for plugin's languages directory
			$lang_dir = dirname( plugin_basename( __FILE__ ) ) . '/languages/';
			$lang_dir = apply_filters( 'affiliatewp_zapier_languages_directory', $lang_dir );

			// Traditional WordPress plugin locale filter
			$locale   = apply_filters( 'plugin_locale',  get_locale(), 'affiliatewp-zapier' );
			$mofile   = sprintf( '%1$s-%2$s.mo', 'affiliatewp-zapier', $locale );

			// Setup paths to current locale file
			$mofile_local  = $lang_dir . $mofile;
			$mofile_global = WP_LANG_DIR . '/affiliatewp-zapier/' . $mofile;

			if ( file_exists( $mofile_global ) ) {
				// Look in global /wp-content/languages/affiliatewp-zapier/ folder
				load_textdomain( 'affiliatewp-zapier', $mofile_global );
			} elseif ( file_exists( $mofile_local ) ) {
				// Look in local /wp-content/plugins/affiliatewp-zapier/languages/ folder
				load_textdomain( 'affiliatewp-zapier', $mofile_local );
			} else {
				// Load the default language files
				load_plugin_textdomain( 'affiliatewp-zapier', false, $lang_dir );
			}
		}

		/**
		 * Include necessary files
		 *
		 * @access      private
		 * @since       1.0
		 * @return      void
		 */
		private function includes() {

			require_once AFFWP_ZAPIER_PLUGIN_DIR . 'includes/class-admin.php';

			require_once AFFWP_ZAPIER_PLUGIN_DIR . '/includes/class-affwp-zapier-db.php';
			require_once AFFWP_ZAPIER_PLUGIN_DIR . '/includes/class-affwp-zapier-logs-db.php';
			require_once AFFWP_ZAPIER_PLUGIN_DIR . '/includes/zapier-log-functions.php';
			require_once AFFWP_ZAPIER_PLUGIN_DIR . '/includes/class-affwp-zapier-endpoints.php';
		}

		/**
		 * Checks for updates to the add-on on plugin initialization.
		 *
		 * @access private
		 * @since  1.0.1
		 *
		 * @see \AffWP_AddOn_Updater
		 */
		private function init() {

			if ( is_admin() && class_exists( 'AffWP_AddOn_Updater' ) ) {
				$updater = new AffWP_AddOn_Updater( 142032, __FILE__, $this->version );
			}
		}

		/**
		 * Create Zapier logs table.
		 *
		 * @since  1.0
		 * @access  public
		 *
		 * @return void
		 */
		public function install() {

			$this->logs->create_table();
		}

		/**
		 * Writes a log message.
		 *
		 * @since   1.0
		 * @access  public
		 *
		 * @param string $message An optional message to log. Default is an empty string.
		 */
		public function error( $message = '' ) {

			if ( $this->debug ) {

				$this->error->log( $message );

			}
		}

		/**
		 * An array of error messages.
		 *
		 * Note: The user insertion failure error is not included in this method,
		 * as it is defined inline, to provide access to the $args array.
		 *
		 * @access  public
		 * @since   1.0
		 *
		 * @param array $error An array of error messages.
		 */
		public function errors() {

			if ( ! $this->debug ) {

				return false;

			}

			$errors = array(
				'created' => __( 'A Zapier log was inserted when this object was created.', 'affiliatewp-zapier' ),
				'updated' => __( 'A Zapier log was inserted when this object was created.', 'affiliatewp-zapier' ),
				'deleted' => __( 'A Zapier log was inserted when this object was created.', 'affiliatewp-zapier' )
			);

			return $errors;
		}

		/**
		 * Setup the default hooks and actions
		 *
		 * @since   1.0
		 * @access  private
		 *
		 * @return  void
		 */
		private function hooks() {

			register_activation_hook( AFFWP_ZAPIER_PLUGIN_FILE, array( $this, 'flush' ) );

			register_activation_hook( AFFWP_ZAPIER_PLUGIN_FILE, array( $this, 'delete_logs_event' ) );

			register_deactivation_hook( AFFWP_ZAPIER_PLUGIN_FILE, array( $this, 'clear_scheduled' ) );

			add_action( 'affwp_zapier_log_deletion_event', array( $this, 'delete_logs' ) );

			add_action( 'rest_api_init', array( $this, 'register_routes' ) );

			// Add the affiliate name to the core affiliates endpoint.
			add_action( 'affwp_rest_affiliates_query_args', array( $this, 'affiliate_name' ) );
		}

		/**
		 * Add the affiliate name to the core affiliates endpoint.
		 *
		 * @since  1.0
		 *
		 * @return array $args Affiliate object query arguments.
		 */
		public function affiliate_name( $args ) {

			$affiliate_id = isset( $args['affiliate_id'] ) ? $args['affiliate_id'] : 0;

			$args['name'] = affwp_get_affiliate_name( $affiliate_id );

			return $args;
		}

		/**
		 * Determine if the user is on a version of AffiliateWP lower than 1.9.
		 *
		 * @since   1.0
		 * @access  public
		 *
		 * @return  boolean
		 */
		public function has_1_9() {

			$return = true;

			if ( version_compare( AFFILIATEWP_VERSION, '1.9', '<' ) ) {
				$return = false;
			}

			return $return;
		}

		/**
		 * Schedule an event to delete queried Zapier logs at a specified interval.
		 *
		 * @since  1.0
		 *
		 * @return void
		 */
		public function delete_logs_event() {

			/**
			 * Specify a human-readable time interval on which Zapier logs should be deleted.
			 *
			 * @var    $interval  Deletion interval. Default is `daily`.
			 *
			 * @since  1.0
			 */
			$interval = apply_filters( 'affwp_zapier_log_deletion_interval', 'daily' );

		    if ( ! wp_next_scheduled ( 'affwp_zapier_log_deletion_event' ) ) {
				wp_schedule_event( time(), $interval, array( $this, 'affwp_zapier_log_deletion_event' ) );
		    }
		}

		/**
		 * Clear scheduled Zapier log deletions on deactivation.
		 *
		 * @since  1.0
		 *
		 * @return void
		 */
		public function clear_scheduled() {
			wp_clear_scheduled_hook( 'affwp_zapier_log_deletion_event' );
		}


		/**
		 * Delete any queried Zapier log, on a daily recurring cron.
		 *
		 * @since  1.0
		 *
		 * @return void
		 */
		public function delete_logs() {

			$args = array(
				'queried' => true
				);

			$logs = affwp_zapier_get_logs(

				/**
				 * Log deletion query arguments.
				 *
				 * @var    array  $args  Query arguments.
				 *                       Default is `queried => true`.
				 *
				 * @since  1.0
				 */
				apply_filters ( 'affwp_zapier_log_deletion_args', $args )
			);

			foreach ( $logs as $log ) {

				$log_id = $log->log_id;

				affwp_zapier_delete_log( $log_id );
			}
		}

		/**
		 * Flushes rewrite rules on activation.
		 *
		 * @since  1.0
		 *
		 * @return void
		 */
		public function flush() {
			flush_rewrite_rules();
		}

		public function register_routes() {

			$routes = new AffWP\Zapier_Endpoints;
			$routes->register_routes();
		}

	}
}
/**
 * The main function responsible for returning the one true AffiliateWP_Zapier
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $affiliatewp_zapier = affiliatewp_zapier(); ?>
 *
 * @since 1.0
 * @return object The one true AffiliateWP_Zapier Instance
 */
function affiliatewp_zapier() {
	if ( ! class_exists( 'Affiliate_WP' ) ) {
		if ( ! class_exists( 'AffiliateWP_Activation' ) ) {
			require_once 'includes/class-activation.php';
		}

		$activation = new AffiliateWP_Activation( plugin_dir_path( __FILE__ ), basename( __FILE__ ) );
		$activation = $activation->run();
	} else {
		return AffiliateWP_Zapier::instance();
	}
}
add_action( 'plugins_loaded', 'affiliatewp_zapier', 100 );
