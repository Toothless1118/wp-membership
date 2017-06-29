<?php

class AffiliateWP_Signup_Referrals_Admin {

	public function __construct() {

	    // settings
	    add_filter( 'affwp_settings_integrations', array( $this, 'settings' ) );

		// store the referring affiliate if there was one
		add_action( 'affwp_edit_affiliate_end', array( $this, 'show_referring_affiliate' ), 10, 1 );

		// load JS
		add_action( 'in_admin_footer', array( $this, 'scripts' ) );

	}

	/**
	 * Settings
	 * Affiliates -> Settings -> Integrations
	 *
	 * @since 1.0.0
	 */
	public function settings( $settings = array() ) {

		// Signup Referrals
	    $settings['affwp_sr_header'] = array(
	        'name' => __( 'Signup Referrals', 'affiliatewp-signup-referrals' ),
	        'type' => 'header'
	    );

		// Referral Types
	    $settings['affwp_sr_referral_types'] = array(
	        'name' => __( 'Referral Types', 'affiliatewp-signup-referrals' ),
	        'desc' => '<p class="description">' . __( 'Select which types of signups should generate a referral', 'affiliatewp-signup-referrals' ) . '</p>',
	        'type' => 'multicheck',
	        'options' => affwp_sr_get_referral_types()
	    );

		// Referral status
	    $settings['affwp_sr_referral_status'] = array(
	        'name' => __( 'Referral Status', 'affiliatewp-signup-referrals' ),
	        'desc' => '<p class="description">' . __( 'Select the default status the referral will be set to.', 'affiliatewp-signup-referrals' ) . '</p>',
	        'type' => 'select',
	        'options' => array(
	            'pending' => __( 'Pending', 'affiliatewp-signup-referrals' ),
	            'unpaid'  => __( 'Unpaid', 'affiliatewp-signup-referrals' ),
	        ),
	        'std' => 'unpaid'
	    );

		// Affiliate registration amount
	    $settings['affwp_sr_affiliate_registration_referral_amount'] = array(
	        'name' => __( 'Affiliate Registration - Referral Amount', 'affiliatewp-signup-referrals' ),
	        'desc' => '<p class="description">' . __( 'Enter the dollar amount the affiliate should receive when a user registers as an affiliate.', 'affiliatewp-signup-referrals' ) . '</p>',
	        'type' => 'number',
	        'size' => 'small',
			'step' => '0.01',
			'std' => '0'
	    );

		// EDD registration amount
		if ( affwp_sr_is_edd_active() ) {

		    $settings['affwp_sr_edd_registration_referral_amount'] = array(
		        'name' => __( 'EDD - Registration Referral Amount', 'affiliatewp-signup-referrals' ),
		        'desc' => '<p class="description">' . __( 'Enter the dollar amount the affiliate should receive if the user registers for an account through Easy Digital Downloads.', 'affiliatewp-signup-referrals' ) . '</p>',
				'type' => 'number',
		        'size' => 'small',
				'step' => '0.01',
				'std' => '0'
		    );

		    $settings['affwp_sr_edd_disable_checkout_referral'] = array(
		        'name' => __( 'EDD - Disable Checkout Referral', 'affiliatewp-signup-referrals' ),
		        'desc' => '<p class="description">' . __( 'Disables referral creation at checkout if a user registers for an account.', 'affiliatewp-signup-referrals' ) . '</p>',
		        'type' => 'checkbox'
		    );

		}

		// WooCommerce registration amount
		if ( affwp_sr_is_woocommerce_active() ) {
		    $settings['affwp_sr_woocommerce_registration_referral_amount'] = array(
		        'name' => __( 'WooCommerce - Registration Referral Amount', 'affiliatewp-signup-referrals' ),
		        'desc' => '<p class="description">' . __( 'Enter the dollar amount the affiliate should receive if the user registers for an account through WooCommerce.', 'affiliatewp-signup-referrals' ) . '</p>',
				'type' => 'number',
		        'size' => 'small',
				'step' => '0.01',
				'std' => '0'
		    );

			$settings['affwp_sr_woocommerce_disable_checkout_referral'] = array(
		        'name' => __( 'WooCommerce - Disable Checkout Referral', 'affiliatewp-signup-referrals' ),
		        'desc' => '<p class="description">' . __( 'Disables referral creation at checkout if a user registers for an account.', 'affiliatewp-signup-referrals' ) . '</p>',
		        'type' => 'checkbox'
		    );

		}

		// Gravity Forms User registration amount
		if ( affwp_sr_is_gravity_forms_active() ) {
		    $settings['affwp_sr_gravity_forms_registration_referral_amount'] = array(
		        'name' => __( 'Gravity Forms - User Registration Referral Amount', 'affiliatewp-signup-referrals' ),
		        'desc' => '<p class="description">' . __( 'Enter the dollar amount the affiliate should receive if the user registers for an account through the Gravity Forms User Registration add-on.', 'affiliatewp-signup-referrals' ) . '</p>',
				'type' => 'number',
		        'size' => 'small',
				'step' => '0.01',
				'std' => '0'
		    );
		}

		// Ultimate Member registration amount
		if ( affwp_sr_is_ultimate_member_active() ) {
			$settings['affwp_sr_ultimate_member_registration_referral_amount'] = array(
				'name' => __( 'Ultimate Member - Registration Referral Amount', 'affiliatewp-signup-referrals' ),
				'desc' => '<p class="description">' . __( 'Enter the dollar amount the affiliate should receive if the user registers for an account through Ultimate Member', 'affiliatewp-signup-referrals' ) . '</p>',
				'type' => 'number',
				'size' => 'small',
				'step' => '0.01',
				'std' => '0.00'
			);
		}

		if ( affwp_sr_is_user_pro_active() ) {
			$settings['affwp_sr_user_pro_registration_referral_amount'] = array(
				'name' => __( 'User Pro - Registration Referral Amount', 'affiliatewp-signup-referrals' ),
				'desc' => '<p class="description">' . __( 'Enter the dollar amount the affiliate should receive if the user registers for an account through User Pro', 'affiliatewp-signup-referrals' ) . '</p>',
				'type' => 'number',
				'size' => 'small',
				'step' => '0.01',
				'std' => '0.00'
			);
		}

	    return apply_filters( 'affwp_signup_referrals_settings', $settings );
	}

	/**
	 * Show the referring affiliate on the edit affiliate screen
	 *
	 * @since 1.0.0
	 */
	public function show_referring_affiliate( $affiliate ) {

		$referring_affiliate_id = affwp_get_affiliate_meta( $affiliate->affiliate_id, 'affwp_referring_affiliate_id', true );

		$url = esc_url( admin_url( 'admin.php?page=affiliate-wp-affiliates&action=edit_affiliate&affiliate_id=' . $referring_affiliate_id ) );

		if ( $referring_affiliate_id ) :
		?>
		<tr class="form-row">

			<th scope="row">
				<label><?php _e( 'Referring Affiliate', 'affiliatewp-signup-referrals' ); ?></label>
			</th>

			<td>
				<div class="description"><a href="<?php echo $url; ?>"><?php echo esc_attr( affwp_get_affiliate_username( $referring_affiliate_id ) ); ?></a> (ID: <?php echo $referring_affiliate_id; ?>)</div>
			</td>

		</tr>

	<?php endif;

	}

	/**
	 * Show or hide the table row based on the checkbox option
	 *
	 * @since 1.0.0
	 */
	public function scripts() {

		$screen = get_current_screen();

		if ( ! ( $screen->id === 'affiliates_page_affiliate-wp-settings' && is_admin() && isset( $_GET['tab'] ) && $_GET['tab'] === 'integrations' ) ) {
			return;
		}

		?>
		<script>
			jQuery(document).ready(function($) {

				// Affiliate option
				var optionAffiliate = $('input[name="affwp_settings[affwp_sr_referral_types][affiliate]"]');

				// amount for Affiliate Registration
				var amountAffiliate = $('input[name="affwp_settings[affwp_sr_affiliate_registration_referral_amount]"]');

				// get the TR that holds the amount
				var rowAmountAffiliate = amountAffiliate.closest('tr');

				// show or hide the table row based on the checkbox option
				optionAffiliate.click( function() {

					if ( this.checked ) {
						rowAmountAffiliate.show();
					} else {
						rowAmountAffiliate.hide();
					}

				});

				if ( optionAffiliate.is(':checked') ) {
					rowAmountAffiliate.show();
				} else {
					rowAmountAffiliate.hide();
				}

				// EDD option
				var optionEDD = $('input[name="affwp_settings[affwp_sr_referral_types][edd]"]');

				var amountEDD = $('input[name="affwp_settings[affwp_sr_edd_registration_referral_amount]"]');
				var preventCheckoutReferralsEDD = $('input[name="affwp_settings[affwp_sr_edd_disable_checkout_referral]"]');

				// get the TRs to show and hide
				var rowAmountEDD = amountEDD.closest('tr');
				var rowpreventCheckoutReferralsEDD = preventCheckoutReferralsEDD.closest('tr');

				// show or hide the table row based on the checkbox option
				optionEDD.click( function() {

					if ( this.checked ) {
						rowAmountEDD.show();
						rowpreventCheckoutReferralsEDD.show();
					} else {
						rowAmountEDD.hide();
						rowpreventCheckoutReferralsEDD.hide();
					}

				});

	        	// When the page first loads, determine if the rows should be hidden or shown
				if ( optionEDD.is(':checked') ) {
					rowAmountEDD.show();
					rowpreventCheckoutReferralsEDD.show();
				} else {
					rowAmountEDD.hide();
					rowpreventCheckoutReferralsEDD.hide();
				}

				// WooCommerce option
				var optionWC = $('input[name="affwp_settings[affwp_sr_referral_types][woocommerce]"]');

				var amountWC = $('input[name="affwp_settings[affwp_sr_woocommerce_registration_referral_amount]"]');
				var preventCheckoutReferralsWooCommerce = $('input[name="affwp_settings[affwp_sr_woocommerce_disable_checkout_referral]"]');

				var rowAmountWC = amountWC.closest('tr');
				var rowpreventCheckoutReferralsWooCommerce = preventCheckoutReferralsWooCommerce.closest('tr');

				// show or hide the table row based on the checkbox option
				optionWC.click( function() {

				  if ( this.checked ) {
					  rowAmountWC.show();
					  rowpreventCheckoutReferralsWooCommerce.show();
				  } else {
					  rowAmountWC.hide();
					  rowpreventCheckoutReferralsWooCommerce.hide();
				  }

				});

				// When the page first loads, determine if the rows should be hidden or shown
				if ( optionWC.is(':checked') ) {
					rowAmountWC.show();
					rowpreventCheckoutReferralsWooCommerce.show();
				} else {
					rowAmountWC.hide();
					rowpreventCheckoutReferralsWooCommerce.hide();
				}

				// Ultimate Member option
				var optionUltimateMember = $('input[name="affwp_settings[affwp_sr_referral_types][ultimate_member]"]');

				// amount
				var amountUltimateMember = $('input[name="affwp_settings[affwp_sr_ultimate_member_registration_referral_amount]"]');

				// get the TR that holds the amount
				var rowAmountUltimateMember = amountUltimateMember.closest('tr');

				// show or hide the table row based on the checkbox option
				optionUltimateMember.click( function() {

					if ( this.checked ) {
						rowAmountUltimateMember.show();
					} else {
						rowAmountUltimateMember.hide();
					}

				});

				// When the page first loads, determine if the rows should be hidden or shown
				if ( optionUltimateMember.is(':checked') ) {
					rowAmountUltimateMember.show();
				} else {
					rowAmountUltimateMember.hide();
				}


				// Gravity Forms option
				var optionGravityForms = $('input[name="affwp_settings[affwp_sr_referral_types][gravity_forms]"]');

				// amount
				var amountGravityForms = $('input[name="affwp_settings[affwp_sr_gravity_forms_registration_referral_amount]"]');

				// get the TR that holds the amount
				var rowAmountGravityForms = amountGravityForms.closest('tr');

				// show or hide the table row based on the checkbox option
				optionGravityForms.click( function() {

					if ( this.checked ) {
						rowAmountGravityForms.show();
					} else {
						rowAmountGravityForms.hide();
					}

				});

				// When the page first loads, determine if the rows should be hidden or shown
				if ( optionGravityForms.is(':checked') ) {
					rowAmountGravityForms.show();
				} else {
					rowAmountGravityForms.hide();
				}

				// User Pro option
				var optionUserPro = $('input[name="affwp_settings[affwp_sr_referral_types][user_pro]"]');

				// amount
				var amountUserPro = $('input[name="affwp_settings[affwp_sr_user_pro_registration_referral_amount]"]');

				// get the TR that holds the amount
				var rowAmountUserPro = amountUserPro.closest('tr');

				// show or hide the table row based on the checkbox option
				optionUserPro.click( function() {

					if ( this.checked ) {
						rowAmountUserPro.show();
					} else {
						rowAmountUserPro.hide();
					}

				});

				// When the page first loads, determine if the rows should be hidden or shown
				if ( optionUserPro.is(':checked') ) {
					rowAmountUserPro.show();
				} else {
					rowAmountUserPro.hide();
				}

			});
		</script>

		<?php
	}

}
new AffiliateWP_Signup_Referrals_Admin;
