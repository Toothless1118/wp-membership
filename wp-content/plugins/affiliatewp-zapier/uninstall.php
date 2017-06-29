<?php
/**
 * Uninstall AffiliateWP Zapier logs table.
 *
 * @package affiliatewp-zapier
 * @since   1.0
 */

// Exit if accessed directly
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) exit;

// Load main plugin file
include_once( 'affiliatewp-zapier.php' );

global $wpdb;

// Remove AffiliateWP Zaper logs table.
$wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->prefix . "affwp_zapier_logs" );

