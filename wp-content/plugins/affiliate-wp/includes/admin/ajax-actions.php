<?php

// retrieves a list of users via live search
function affwp_search_users() {
	if ( empty( $_REQUEST['term'] ) ) {
		wp_die( -1 );
	}

	if ( ! current_user_can( 'manage_affiliates' ) ) {
		wp_die( -1 );
	}

	$search_query = htmlentities2( trim( $_REQUEST['term'] ) );

	/**
	 * Fires immediately prior to an AffiliateWP user search query.
	 *
	 * @param string $search_query The user search query.
	 */
	do_action( 'affwp_pre_search_users', $search_query );

	$args = array(
		'search_columns' => array( 'user_login', 'display_name', 'user_email' )
	);

	if ( isset( $_REQUEST['status'] ) ) {
		$status = mb_strtolower( htmlentities2( trim( $_REQUEST['status'] ) ) );

		switch ( $status ) {
			case 'none':
				$affiliate_users = affiliate_wp()->affiliates->get_affiliates(
					array(
						'number' => -1,
						'fields' => 'user_id',
					)
				);
				$args = array( 'exclude' => $affiliate_users );
				break;
			case 'any':
				$affiliate_users = affiliate_wp()->affiliates->get_affiliates(
					array(
						'number' => -1,
						'fields' => 'user_id',
					)
				);
				$args = array( 'include' => $affiliate_users );
				break;
			default:
				$affiliate_users = affiliate_wp()->affiliates->get_affiliates(
					array(
						'number' => -1,
						'status' => $status,
						'fields' => 'user_id',
					)
				);
				$args = array( 'include' => $affiliate_users );
		}
	}

	// Add search string to args.
	$args['search'] = '*' . mb_strtolower( htmlentities2( trim( $_REQUEST['term'] ) ) ) . '*';

	// Get users matching search.
	$found_users = get_users( $args );

	$user_list = array();

	if ( $found_users ) {
		foreach( $found_users as $user ) {
			$label = empty( $user->user_email ) ? $user->user_login : "{$user->user_login} ({$user->user_email})";

			$user_list[] = array(
				'label'   => $label,
				'value'   => $user->user_login,
				'user_id' => $user->ID
			);
		}
	}

	wp_die( json_encode( $user_list ) );
}
add_action( 'wp_ajax_affwp_search_users', 'affwp_search_users' );

/**
 * Handles Ajax for processing a single batch request.
 *
 * @since 2.0
 */
function affwp_process_batch_request() {
	// Batch ID.
	if ( ! isset( $_REQUEST['batch_id'] ) ) {
		wp_send_json_error( array(
			'error' => __( 'A batch process ID must be present to continue.', 'affiliate-wp' )
		) );
	} else {
		$batch_id = sanitize_key( $_REQUEST['batch_id'] );
	}

	// Nonce.
	if ( ! isset( $_REQUEST['nonce'] )
	     || ( isset( $_REQUEST['nonce'] ) && false === wp_verify_nonce( $_REQUEST['nonce'], "{$batch_id}_step_nonce") )
	) {
		wp_send_json_error( array(
			'error' => __( 'You do not have permission to initiate this request. Contact an administrator for more information.', 'affiliate-wp' )
		) );
	}

	// Attempt to retrieve the batch attributes from memory.
	if ( $batch_id && false === $batch = affiliate_wp()->utils->batch->get( $batch_id ) ) {
		wp_send_json_error( array(
			'error' => sprintf( __( '%s is an invalid batch process ID.', 'affiliate-wp' ), esc_html( $_REQUEST['batch_id'] ) )
		) );
	}

	$class      = isset( $batch['class'] ) ? sanitize_text_field( $batch['class'] ) : '';
	$class_file = isset( $batch['file'] ) ? $batch['file'] : '';

	if ( empty( $class_file ) ) {
		wp_send_json_error( array(
			'error' => sprintf( __( 'An invalid file path is registered for the %1$s batch process handler.', 'affiliate-wp' ), "<code>{$batch_id}</code>" )
		) );
	} else {
		require_once $class_file;
	}

	if ( empty( $class ) || ! class_exists( $class ) ) {
		wp_send_json_error( array(
			'error' => sprintf( __( '%1$s is an invalid handler for the %2$s batch process. Please try again.', 'affiliate-wp' ),
				"<code>{$class}</code>",
				"<code>{$batch_id}</code>"
			)
		) );
	}

	$step = sanitize_text_field( $_REQUEST['step'] );

	/**
	 * Instantiate the batch class.
	 *
	 * @var \AffWP\Utils\Batch_Process\Export|\AffWP\Utils\Batch_Process\Base $process
	 */
	$process = new $class( $step );

	$using_prefetch = ( $process instanceof \AffWP\Utils\Batch_Process\With_PreFetch );

	// Handle pre-fetching data.
	if ( $using_prefetch ) {
		// Initialize any data needed to process a step.
		$data = isset( $_REQUEST['form'] ) ? $_REQUEST['form'] : array();

		$process->init( $data );
		$process->pre_fetch();
	}

	/** @var int|string|\WP_Error $step */
	$step = $process->process_step();

	if ( is_wp_error( $step ) ) {
		wp_send_json_error( $step );
	} else {
		$response_data = array( 'step' => $step );

		// Finish and set the status flag if done.
		if ( 'done' === $step ) {
			$response_data['done'] = true;
			$response_data['message'] = $process->get_message( 'done' );

			// If this is an export class and not an empty export, send the download URL.
			if ( method_exists( $process, 'can_export' ) ) {

				if ( ! $process->is_empty ) {
					$response_data['url'] = affwp_admin_url( 'tools', array(
						'step'         => $step,
						'nonce'        => wp_create_nonce( 'affwp-batch-export' ),
						'batch_id'     => $batch_id,
						'affwp_action' => 'download_batch_export',
					) );
				}
			}

			// Once all calculations have finished, run cleanup.
			$process->finish();
		} else {
			$response_data['done'] = false;
			$response_data['percentage'] = $process->get_percentage_complete();
		}

		wp_send_json_success( $response_data );
	}

}
add_action( 'wp_ajax_process_batch_request', 'affwp_process_batch_request' );
