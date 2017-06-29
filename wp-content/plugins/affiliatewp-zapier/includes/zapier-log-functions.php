<?php

/**
 * Generates an Affiliate_WP_Logging debug message, if enabled.
 * Pass either a custo merror string, or the key of an existing affiliatewp-zapier error.
 *
 *
 * @since  1.0
 *
 * @param  string  $message Error message string.
 *
 * @return void
 *
 * @see    AffiliateWP_Zapier::error
 * @uses   Affiliate_WP_Logging
 */
function affwp_zapier_error( $message ) {

	if ( empty( $message ) ) {
		return false;
	}

	$message = sanitize_text_field( $message );

	$errors = affiliatewp_zapier()->errors();

	if ( $errors && in_array( $message, $errors ) ) {
		affiliatewp_zapier()->error( affiliatewp_zapier()->errors[ $message ] );
	} else {
		affiliatewp_zapier()->error( $message );
	}

}

/**
 * Retrieves an AffilaiteWP Zapier log object.
 *
 * @since 1.0
 *
 * @param int|affwp_zapier_log $log Zapier Log ID or object.
 * @return affwp_zapier_log|false Zapier Log object, otherwise false.
 */
function affwp_zapier_get_log( $log = null ) {

	if ( is_object( $log ) && isset( $log->log_id ) ) {
		$log_id = $log->log_id;
	} elseif( is_numeric( $log ) ) {
		$log_id = absint( $log );
	} else {
		return false;
	}

	return affiliatewp_zapier()->logs->get_log( $log_id );
}

/**
 * Adds a new log to the database.
 *
 * @since  1.0
 * @return bool True if data added successfully, otherwise false.
 *
 * @see    Affiliate_WP_Zapier_DB::insert()
 */
function affwp_zapier_add_log( $data, $type ) {

	if ( affiliatewp_zapier()->logs->insert( $data, $type ) ) {

		/**
		 * Fires immediately after a log has been added.
		 *
		 * @param $data The log data.
		 * @param $type The object type being logged.
		 * @since  1.0.1
		 */
		do_action( 'affwp_zapier_add_log', $data, $type );

		/**
		 * Fires immediately after a log is inserted into the database.
		 * This action is fired only for events matching the action schema defined below.
		 *
		 * Format:
		 *     `affwp_zapier_log_<delete or update>_<object type>`
		 *
		 * Example:
		 *     `affwp_zapier_log_update_affiliate`
		 *
		 * @param $data The log data.
		 * @param $type The object type being logged.
		 * @since  1.0
		 */
		do_action( 'affwp_zapier_add_log_' . $data['action'] . '_' . $type, $data, $type );

		return true;
	}

	return false;
}

/**
 * Updates a Zapier log item.
 *
 * @since  1.0
 * @return bool
 *
 * @see    Affiliate_WP_Zapier_DB::update()
 */
function affwp_zapier_update_log( $log_id, $data ) {

	// Bail if the log ID cannot be determined.
	if ( ! isset( $log_id ) || empty( $log_id ) ) {
		if ( empty( $data['log_id'] ) || ( ! affwp_zapier_get_log( $data['log_id'] ) ) ) {
			return false;
		}
	}

	if ( affiliatewp_zapier()->logs->update( $log_id, $data ) ) {
		return true;
	}

	return false;
}

/**
 * Deletes a Zapier log object.
 *
 * @since  1.0
 * @param  $log_id  The AffiliateWP Zapier log ID.
 * @return bool
 */
function affwp_zapier_delete_log( $log_id ) {

	if ( ! affwp_zapier_get_log( $log_id ) ) {
		return false;
	}

	return affiliatewp_zapier()->logs->delete( $log_id );

}

/**
 * Event actions.
 *
 * The following event actions are associated
 * with the immediately proceeding functions.
 *
 * Each core object has an insert, update, and delete function defined,
 * with the exception of payouts, which does not have an update method
 * on which to hook.
 *
 */

/**
 * Update actions
 */
add_action( 'affwp_post_update_affiliate', 'affwp_zapier_post_update_affiliate', 10, 2 );
add_action( 'affwp_post_update_referral',  'affwp_zapier_post_update_referral',  10, 2 );
add_action( 'affwp_post_update_visit',     'affwp_zapier_post_update_visit',     10, 2 );
add_action( 'affwp_post_update_creative',  'affwp_zapier_post_update_creative',  10, 2 );

/**
 * Delete actions
 */
add_action( 'affwp_post_delete_affiliate', 'affwp_zapier_post_delete_affiliate', 10, 1 );
add_action( 'affwp_post_delete_referral',   'affwp_zapier_post_delete_referral', 10, 1 );
add_action( 'affwp_post_delete_visit',      'affwp_zapier_post_delete_visit',    10, 1 );
add_action( 'affwp_post_delete_creative',   'affwp_zapier_post_delete_creative', 10, 1 );
add_action( 'affwp_post_delete_payout',     'affwp_zapier_post_delete_payout',   10, 1 );

/**
 * Adds a Zapier log item when an affiliate is updated.
 *
 * @param $data   The object data.
 * @param $row_id The Affiliate ID.
 *
 * @since 1.0
 */
function affwp_zapier_post_update_affiliate( $data, $row_id ) {

	// Bail if affiliate data is being updated due to a referral or visit context.
	if ( ! isset( $row_id ) || did_action( 'affwp_zapier_after_add_referral_log' ) || did_action( 'affwp_zapier_after_add_visit_log' ) ) {

		if ( ! isset( $row_id ) ) {

			$message = __( 'The affiliate update failed to generate a Zapier log.', 'affiliatewp-zapier' );
			affwp_zapier_error( $message );
		}

		return false;
	}

	$log_data     = array();
	$affiliate    = affwp_get_affiliate( $row_id );
	$affiliate_id = $affiliate->affiliate_id;
	$user_id      = affwp_get_affiliate_user_id( $affiliate_id );

	$promotion_method = get_user_meta( $user_id, 'affwp_promotion_method', true );
	$website          = get_user_meta( $user_id, 'user_url', true );

	$meta = array(
		'affiliate_id'     => $affiliate_id,
		'email'            => affwp_get_affiliate_email( $affiliate_id ),
		'affiliate_name'   => affwp_get_affiliate_name( $affiliate_id ),
		'payment_email'    => affwp_get_affiliate_payment_email( $affiliate_id ),
		'username'         => affwp_get_affiliate_login( $affiliate_id ),
		'rate'             => affwp_get_affiliate_rate( $affiliate_id ),
		'rate_type'        => affwp_get_affiliate_rate_type( $affiliate_id ),
		'earnings'         => $affiliate->earnings,
		'referrals'        => $affiliate->referrals,
		'visits'           => $affiliate->visits,
		'status'           => $affiliate->status,
		'date_registered'  => $affiliate->date_registered,
		'notes'            => $affiliate->notes,
		'promotion_method' => $affiliate->$promotion_method,
		'website'          => $website


	);

	if ( empty( $meta['affiliate_name'] ) ) {
		$meta['affiliate_name'] = affwp_get_affiliate_name( $affiliate_id );
	}

	$log_data['object_id'] = $meta['affiliate_id'];
	$log_data['meta']      = json_encode( $meta );
	$log_data['object']    = 'affiliate';
	$log_data['action']    = 'update';

	affwp_zapier_add_log( $log_data, 'affiliate' );
}

/**
 * Adds a Zapier log item when an affiliate is deleted.
 *
 * @param $row_id  The database row id of the object.
 * @since 1.0
 */
function affwp_zapier_post_delete_affiliate( $row_id ) {

	// Bail if affiliate data is being updated due to a referral or visit context.
	if ( ! isset( $row_id ) || did_action( 'affwp_zapier_after_add_referral_log' ) || did_action( 'affwp_zapier_after_add_visit_log' ) ) {

		if ( ! isset( $row_id ) ) {
			$message = __( 'AffiliateWP Zapier - Affiliate deleted: No row id is available for this affiliate.', 'affiliatewp-zapier' );

			affwp_zapier_error( $message );
		}

		return false;
	}

	$log_data     = array();
	$affiliate    = affwp_get_affiliate( absint( $row_id ) );
	$affiliate_id = $affiliate->affiliate_id;
	$user_id      = affwp_get_affiliate_user_id( $affiliate_id );

	$promotion_method = get_user_meta( $user_id, 'affwp_promotion_method', true );

	$meta = array(
		'affiliate_id'     => $affiliate_id,
		'email'            => affwp_get_affiliate_email( $affiliate_id ),
		'affiliate_name'   => affwp_get_affiliate_name( $affiliate ),
		'payment_email'    => affwp_get_affiliate_payment_email( $affiliate_id ),
		'username'         => affwp_get_affiliate_login( $affiliate_id ),
		'rate'             => affwp_get_affiliate_rate( $affiliate_id ),
		'rate_type'        => affwp_get_affiliate_rate_type( $affiliate_id ),
		'earnings'         => $affiliate->earnings,
		'referrals'        => $affiliate->referrals,
		'visits'           => $affiliate->visits,
		'status'           => $affiliate->status,
		'date_registered'  => $affiliate->date_registered,
		'notes'            => $affiliate->notes,
		'promotion_method' => $affiliate->$promotion_method,
		'website'          => $website
	);

	if ( empty( $meta['affiliate_name'] ) ) {
		$meta['affiliate_name'] = affwp_get_affiliate_name( $affiliate_id );
	}

	if ( empty( $meta['affiliate_name'] ) ) {
		$meta['affiliate_name'] = affwp_get_affiliate_name( $object_id );
	}

	$log_data['meta']      = json_encode( $meta );
	$log_data['object']    = 'affiliate';
	$log_data['object_id'] = $affiliate_id;
	$log_data['action']    = 'delete';

	affwp_zapier_add_log( $log_data, 'affiliate' );
}

/**
 * Adds a Zapier log item when a referral is updated.
 *
 * @param $object The object.
 * @since 1.0
 */
function affwp_zapier_post_update_referral( $object, $object_id ) {

	if ( ! isset( $object_id ) ) {
		$object_id = $object['referral_id'];
	}

	if ( ! isset( $object ) ) {
		$object = affwp_get_referral( $object_id );
	}

	if ( ! $object && ! $object_id ) {
		affwp_zapier_error( 'Could not retrieve referral object in zapier post-update-referral.' );

		return false;
	}

	$log_data    = array();
	$referral    = affiliate_wp()->referrals->get_by( 'referral_id', $object_id );

	$meta = array(
		'referral_id'     => $referral->referral_id,
		'status'          => $referral->status,
		'affiliate_id'    => $referral->affiliate_id,
		'affiliate_name'  => affwp_get_affiliate_name( $row_id ),
		'email'           => affwp_get_affiliate_email( $referral->affiliate_id ),
		'payment_email'   => affwp_get_affiliate_payment_email( $referral->affiliate_id ),
		'date'            => $referral->date,
		'description'     => $referral->description,
		'amount'          => affwp_format_amount( $referral->amount ),
		'currency'        => strtoupper( $referral->currency ),
		'reference'       => $referral->reference,
		'context'         => $referral->context,
		'visit_id'        => $referral->visit_id
	);

	$log_data['object_id'] = $meta['referral_id'];
	$log_data['meta']      = json_encode( $meta );
	$log_data['object']    = 'referral';
	$log_data['action']    = 'update';

	affwp_zapier_add_log( $log_data, 'referral' );

	do_action( 'affwp_zapier_after_add_referral_log' );
}

/**
 * Adds a Zapier log item when a referral is deleted.
 *
 * @param $type    The object type.
 * @param $row_id  The database row id of the object.
 * @since 1.0
 */
function affwp_zapier_post_delete_referral( $row_id ) {

	if ( ! isset( $row_id ) ) {
		affwp_zapier_error( print_r( $row_id, true ) );
		return false;
	}

	$log_data    = array();
	$type        = 'referral';

	$referral    = affwp_get_referral( absint( $row_id ) );

	$meta = array(
		'referral_id'     => $referral->referral_id,
		'status'          => $referral->status,
		'affiliate_id'    => $referral->affiliate_id,
		'affiliate_name'  => affwp_get_affiliate_name( $row_id ),
		'email'           => affwp_get_affiliate_email( $referral->affiliate_id ),
		'payment_email'   => affwp_get_affiliate_payment_email( $referral->affiliate_id ),
		'date'            => $referral->date,
		'description'     => $referral->description,
		'amount'          => affwp_currency_filter( affwp_format_amount( $referral->amount ) ),
		'currency'        => strtoupper( $referral->currency ),
		'reference'       => $referral->reference,
		'context'         => $referral->context,
		'visit_id'        => $referral->visit_id
	);

	$log_data['meta']      = json_encode( $meta );
	$log_data['object']    = $type;
	$log_data['object_id'] = $row_id;
	$log_data['action']    = 'delete';

	affwp_zapier_add_log( $log_data, 'referral' );

	do_action( 'affwp_zapier_after_add_referral_log' );
}

/**
 * Adds a Zapier log item when a visit is updated.
 *
 * @param $object  The object data.
 * @param $type    The object type.
 * @since 1.0
 */
function affwp_zapier_post_update_visit( $object, $type ) {

	if ( ! isset( $object ) ) {
		return false;
	}

	$log_data    = array();
	$type        = 'visit';
	$visit       = affwp_get_visit( absint( $object['visit_id'] ) );

	$meta = array(
		'visit_id'     => $visit->visit_id,
		'referral_id'  => $visit->referral_id,
		'affiliate_id' => $visit->affiliate_id,
		'url'          => $visit->url,
		'referrer'     => $visit->referrer,
		'campaign'     => $visit->campaign,
		'ip'           => $visit->ip
	);

	$log_data['object_id'] = $meta['visit_id'];
	$log_data['meta']      = json_encode( $meta );
	$log_data['object']    = $type;
	$log_data['action']    = 'update';

	affwp_zapier_add_log( $log_data, 'visit' );

	do_action( 'affwp_zapier_after_add_visit_log' );

}

/**
 * Adds a Zapier log item when a visit is deleted.
 *
 * @param $type    The object type.
 * @param $row_id  The database row id of the object.
 * @since 1.0
 */
function affwp_zapier_post_delete_visit( $row_id ) {

	if ( ! isset( $row_id ) ) {
		affwp_zapier_error( print_r( $row_id, true ) );
		return false;
	}

	$log_data = array();
	$type     = 'visit';
	$visit    = affwp_get_visit( absint( $row_id ) );

	$meta = array(
		'visit_id'     => $visit->visit_id,
		'referral_id'  => $visit->referral_id,
		'affiliate_id' => $visit->affiliate_id,
		'url'          => $visit->url,
		'referrer'     => $visit->referrer,
		'campaign'     => $visit->campaign,
		'ip'           => $visit->ip
	);

	$log_data['meta']      = json_encode( $meta );
	$log_data['object']    = $type;
	$log_data['object_id'] = $row_id;
	$log_data['action']    = 'delete';

	affwp_zapier_add_log( $log_data, 'visit' );

	do_action( 'affwp_zapier_after_add_visit_log' );

}

/**
 * Adds a Zapier log item when a creative is updated.
 *
 * @param $type  The object type
 * @since 1.0
 */
function affwp_zapier_post_update_creative( $data, $row_id ) {

	if ( ! isset( $data ) ) {
		affwp_zapier_error( print_r( $data, true ) );
		return false;
	}

	$log_data    = array();
	$creative    = affwp_get_creative( absint( $row_id ) );

	$creative_id = $creative->creative_id;

	$meta = array(
		'name'        => $creative->name,
		'creative_id' => $creative_id,
        'description' => $creative->description,
        'url'         => $creative->url,
        'text'        => $creative->text,
        'image'       => $creative->image,
        'status'      => $creative->status,
        'date'        => $creative->date
	);

	$log_data['meta']      = json_encode( $meta );
	$log_data['object']    = 'creative';
	$log_data['object_id'] = $creative_id;
	$log_data['action']    = 'update';

	affwp_zapier_add_log( $log_data, 'creative' );

}

/**
 * Adds a Zapier log item when a creative is deleted.
 *
 * @param $type    The object type.
 * @param $row_id  The database row id of the object.
 * @since 1.0
 */
function affwp_zapier_post_delete_creative( $row_id ) {

	if ( ! isset( $row_id ) ) {
		affwp_zapier_error( print_r( $row_id, true ) );
		return false;
	}

	$log_data = array();
	$type     = 'creative';
	$creative = affwp_get_creative( absint( $row_id ) );

	$meta = array(
		'name'        => $creative->name,
        'description' => $creative->description,
        'url'         => $creative->url,
        'text'        => $creative->text,
        'image'       => $creative->image,
        'status'      => $creative->status,
        'date'        => $creative->date
	);

	$log_data['meta']      = json_encode( $meta );
	$log_data['object']    = $type;
	$log_data['object_id'] = $row_id;
	$log_data['action']    = 'delete';

	affwp_zapier_add_log( $log_data, 'creative' );
}

/**
 * Adds a Zapier log item when a payout is deleted.
 *
 * @param $type    The object type.
 * @param $row_id  The database row id of the object.
 * @since 1.0
 */
function affwp_zapier_post_delete_payout( $row_id ) {

	if ( ! isset( $row_id ) ) {
		affwp_zapier_error( print_r( $row_id, true ) );
		return false;
	}

	$log_data = array();
	$type     = 'payout';
	$payout   = affwp_get_payout( absint( $row_id ) );

	$meta = array(
		'payout_id'     => $payout->payout_id,
		'affiliate_id'  => $payout->affiliate_id,
		'referrals'     => $payout->referrals,
		'amount'        => affwp_currency_filter( affwp_format_amount( $payout->amount ) ),
		'payout_method' => $payout->payout_method,
		'status'        => $payout->status,
		'date'          => $payout->date,
		'owner'         => $payout->owner
	);

	$log_data['meta']      = json_encode( $meta );
	$log_data['object']    = $type;
	$log_data['object_id'] = $row_id;
	$log_data['action']    = 'delete';

	affwp_zapier_add_log( $log_data, 'payout' );
}

/**
 * The functions below provide additional fields to endpoints.
 */

/**
 * Add the affiliate email to referral endpoint objects.
 *
 * @since  1.1
 *
 * @param  object  $object Referral object.
 *
 * @return object  $object Modified referral object.
 *
 */
function affwp_zapier_referrals_add_affiliate_email() {

    affwp_register_rest_field( 'referral', 'affiliate_email', array(
	        'get_callback' => function( $object, $field_name, $request, $object_type ) {
	        	$affiliate_email = affwp_get_affiliate_email( $object->affiliate_id );
	            return $affiliate_email;
	        }
    	)
    );
}

add_action( 'rest_api_init', 'affwp_zapier_referrals_add_affiliate_email' );

/**
 * Add the affiliate name to referral endpoint objects.
 *
 * @since  1.0
 *
 * @param  object  $object Referral object.
 *
 * @return object  $object Modified referral object.
 *
 * @see    AffiliateWP/issues/1801
 */
function affwp_zapier_referrals_add_affiliate_name() {

    affwp_register_rest_field( 'referral', 'affiliate_name', array(
	        'get_callback' => function( $object, $field_name, $request, $object_type ) {
	        	$affiliate_name = affwp_get_affiliate_name( $object->affiliate_id );
	            return $affiliate_name;
	        }
    	)
    );
}

add_action( 'rest_api_init', 'affwp_zapier_referrals_add_affiliate_name' );

/**
 * Add the affiliate name to payout endpoint objects.
 *
 * @since  1.0
 *
 * @param  object  $object Payout object.
 *
 * @return object  $object Modified payout object.
 *
 * @see    AffiliateWP/issues/1801
 */
function affwp_zapier_payouts_add_affiliate_name() {

    affwp_register_rest_field( 'payout', 'affiliate_name', array(
	        'get_callback' => function( $object, $field_name, $request, $object_type ) {
	        	$affiliate_name = affwp_get_affiliate_name( $object->affiliate_id );
	            return $affiliate_name;
	        }
    	)
    );
}

add_action( 'rest_api_init', 'affwp_zapier_payouts_add_affiliate_name' );

/**
 * Add the affiliate name to visit endpoint objects.
 *
 * @since  1.0
 *
 * @param  object  $object Visit object.
 *
 * @return object  $object Modified visit object.
 *
 * @see    AffiliateWP/issues/1801
 */
function affwp_zapier_visits_add_affiliate_name() {

    affwp_register_rest_field( 'visit', 'affiliate_name', array(
	        'get_callback' => function( $object, $field_name, $request, $object_type ) {
	        	$affiliate_name = affwp_get_affiliate_name( $object->affiliate_id );
	            return $affiliate_name;
	        }
    	)
    );
}

add_action( 'rest_api_init', 'affwp_zapier_visits_add_affiliate_name' );

/**
 * Add the affiliate email to affiliate endpoint objects.
 *
 * @since  1.0
 *
 * @param  object  $object Affiliate object.
 *
 * @return object  $object Modified affiliate object.
 *
 * @see    AffiliateWP/issues/1801
 */
function affwp_zapier_affiliates_add_affiliate_email() {

    affwp_register_rest_field( 'affiliate', 'email', array(
	        'get_callback' => function( $object, $field_name, $request, $object_type ) {
	        	$affiliate_email = affwp_get_affiliate_email( $object->affiliate_id );
	            return $affiliate_email;
	        }
    	)
    );
}

add_action( 'rest_api_init', 'affwp_zapier_affiliates_add_affiliate_email' );

/**
 * Generates an error log each time a Zapier log is inserted into the database.
 *
 * @since  1.0.1
 *
 * @param  int     $insert_id Log ID.
 * @param  objecy  $data      Log data.
 *
 * @return void
 */
function affwp_zapier_insert_error_log( $insert_id, $data ) {

	if ( ! affiliatewp_zapier()->debug ) {
		return;
	}
}
add_action( 'affwp_zapier_add_log', 'affwp_zapier_insert_error_log', 10, 2 );
