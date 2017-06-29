<?php
/**
 * AffiliateWP Admin Notices class
 *
 * @since 1.0
 */
class Affiliate_WP_Admin_Notices {

	/**
	 * Constructor.
	 *
	 * @since 1.0
	 * @access public
	 */
	public function __construct() {

		add_action( 'admin_notices', array( $this, 'show_notices' ) );
		add_action( 'admin_notices', array( $this, 'upgrade_notices' ) );

		add_action( 'affwp_dismiss_notices', array( $this, 'dismiss_notices' ) );
	}

	/**
	 * Displays admin notices.
	 *
	 * @since 1.0
	 * @since 1.8.3 Notices are hidden for users lacking the 'manage_affiliates' capability
	 * @access public
	 */
	public function show_notices() {
		// Don't display notices for users who can't manage affiliates.
		if ( ! current_user_can( 'manage_affiliates' ) ) {
			return;
		}

		$integrations = affiliate_wp()->integrations->get_enabled_integrations();

		if( empty( $integrations ) && ! get_user_meta( get_current_user_id(), '_affwp_no_integrations_dismissed', true ) ) {
			echo '<div class="error">';
				echo '<p>' . sprintf( __( 'There are currently no AffiliateWP <a href="%s">integrations</a> enabled. If you are using AffiliateWP without any integrations, you may disregard this message.', 'affiliate-wp' ), affwp_admin_url( 'settings', array( 'tab' => 'integrations' ) ) ) . '</p>';
				echo '<p><a href="' . wp_nonce_url( add_query_arg( array( 'affwp_action' => 'dismiss_notices', 'affwp_notice' => 'no_integrations' ) ), 'affwp_dismiss_notice', 'affwp_dismiss_notice_nonce' ) . '">' . _x( 'Dismiss Notice', 'Integrations', 'affiliate-wp' ) . '</a></p>';
			echo '</div>';
		}

		$class = 'updated';

		if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] && isset( $_GET['page'] ) && $_GET['page'] == 'affiliate-wp-settings' ) {
			$message = __( 'Settings updated.', 'affiliate-wp' );
		}

		if ( isset( $_GET['affwp_notice'] ) && $_GET['affwp_notice'] ) {

			switch( $_GET['affwp_notice'] ) {

				// Affiliates.
				case 'affiliate_added' :
					if ( ! class_exists( 'Affiliate_WP_Migrate_Users' ) ) {
						require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/tools/class-migrate-base.php';
						require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/tools/class-migrate-users.php';
					}

					$migrate          = new Affiliate_WP_Migrate_Users;
					$total_affiliates = (int) $migrate::get_items_total( 'affwp_migrate_users_current_count' );

					/*
					 * If $total_affiliates is 0 and we know 'affiliate_added' has been fired,
					 * it was a manual addition, and therefore 1 affiliate was added.
					 */
					if ( 0 === $total_affiliates ) {
						$total_affiliates = 1;
					}

					$message = sprintf( _n(
						'%d affiliate was added successfully.',
						'%d affiliates were added successfully',
						$total_affiliates,
						'affiliate-wp'
					), number_format_i18n( $total_affiliates ) );

					$migrate::clear_items_total( 'affwp_migrate_users_current_count' );

					break;

				case 'affiliate_added_failed' :

					$message = __( 'Affiliate wasn&#8217;t added, please try again.', 'affiliate-wp' );
					$class   = 'error';

					break;

				case 'affiliate_updated' :

					$message = __( 'Affiliate updated successfully', 'affiliate-wp' );

					$message .= '<p>'. sprintf( __( '<a href="%s">Back to Affiliates</a>', 'affiliate-wp' ), esc_url( affwp_admin_url( 'affiliates' ) ) ) .'</p>';

					break;

				case 'affiliate_update_failed' :

					$message = __( 'Affiliate update failed, please try again', 'affiliate-wp' );
					$class   = 'error';

					break;

				case 'affiliate_deleted' :

					$message = __( 'Affiliate account(s) deleted successfully', 'affiliate-wp' );

					break;

				case 'affiliate_delete_failed' :

					$message = __( 'Affiliate deletion failed, please try again', 'affiliate-wp' );
					$class   = 'error';

					break;

				case 'affiliate_activated' :

					$message = __( 'Affiliate account activated', 'affiliate-wp' );

					break;

				case 'affiliate_deactivated' :

					$message = __( 'Affiliate account deactivated', 'affiliate-wp' );

					break;

				case 'affiliate_accepted' :

					$message = __( 'Affiliate request was accepted', 'affiliate-wp' );

					break;

				case 'affiliate_rejected' :

					$message = __( 'Affiliate request was rejected', 'affiliate-wp' );

					break;

				case 'affiliates_migrated' :

					if ( ! class_exists( 'Affiliate_WP_Migrate_WP_Affiliate' ) ) {
						require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/tools/class-migrate-base.php';
						require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/tools/class-migrate-wp-affiliate.php';
					}

					$migrate          = new Affiliate_WP_Migrate_WP_Affiliate;
					$total_affiliates = (int) $migrate::get_items_total( 'affwp_migrate_affiliates_total_count' );

					$message = sprintf( _n(
						'%d affiliate from WP Affiliate was added successfully.',
						'%d affiliates from WP Affiliate were added successfully',
						$total_affiliates,
						'affiliate-wp'
					), number_format_i18n( $total_affiliates ) );

					$migrate::clear_items_total( 'affwp_migrate_affiliates_total_count' );

					break;

				case 'affiliates_pro_migrated' :

					if ( ! class_exists( 'Affiliate_WP_Migrate_Affiliates_Pro' ) ) {
						require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/tools/class-migrate-base.php';
						require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/tools/class-migrate-affiliates-pro.php';
					}

					$migrate          = new Affiliate_WP_Migrate_Affiliates_Pro;
					$total_affiliates = (int) $migrate::get_items_total( 'affwp_migrate_affiliates_pro_total_count' );

					$message = sprintf( _n(
						'%d affiliate from Affiliates Pro was added successfully.',
						'%d affiliates from Affiliates Pro were added successfully',
						$total_affiliates,
						'affiliate-wp'
					), number_format_i18n( $total_affiliates ) );

					$migrate::clear_items_total( 'affwp_migrate_affiliates_pro_total_count' );

					break;

				case 'stats_recounted' :

					$message = __( 'Affiliate stats have been recounted!', 'affiliate-wp' );

					break;

				// Referrals.
				case 'referral_added' :

					$message = __( 'Referral added successfully', 'affiliate-wp' );

					break;

				case 'referral_updated' :

					$message = __( 'Referral updated successfully', 'affiliate-wp' );

					break;

				case 'referral_update_failed' :

					$message = __( 'Referral update failed, please try again', 'affiliate-wp' );

					break;

				case 'referral_deleted' :

					$message = __( 'Referral deleted successfully', 'affiliate-wp' );

					break;

				case 'referral_delete_failed' :

					$message = __( 'Referral deletion failed, please try again', 'affiliate-wp' );
					$class   = 'error';

					break;

				case 'payout_created' :

					$message = sprintf( __( 'A payout has been created.', 'affiliate-wp' ) );

					break;

				// Creatives.
				case 'creative_updated' :

					$message = __( 'Creative updated successfully', 'affiliate-wp' );

					$message .= '<p>'. sprintf( __( '<a href="%s">Back to Creatives</a>', 'affiliate-wp' ), esc_url( affwp_admin_url( 'creatives' ) ) ) .'</p>';

					break;

				case 'creative_added' :

					$message = __( 'Creative added successfully', 'affiliate-wp' );

					break;

				case 'creative_deleted' :

					$message = __( 'Creative deleted successfully', 'affiliate-wp' );

					break;

				case 'creative_activated' :

					$message = __( 'Creative activated', 'affiliate-wp' );

					break;

				case 'creative_deactivated' :

					$message = __( 'Creative deactivated', 'affiliate-wp' );

					break;

				// Importer.
				case 'settings-imported' :

					$message = __( 'Settings successfully imported', 'affiliate-wp' );

					break;

				case 'license-expired' :

					$class = 'expired';
					$message = sprintf(
						__( 'Your license key expired on %s. Please <a href="%s" target="_blank">renew your license key</a>.', 'affiliate-wp' ),
						date_i18n( get_option( 'date_format' ), strtotime( $license->expires, current_time( 'timestamp' ) ) ),
						'https://affiliatewp.com/checkout/?edd_license_key=' . $value . '&utm_campaign=admin&utm_source=licenses&utm_medium=expired'
					);


					break;

				case 'license-revoked' :

					$class = 'error';
					$message = sprintf(
						__( 'Your license key has been disabled. Please <a href="%s" target="_blank">contact support</a> for more information.', 'affiliate-wp' ),
						'https://affiliatewp.com/support?utm_campaign=admin&utm_source=licenses&utm_medium=revoked'
					);


					break;

				case 'license-missing' :

					$class = 'error';
					$message = sprintf(
						__( 'Invalid license. Please <a href="%s" target="_blank">visit your account page</a> and verify it.', 'affiliate-wp' ),
						'https://affiliatewp.com/account/?utm_campaign=admin&utm_source=licenses&utm_medium=missing'
					);


					break;

				case 'license-invalid' :
				case 'license-site_inactive' :

					$class = 'error';
					$message = sprintf(
						__( 'Your %s is not active for this URL. Please <a href="%s" target="_blank">visit your account page</a> to manage your license key URLs.', 'affiliate-wp' ),
						$args['name'],
						'https://affiliatewp.com/account/?utm_campaign=admin&utm_source=licenses&utm_medium=invalid'
					);


					break;

				case 'license-item_name_mismatch' :

					$class = 'error';
					$message = sprintf( __( 'This appears to be an invalid license key for %s.', 'affiliate-wp' ), $args['name'] );


					break;

				case 'license-no_activations_left':

					$class = 'error';
					$message = sprintf(
						__( 'Your license key has reached its activation limit. <a href="%s">View possible upgrades</a> now.', 'affiliate-wp' ),
						'https://affiliatewp.com/account/?utm_campaign=admin&utm_source=licenses&utm_medium=missing'
					);

					break;

				// API keys.
				case 'api_key_generated' :

					$message = __( 'The API keys were successfully generated.', 'affiliate-wp' );

					break;

				case 'api_key_failed' :

					$message = __( 'The API keys could not be generated.', 'affiliate-wp' );
					$class   = 'error';

					break;

				case 'api_key_regenerated' :

					$message = __( 'The API keys were successfully regenerated.', 'affiliate-wp' );

					break;

				case 'api_key_revoked' :

					$message = __( 'The API keys were successfully revoked.', 'affiliate-wp' );

					break;

				default :

					if( ! empty( $_GET['affwp_message'] ) ) {
						$message = urldecode( sanitize_text_field( $_GET['affwp_message'] ) );
						if( ! empty( $_GET['affwp_success'] ) && 'no' == $_GET['affwp_success'] ) {
							$class = 'error';
						}
					}

					break;

			}
		}

		if ( ! empty( $message ) ) {
			echo '<div class="' . esc_attr( $class ) . '"><p>' .  $message  . '</p></div>';
		}

		$license = affiliate_wp()->settings->check_license();

		if ( ! is_wp_error( $license ) && false === get_transient( 'affwp_license_notice' ) ) {

			// Base query args.
			$notice_query_args = array(
				'affwp_action' => 'dismiss_notices'
			);

			if( is_object( $license ) ) {

				$status = $license->license;

			} else {

				$status = $license;

			}

			if ( 'expired' === $status ) {

				$notice_query_args['affwp_notice'] = 'expired_license';

				echo '<div class="error info">';
					echo '<p>' . __( 'Your license key for AffiliateWP has expired. Please renew your license to re-enable automatic updates.', 'affiliate-wp' ) . '</p>';
					echo '<p><a href="' . wp_nonce_url( add_query_arg( $notice_query_args ), 'affwp_dismiss_notice', 'affwp_dismiss_notice_nonce' ) . '">' . _x( 'Dismiss Notice', 'License', 'affiliate-wp' ) . '</a></p>';
				echo '</div>';

			} elseif ( 'valid' !== $status ) {

				$notice_query_args['affwp_notice'] = 'invalid_license';

				echo '<div class="notice notice-info">';
					echo '<p>' . sprintf( __( 'Please <a href="%s">enter and activate</a> your license key for AffiliateWP to enable automatic updates.', 'affiliate-wp' ), esc_url( affwp_admin_url( 'settings' ) ) ) . '</p>';
					echo '<p><a href="' . wp_nonce_url( add_query_arg( $notice_query_args ), 'affwp_dismiss_notice', 'affwp_dismiss_notice_nonce' ) . '">' . _x( 'Dismiss Notice', 'License', 'affiliate-wp' ) . '</a></p>';
				echo '</div>';

			}

		}
	}

	/**
	 * Displays upgrade notices.
	 *
	 * @access public
	 * @since  2.0
	 */
	public function upgrade_notices() {

		if ( true === version_compare( AFFILIATEWP_VERSION, '2.0', '<' ) && false === affwp_has_upgrade_completed( 'upgrade_v20_recount_unpaid_earnings' ) ) :

			// Enqueue admin JS for the batch processor.
			affwp_enqueue_admin_js();
			?>
			<div class="notice notice-info is-dismissible">
				<p><?php _e( 'Your database needs to be upgraded following the latest AffiliateWP update.', 'affiliate-wp-' ); ?></p>
				<form method="post" class="affwp-batch-form" data-batch_id="recount-affiliate-stats-upgrade" data-nonce="<?php echo esc_attr( wp_create_nonce( 'recount-affiliate-stats-upgrade_step_nonce' ) ); ?>">
					<p>
						<?php submit_button( __( 'Upgrade Database', 'affiliate-wp' ), 'secondary', 'v20-recount-unpaid-earnings', false ); ?>
					</p>
				</form>
			</div>
		<?php endif;
	}

	/**
	 * Dismisses admin notices when Dismiss links are clicked.
	 *
	 * @since 1.7.5
	 * @access public
	 * @return void
	 */
	public function dismiss_notices() {
		if( ! isset( $_GET['affwp_dismiss_notice_nonce'] ) || ! wp_verify_nonce( $_GET['affwp_dismiss_notice_nonce'], 'affwp_dismiss_notice') ) {
			wp_die( __( 'Security check failed', 'affiliate-wp' ), __( 'Error', 'affiliate-wp' ), array( 'response' => 403 ) );
		}

		if ( isset( $_GET['affwp_notice'] ) ) {

			$notice = sanitize_key( $_GET['affwp_notice'] );

			switch( $notice ) {
				case 'no_integrations':
					update_user_meta( get_current_user_id(), "_affwp_{$notice}_dismissed", 1 );
					break;
				case 'expired_license':
				case 'invalid_license':
					set_transient( 'affwp_license_notice', true, 2 * WEEK_IN_SECONDS );
					break;
				default:
					/**
					 * Fires once a notice has been flagged for dismissal.
					 *
					 * @since 1.8 as 'affwp_dismiss_notices'
					 * @since 2.0.4 Renamed to 'affwp_dismiss_notices_default' to avoid a dynamic hook conflict.
					 *
					 * @param string $notice Notice value via $_GET['affwp_notice'].
					 */
					do_action( 'affwp_dismiss_notices_default', $notice );
					break;
			}

			wp_redirect( remove_query_arg( array( 'affwp_action', 'affwp_notice' ) ) );
			exit;
		}
	}

}
new Affiliate_WP_Admin_Notices;
