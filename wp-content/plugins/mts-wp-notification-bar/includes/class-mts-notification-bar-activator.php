<?php

/**
 * Fired during plugin activation
 *
 * @link       http://mythemeshop.com
 * @since      1.0.0
 *
 * @package    MTSNB
 * @subpackage MTSNB/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    MTSNB
 * @subpackage MTSNB/includes
 * @author     Your Name <email@example.com>
 */
class MTSNB_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		if ( false == get_option( 'mtsnb_stats' ) ) {

			add_option( 'mtsnb_stats', array() );
		}
	}

}
