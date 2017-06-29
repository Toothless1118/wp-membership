<?php

/**
 * The AffiliateWP_Zapier_Admin class.
 *
 * Creates AffilaiteWP settings options for the AffiliateWP Zapier add-on.
 */
class AffiliateWP_Zapier_Admin {

	/**
	 * Get things started
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function __construct() {
		add_filter( 'affwp_settings_tabs', array( $this, 'register_settings_tab' ) );
		add_filter( 'affwp_settings', array( $this, 'register_settings' ) );
	}

	/**
	 * Register the Zapier settings tab
	 *
	 * @access public
	 * @since  1.0
	 * @return array The new tab name
	 */
	public function register_settings_tab( $tabs = array() ) {

		$tabs['zapier'] = __( 'Zapier', 'affiliatewp-zapier' );

		return $tabs;
	}

	/**
	 * Checks if The AffiliateWP - REST API Extended add-on is installed and active.
	 *
	 * @since  1.1
	 *
	 * @return boolean True if installed and active, otherwise false.
	 */
	public function is_rae_active() {
		return class_exists( 'AffiliateWP_REST_API_Extended' );
	}

	/**
	 * Add our settings
	 *
	 * @access public
	 * @since  1.0
	 * @param  array $settings The existing settings
	 * @return array $settings The updated settings
	 */
	public function register_settings( $settings = array() ) {

		$zapier_url     = 'https://zapier.com/app/editor/';
		$doc_url        = 'http://docs.affiliatewp.com/article/1478-zapier-setup-and-configuration';

		// The following variables pertain to the AffiliateWP REST API Extended add-on.
		$rae_add_on_url     = 'https://affiliatewp.com/add-ons/pro/rest-api-extended';
		$rae_doc_url        = 'http://docs.affiliatewp.com/category/1776-rest-api-extended';
		// $rae_settings_tab   = wp_safe_redirect( affwp_admin_url( 'settings', array( 'tab' => 'rest_api' ) ) );
		$rae_settings_tab   = admin_url( 'admin.php?page=affiliate-wp-settings&tab=rest_api' );
		$rae_url            = $this->is_rae_active() ? $rae_settings_tab : $rae_install_notice;

		$rae_tab_notice     = __( 'can be enabled in the REST API Extended add-on tab', 'affiliatewp-zapier' );
		$rae_install_notice = __( 'require the REST API Extended add-on', 'affiliatewp-zapier' );
		$rae_notice         = $this->is_rae_active() ? $rae_tab_notice : $rae_install_notice;

		$settings[ 'zapier' ] = array(
			'enable_zapier' => array(
				'name' => __( 'Enable Zapier', 'affiliatewp-zapier' ),
				'desc' => sprintf( __( 'Check this box to enable Zapier triggers. Once enabled, select your options below. <ul> <li>%3$s <a href="%1$s" target="_blank">Create a Zap on Zapier.com</a></li><li>%3$s <a href="%2$s" target="_blank">Documentation for this add-on</a></li><li>%3$s Zapier Action settings <a href="%4$s">%5$s</a>. <br />Zapier Actions enable you to create, edit, or delete AffiliateWP data.</li></ul>', 'affiliatewp-zapier' ),
					/**
					 * The Zapier Zap editor url displayed within plugin settings.
					 *
					 * @param  $zapier_url Zapier url to provide within plugin settings.
					 *
					 * @since  1.0
					 */
					esc_url( apply_filters( 'affwp_zapier_admin_zapier_url', $zapier_url ) ),
					/**
					 * The Help Scout docs url displayed within plugin settings.
					 *
					 * @param  $doc_url Help Scout docs url to provide within plugin settings.
					 *
					 * @since  1.0
					 */
					esc_url( apply_filters( 'affwp_zapier_admin_docs_url', $doc_url ) ),
					'<span class="dashicons dashicons-external"></span>',
					$rae_url,
					$rae_notice
				),
				'type' => 'checkbox'
			),
			'affwp_zapier_enable_object_types' => array(
				'name' => '<strong>' . __( 'Enable specific Zapier triggers', 'affiliatewp-zapier' ) . '</strong>',
				'type' => 'header'
			),
			'affwp_zapier_affiliates' => array(
				'name' => __( 'Affiliate triggers', 'affiliatewp-zapier' ),
				'desc' => __( 'Check to enable', 'affiliatewp-zapier' ),
				'type' => 'checkbox'
			),
			'affwp_zapier_referrals' => array(
				'name' => __( 'Referral triggers', 'affiliatewp-zapier' ),
				'desc' => __( 'Check to enable', 'affiliatewp-zapier' ),
				'type' => 'checkbox'
			),
			'affwp_zapier_creatives' => array(
				'name' => __( 'Creative triggers', 'affiliatewp-zapier' ),
				'desc' => __( 'Check to enable', 'affiliatewp-zapier' ),
				'type' => 'checkbox'
			),
			'affwp_zapier_visits' => array(
				'name' => __( 'Visit triggers', 'affiliatewp-zapier' ),
				'desc' => __( 'Check to enable', 'affiliatewp-zapier' ),
				'type' => 'checkbox'
			),
			'affwp_zapier_payouts' => array(
				'name' => __( 'Payout triggers', 'affiliatewp-zapier' ),
				'desc' => __( 'Check to enable', 'affiliatewp-zapier' ),
				'type' => 'checkbox'
			),


		);

		return $settings;
	}

	public function label() {
		$url = 'https://zapier.com/app/editor/';

		$label = wp_sprintf( __( 'The affiliate&#8217;s first and/or last name. Will be empty if no name is specified. This can be changed on the <a href="%1$s" alt="%2$s">create a Zap</a>.', 'affiliatewp-zapier' ),
			esc_url( $url ),
			esc_attr__( 'A link to the Zapier Zap editor.', 'affiliatewp-zapier' )
		);


		return $label;
	}

}
new AffiliateWP_Zapier_Admin;
