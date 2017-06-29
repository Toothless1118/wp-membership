<?php


/**
 * Is EDD active?
 *
 * @since 1.0.0
 * @return bool
 */
function affwp_sr_is_edd_active() {
	return class_exists( 'Easy_Digital_Downloads' );
}

/**
 * Is WooCommerce active?
 *
 * @since 1.0.0
 * @return bool
 */
function affwp_sr_is_woocommerce_active() {
	return class_exists( 'WooCommerce' );
}

/**
 * Is Gravity Forms active?
 * Both Gravity Forms and the user registration add-on must be active
 *
 * @since 1.0.0
 * @return bool
 */
function affwp_sr_is_gravity_forms_active() {
	return class_exists( 'GFForms' ) && class_exists( 'GFUser' );
}

/**
 * Is Ultimate Member active?
 *
 * @since 1.0.0
 * @return bool
 */
function affwp_sr_is_ultimate_member_active() {
	return class_exists( 'UM_API' );
}

/**
 * Is User Pro active?
 *
 * @since 1.0.0
 * @return bool
 */
function affwp_sr_is_user_pro_active() {
	return class_exists( 'userpro_api' );
}

/**
 * Referral Types
 *
 * @since 1.0.0
 */
function affwp_sr_get_referral_types() {

	$types = array(
		'affiliate' => __( 'Affiliate Registration', 'affiliatewp-signup-referrals' )
	);

	// Add EDD signup option
	if ( affwp_sr_is_edd_active() ) {
		$types['edd'] = __( 'Easy Digital Downloads Registration', 'affiliatewp-signup-referrals' );
	}

	// Add WooCommerce signup option
	if ( affwp_sr_is_woocommerce_active() ) {
		$types['woocommerce'] = __( 'WooCommerce Registration', 'affiliatewp-signup-referrals' );
	}

	if ( affwp_sr_is_gravity_forms_active() ) {
		$types['gravity_forms'] = __( 'Gravity Forms User Registration', 'affiliatewp-signup-referrals' );
	}

	if ( affwp_sr_is_ultimate_member_active() ) {
		$types['ultimate_member'] = __( 'Ultimate Member Registration', 'affiliatewp-signup-referrals' );
	}

	if ( affwp_sr_is_user_pro_active() ) {
		$types['user_pro'] = __( 'User Pro Registration', 'affiliatewp-signup-referrals' );
	}

	return apply_filters( 'affwp_signup_referrals_referral_types', $types );
}

/**
 * Allowed referral types
 *
 * @since 1.0.0
 */
function affwp_sr_allowed_referral_types() {
	$allowed_referral_types = affiliate_wp()->settings->get( 'affwp_sr_referral_types' );

	return $allowed_referral_types;
}
