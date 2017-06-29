<?php
/**
 * Plugin Name: AffiliateWP - Signup Referrals
 * Plugin URI: https://affiliatewp.com/addons/signup-referrals
 * Description: Award commission to affiliates when a referred user signs up for an account
 * Author: Pippin Williamson and Andrew Munro
 * Author URI: http://affiliatewp.com
 * Version: 1.0.1
 * Text Domain: affiliatewp-signup-referrals
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

if ( ! class_exists( 'AffiliateWP_Signup_Referrals' ) ) {

	final class AffiliateWP_Signup_Referrals {

		/**
		 * Holds the instance
		 *
		 * Ensures that only one instance of AffiliateWP_Signup_Referrals exists in memory at any one
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
		 * The version number of AffiliateWP
		 *
		 * @since 1.0
		 */
		private $version = '1.0.1';

		/**
		 * Main AffiliateWP_Signup_Referrals Instance
		 *
		 * Insures that only one instance of AffiliateWP_Signup_Referrals exists in memory at any one
		 * time. Also prevents needing to define globals all over the place.
		 *
		 * @since 1.0
		 * @static
		 * @static var array $instance
		 * @return The one true AffiliateWP_Signup_Referrals
		 */
		public static function instance() {

			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof AffiliateWP_Signup_Referrals ) ) {

				self::$instance = new AffiliateWP_Signup_Referrals;
				self::$instance->setup_constants();
				self::$instance->load_textdomain();
				self::$instance->includes();
				self::$instance->init();
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
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'affiliatewp-signup-referrals' ), '1.0' );
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
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'affiliatewp-signup-referrals' ), '1.0' );
		}

		/**
		 * Constructor Function
		 *
		 * @since 1.0
		 * @access private
		 */
		private function __construct() {
			self::$instance = $this;
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
			if ( ! defined( 'AFFWP_SR_VERSION' ) ) {
				define( 'AFFWP_SR_VERSION', $this->version );
			}

			// Plugin Folder Path
			if ( ! defined( 'AFFWP_SR_PLUGIN_DIR' ) ) {
				define( 'AFFWP_SR_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
			}

			// Plugin Folder URL
			if ( ! defined( 'AFFWP_SR_PLUGIN_URL' ) ) {
				define( 'AFFWP_SR_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
			}

			// Plugin Root File
			if ( ! defined( 'AFFWP_SR_PLUGIN_FILE' ) ) {
				define( 'AFFWP_SR_PLUGIN_FILE', __FILE__ );
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
			$lang_dir = apply_filters( 'affwp_signup_referrals_languages_directory', $lang_dir );

			// Traditional WordPress plugin locale filter
			$locale   = apply_filters( 'plugin_locale',  get_locale(), 'affiliatewp-signup-referrals' );
			$mofile   = sprintf( '%1$s-%2$s.mo', 'affiliatewp-signup-referrals', $locale );

			// Setup paths to current locale file
			$mofile_local  = $lang_dir . $mofile;
			$mofile_global = WP_LANG_DIR . '/affiliatewp-signup-referrals/' . $mofile;

			if ( file_exists( $mofile_global ) ) {
				// Look in global /wp-content/languages/affiliatewp-signup-referrals/ folder
				load_textdomain( 'affiliatewp-signup-referrals', $mofile_global );
			} elseif ( file_exists( $mofile_local ) ) {
				// Look in local /wp-content/plugins/affiliatewp-signup-referrals/languages/ folder
				load_textdomain( 'affiliatewp-signup-referrals', $mofile_local );
			} else {
				// Load the default language files
				load_plugin_textdomain( 'affiliatewp-signup-referrals', false, $lang_dir );
			}
		}

		/**
		 * Include necessary files
		 *
		 * @access      private
		 * @since       1.0.0
		 * @return      void
		 */
		private function includes() {

			require_once AFFWP_SR_PLUGIN_DIR . 'includes/functions.php';
			require_once AFFWP_SR_PLUGIN_DIR . 'includes/class-base.php';

			if ( is_admin() ) {
				require_once AFFWP_SR_PLUGIN_DIR . 'includes/class-admin.php';
			}

		}

		/**
		 * Init
		 *
		 * @access private
		 * @since 1.0
		 * @return void
		 */
		private function init() {

			if ( is_admin() ) {
				self::$instance->updater();
			}

		}

		/**
		 * Setup the default hooks and actions
		 *
		 * @since 1.0
		 *
		 * @return void
		 */
		private function hooks() {

			// plugin meta
			add_filter( 'plugin_row_meta', array( $this, 'plugin_meta' ), null, 2 );

		}

		/**
		 * Load the custom plugin updater
		 *
		 * @access private
		 * @since 1.0
		 * @return void
		 */
		public function updater() {

			if ( class_exists( 'AffWP_AddOn_Updater' ) ) {
				$updater = new AffWP_AddOn_Updater( 64096, __FILE__, AFFWP_SR_VERSION );
			}
		}

		/**
		 * Modify plugin metalinks
		 *
		 * @access      public
		 * @since       1.0.0
		 * @param       array $links The current links array
		 * @param       string $file A specific plugin table entry
		 * @return      array $links The modified links array
		 */
		public function plugin_meta( $links, $file ) {
		    if ( $file == plugin_basename( __FILE__ ) ) {
		        $plugins_link = array(
		            '<a title="' . __( 'Get more add-ons for AffiliateWP', 'affiliatewp-signup-referrals' ) . '" href="http://affiliatewp.com/addons/" target="_blank">' . __( 'More add-ons', 'affiliatewp-signup-referrals' ) . '</a>'
		        );

		        $links = array_merge( $links, $plugins_link );
		    }

		    return $links;
		}


	}

	/**
	 * The main function responsible for returning the one true AffiliateWP_Signup_Referrals
	 * Instance to functions everywhere.
	 *
	 * Use this function like you would a global variable, except without needing
	 * to declare the global.
	 *
	 * Example: <?php $affwp_sign_up_referrals = affiliatewp_signup_referrals(); ?>
	 *
	 * @since 1.0.0
	 * @return object The one true AffiliateWP_Signup_Referrals Instance
	 */
	function affiliatewp_signup_referrals() {

	    if ( ! class_exists( 'Affiliate_WP' ) ) {

	        if ( ! class_exists( 'AffiliateWP_Activation' ) ) {
	            require_once 'includes/class-activation.php';
	        }

	        $activation = new AffiliateWP_Activation( plugin_dir_path( __FILE__ ), basename( __FILE__ ) );
	        $activation = $activation->run();

	    } else {
	        return AffiliateWP_Signup_Referrals::instance();
	    }

	}
	add_action( 'plugins_loaded', 'affiliatewp_signup_referrals', 100 );

}
