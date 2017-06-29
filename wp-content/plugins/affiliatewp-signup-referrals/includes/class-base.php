<?php

class AffiliateWP_Signup_Referrals_Base {

	public function __construct() {

	    // award commission on affiliate signup
	    add_action( 'affwp_register_user', array( $this, 'affiliate_signup' ), 1, 3 );

		// award commission when affiliate has been approved (affiliate approval enabled)
	    add_action( 'affwp_set_affiliate_status', array( $this, 'affiliate_approved' ), 10, 3 );

	    // EDD Registration
	    add_action( 'edd_insert_user', array( $this, 'edd_user_signup' ), 10, 2 );

	    // WooCommerce Registration
	    add_action( 'woocommerce_created_customer', array( $this, 'woocommerce_user_signup' ), 10, 3 );

	    // Gravity Forms user Registration
		add_action( 'gform_user_registered', array( $this, 'gravity_forms_user_signup' ), 10, 4 );

		// Ultimate Member user Registration
		add_action( 'um_after_new_user_register', array( $this, 'ultimate_member_user_signup' ), 10, 2 );

	    // link referral reference
	    add_filter( 'affwp_referral_reference_column', array( $this, 'reference_link' ), 10, 2 );

		// User Pro user Registration
		add_action( 'userpro_after_new_registration', array( $this, 'user_pro_user_signup' ), 10, 1 );

	}

	/**
	 * User Pro registration
	 *
	 * @since 1.0.0
	 */
	public function user_pro_user_signup( $user_id ) {

		// bail early if integration does not exist
		if ( ! affwp_sr_is_user_pro_active() ) {
		  return;
		}

		$args = array(
		  'user_id' => $user_id,
		  'context' => 'user_pro'
		);

		$this->add_referral( $args );
	}

	/**
	 * Ultimate Member registration
	 *
	 * @since 1.0.0
	 */
	public function ultimate_member_user_signup( $user_id, $args ) {

		// bail early if integration does not exist
		if ( ! affwp_sr_is_ultimate_member_active() ) {
		  return;
		}

		$args = array(
		  'user_id' => $user_id,
		  'context' => 'ultimate_member'
		);

		$this->add_referral( $args );
	}

	/**
	 * Gravity Forms user registration
	 *
	 * @since 1.0.0
	 */
	public function gravity_forms_user_signup( $user_id, $config, $lead, $password ) {

		// bail early if integration does not exist
		if ( ! affwp_sr_is_gravity_forms_active() ) {
		  return;
		}

		$args = array(
		  'user_id' => $user_id,
		  'context' => 'gravity_forms'
		);

		$this->add_referral( $args );
	}

	/**
	 * WooCommerce user registration
	 *
	 * @since 1.0.0
	 */
	public function woocommerce_user_signup( $customer_id, $new_customer_data, $password_generated ) {

		// bail early if integration does not exist
		if ( ! affwp_sr_is_woocommerce_active() ) {
		  return;
		}

		$args = array(
		  'user_id' => $customer_id,
		  'context' => 'woocommerce'
		);

		/**
		 * Block the referral when an account is created at WooCommerce checkout
		 * If not, 2 referrals could be created, one for the actual purchase and one for the registration
		 */
		if ( affiliate_wp()->settings->get( 'affwp_sr_wc_disable_checkout_referral' ) ) {
			if ( isset( $_POST['payment_method'] ) && isset( $_POST['createaccount'] ) && '1' === $_POST['createaccount'] ) {
				return;
			}
		}

		$this->add_referral( $args );
	}

	/**
	 * EDD user registration
	 *
	 * @since 1.0.0
	 * @todo detect if EDD is installed, and add option to set amount
	 */
	public function edd_user_signup( $user_id, $user_data ) {

		// bail early if integration does not exist
		if ( ! affwp_sr_is_edd_active() ) {
			return;
		}

		$args = array(
			'user_id' => $user_id,
			'context' => 'edd'
		);

		/**
		 * Block the referral when an account is created at EDD checkout
		 * If not, 2 referrals could be created, one for the actual purchase and one for the registration
		 */
		if ( affiliate_wp()->settings->get( 'affwp_sr_edd_disable_checkout_referral' ) ) {
			if ( isset( $_POST['edd_action'] ) && 'purchase' === $_POST['edd_action'] ) {
				return;
			}
		}

		// EDD user signed up, award commission to referring affiliate
		$this->add_referral( $args );
	}


	/**
	 * Affiliate signup from the standard affiliate registration form
	 *
	 * @since 1.0.0
	 */
	public function affiliate_signup( $affiliate_id, $status, $args ) {

		$args = array(
			'affiliate_id'     => $affiliate_id,
			'user_id'          => affwp_get_affiliate_user_id( $affiliate_id ),
			'affiliate_status' => $status,
			'context'          => 'affiliate'
		);

		// affiliate signed up, award commission to referring affiliate
		$this->add_referral( $args );

	}

	/**
	 * Award the commission after the new affiliate has been approved
	 * Runs when an affiliate's status has changed from "pending" to "active"
	 *
	 * @since 1.0.0
	 */
	public function affiliate_approved( $affiliate_id, $status, $old_status ) {

		if ( 'active' == $status && 'pending' == $old_status ) {

			$referring_affiliate_id = affwp_get_affiliate_meta( $affiliate_id, 'affwp_referring_affiliate_id', true );

			$args = array(
				'affiliate_id'           => $affiliate_id,
				'affiliate_status'       => $status,
				'context'                => 'affiliate',
				'referring_affiliate_id' => $referring_affiliate_id
			);

			// Add the referral
			$this->add_referral( $args );

		}

	}

		/**
		 * Get the referral description
		 *
		 * @since 1.0.0
		 */
	   	public function get_description( $context = '' ) {

		if ( ! $context ) {
			return false;
		}

        switch ( $context ) {

			case 'edd':
				$description = __( 'Easy Digital Downloads Registration', 'affiliatewp-signup-referrals' );
			break;

			case 'woocommerce':
				$description = __( 'WooCommerce Registration', 'affiliatewp-signup-referrals' );
			break;

			case 'affiliate':
				$description = __( 'Affiliate Registration', 'affiliatewp-signup-referrals' );
			break;

			case 'gravity_forms':
				$description = __( 'Gravity Forms User Registration', 'affiliatewp-signup-referrals' );
			break;

			case 'ultimate_member':
				$description = __( 'Ultimate Member Registration', 'affiliatewp-signup-referrals' );
			break;

			case 'user_pro':
				$description = __( 'User Pro Registration', 'affiliatewp-signup-referrals' );
			break;

        }

        return apply_filters( 'affwp_signup_referrals_description', $description, $context );

      }


		/**
		 * Add the referral
		 *
		 * @since 1.0.0
		 */
		public function add_referral( $args = array() ) {

			// get the referring affiliate ID from passed in value, otherwise from the cookie
			$referring_affiliate_id = isset( $args['referring_affiliate_id'] ) ? $args['referring_affiliate_id'] : affiliate_wp()->tracking->get_affiliate_id();

			// bail early if no referring affiliate ID
			if ( ! $referring_affiliate_id ) {
				return;
			}

			// the current affiliate being registered (if any)
			$affiliate_id = isset( $args['affiliate_id'] ) ? $args['affiliate_id'] : '';

			// The type of referral
			$context = isset( $args['context'] ) ? $args['context'] : '';

	        // Bail if referral type not permitted
	        if ( ! array_key_exists( $context, affwp_sr_allowed_referral_types() ) ) {
	          return;
	        }

			// User ID. Given when a user registers via WP registration form or similar
			$user_id = isset( $args['user_id'] ) ? $args['user_id'] : '';

			// check if affiliate approval is enabled
			$affiliate_approval = affiliate_wp()->settings->get( 'require_approval' );

			if ( 'affiliate' == $context ) {
				affwp_add_affiliate_meta( affwp_get_affiliate_id( $user_id ), 'affwp_referring_affiliate_id', $referring_affiliate_id );
			}

			// New affiliate status. Useful for when affiliate approval is enabled since the referral should only be awarded if the affiliate is accepted
			$affiliate_status = isset( $args['affiliate_status'] ) ? $args['affiliate_status'] : '';

			// bail if affiliate status is pending
			if ( 'pending' == $affiliate_status ) {
				return;
			}

			// Set amount
			$amount = affiliate_wp()->settings->get( 'affwp_sr_' . $context .'_registration_referral_amount' );

			// Set referral description
    		$referral_description = $this->get_description( $context );

			// Set up the reference
			if ( 'affiliate' == $context ) {
				$reference = $affiliate_id;
			} else {
				$reference = $user_id;
			}

			// Set status. Pending or Unpaid
			$referral_status = affiliate_wp()->settings->get( 'affwp_sr_referral_status' );

			// Check if the referring affiliate is valid
			if ( affwp_is_active_affiliate( $referring_affiliate_id ) ) {

				$args = apply_filters( 'affwp_signup_referrals_args', array(
					'affiliate_id' => $referring_affiliate_id,
					'amount'       => $amount,
					'status'       => $referral_status,
					'description'  => $referral_description,
					'reference'    => $reference,
					'visit_id'     => affiliate_wp()->tracking->get_visit_id(),
					'context'      => $context . '_signup' // context should be unique. We can't use edd or woocommerce since they already exist
				), $referring_affiliate_id, $amount, $referral_status, $referral_description, $reference, $context );

				$referral_id = affwp_add_referral( $args );

				// update visit
				affiliate_wp()->visits->update( affiliate_wp()->tracking->get_visit_id(), array( 'referral_id' => $referral_id ), '', 'visit' );
			}

		}

  		/**
  		 * Link the "reference" column value
  		 *
  		 * @since 1.0.0
  		 */
  		function reference_link( $reference = 0, $referral ) {

  			if ( empty( $referral->context ) ) {
  				return $reference;
  			}

  			if ( 'affiliate_signup' == $referral->context ) {
  				// link to the referred affiliate's edit screen
  				$url = admin_url( 'admin.php?page=affiliate-wp-affiliates&action=edit_affiliate&affiliate_id=' . $reference );
  			} else {
  				$url = admin_url( 'user-edit.php?user_id=' . $reference );
  			}

  			return '<a href="' . esc_url( $url ) . '">' . $reference . '</a>';

  		}

}
new AffiliateWP_Signup_Referrals_Base;
