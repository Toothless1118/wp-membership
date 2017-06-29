<?php

class Affiliate_WP_Stripe extends Affiliate_WP_Base {

	/**
	 * Get things started
	 *
	 * @access  public
	 * @since   2.0
	 */
	public function init() {

		$this->context = 'stripe';

		add_action( 'simpay_charge_created', array( $this, 'insert_referral' ) );

		add_filter( 'affwp_referral_reference_column', array( $this, 'reference_link' ), 10, 2 );

	}


	/**
	 * Create a referral during stripe form submission if customer was referred
	 *
	 * @access  public
	 * @since   2.0
	*/
	public function insert_referral( $charge ) {

		if( $this->was_referred() ) {

			if( $this->is_zero_decimal( $charge->currency ) ) {
				$amount = $charge->amount;
			} else {
				$amount = round( $charge->amount / 100, 2 );
			}

			if( is_object( $charge->customer ) && ! empty( $charge->customer->email ) ) {
				$email = $charge->customer->email;
			} else {
				$email = sanitize_text_field( $_POST['stripeEmail'] );
			}

			if( $this->is_affiliate_email( $email, $this->affiliate_id ) ) {

				if( $this->debug ) {
					$this->log( 'Referral not created because affiliate\'s own account was used.' );
				}

				return;

			}

			$referral_total = $this->calculate_referral_amount( $amount, $charge->id );
			$referral_id    = $this->insert_pending_referral( $referral_total, $charge->id, $charge->description, array(), array( 'livemode' => $charge->livemode ) );

			if( $referral_id && $this->debug ) {

				$this->log( 'Pending referral created successfully during insert_referral()' );

				if( $this->complete_referral( $charge->id ) && $this->debug ) {

					$this->log( 'Referral completed successfully during insert_referral()' );

				}

			} elseif ( $this->debug ) {

				$this->log( 'Pending referral failed to be created during insert_referral()' );

			}

		}

	}

	/**
	 * Determine if this is a zero decimal currency
	 *
	 * @access public
	 * @since  2.0
	 * @param  $currency String The currency code
	 * @return bool
	 */
	public function is_zero_decimal( $currency ) {

		$is_zero = array(
			'BIF',
			'CLP',
			'DJF',
			'GNF',
			'JPY',
			'KMF',
			'KRW',
			'MGA',
			'PYG',
			'RWF',
			'VND',
			'VUV',
			'XAF',
			'XOF',
			'XPF',
		);

		return in_array( strtoupper( $currency ), $is_zero );
	}

	/**
	 * Sets up the reference link in the Referrals table
	 *
	 * @access  public
	 * @since   2.0
	*/
	public function reference_link( $reference = 0, $referral ) {

		if ( empty( $referral->context ) || 'stripe' != $referral->context ) {

			return $reference;

		}

		$test = '';

		if( ! empty( $referral->custom ) ) {
			$custom = maybe_unserialize( $referral->custom );
			$test   = empty( $custom['livemode'] ) ? 'test/' : '';
		}

		$url = 'https://dashboard.stripe.com/' . $test . 'payments/' . $reference ;

		return '<a href="' . esc_url( $url ) . '">' . $reference . '</a>';
	}

}
new Affiliate_WP_Stripe;