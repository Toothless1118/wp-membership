<?php
namespace AffWP;

use \AffWP\REST\v1\Controller;

/**
 * Implements Zapier rest hooks
 *
 * @since 1.0
 *
 */
class Zapier_Endpoints extends Controller {

	/**
	 * Route base for zapier add-on.
	 *
	 * @since  1.0
	 * @access public
	 * @var    string
	 */
	public $rest_base = 'zapier';

	/**
	 * Constructor.
	 *
	 * Looks for a register_routes() method and hooks it up to 'rest_api_init'.
	 *
	 * @since 1.0
	 * @access public
	 * @see AffWP\REST\v1\Controller
	 */
	public function __construct() {

		add_action( 'rest_api_init', array( $this, 'register_routes' ), 15 );
		add_action( 'affwp_zapier_pre_return_response', array( $this, 'set_queried' ), 10, 2 );
	}

	/**
	 * Converts an object or array of objects into a \WP_REST_Response object.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @param  object|array $response Object or array of objects.
	 * @return \WP_REST_Response      REST response.
	 */
	public function response( $response ) {

		if ( is_array( $response ) ) {
			$response = array_map( function( $object ) {
				$object->id = $object->log_id;

				return $object;

			}, $response );
		}

		return rest_ensure_response( $response );
	}

	/**
	 * Check whether Zapier notifications are enabled for a specified object.
	 *
	 * @since  1.0
	 *
	 * @param  string  $object The core object plural term.
	 * @return bool            True if the specified setting is active, otherwise false.
	 */
	public function enabled( $object ) {

		$setting = 'affwp_zapier_' . $object;
		$enabled = affiliate_wp()->settings->get( $setting );

		if ( $enabled ) {
			return true;
		}

		return false;
	}
	/**
	 * Register endpoint routes for Zapier log items.
	 *
	 * Each core object contains two additional endpoints,
	 * except for payouts, which contains one.
	 *
	 * @since  1.0
	 * @access public
	 *
	 */
	public function register_routes() {

		if ( $this->enabled( 'affiliates' ) ) {

			// /zapier/affiliates/updated
			register_rest_route( $this->namespace, '/' . $this->rest_base . '/affiliates/updated', array(
				'methods'  => \WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_logs_updated' ),
				'args'     => $this->get_collection_params(),
				'permission_callback' => function( $request ) {
					return current_user_can( 'manage_affiliates' );
				}
			) );

			// /zapier/affiliates/deleted
			register_rest_route( $this->namespace, '/' . $this->rest_base . '/affiliates/deleted', array(
				'methods'  => \WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_logs_deleted' ),
				'args'     => $this->get_collection_params(),
				'permission_callback' => function( $request ) {
					return current_user_can( 'manage_affiliates' );
				}
			) );

		}

		if ( $this->enabled( 'referrals' ) ) {

			// /zapier/referrals/updated
			register_rest_route( $this->namespace, '/' . $this->rest_base . '/referrals/updated', array(
				'methods'  => \WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_logs_updated' ),
				'args'     => $this->get_collection_params(),
				'permission_callback' => function( $request ) {
					return current_user_can( 'manage_referrals' );
				}
			) );

			// /zapier/referrals/deleted
			register_rest_route( $this->namespace, '/' . $this->rest_base . '/referrals/deleted', array(
				'methods'  => \WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_logs_deleted' ),
				'args'     => $this->get_collection_params(),
				'permission_callback' => function( $request ) {
					return current_user_can( 'manage_referrals' );
				}
			) );

		}

		if ( $this->enabled( 'visits' ) ) {

			// /zapier/visits/updated
			register_rest_route( $this->namespace, '/' . $this->rest_base . '/visits/updated', array(
				'methods'  => \WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_logs_updated' ),
				'args'     => $this->get_collection_params(),
				'permission_callback' => function( $request ) {
					return current_user_can( 'manage_visits' );
				}
			) );

			// /zapier/visits/deleted
			register_rest_route( $this->namespace, '/' . $this->rest_base . '/visits/deleted', array(
				'methods'  => \WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_logs_deleted' ),
				'args'     => $this->get_collection_params(),
				'permission_callback' => function( $request ) {
					return current_user_can( 'manage_visits' );
				}
			) );

		}

		if ( $this->enabled( 'creatives' ) ) {

			// /zapier/creatives/updated
			register_rest_route( $this->namespace, '/' . $this->rest_base . '/creatives/updated', array(
				'methods'  => \WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_logs_updated' ),
				'args'     => $this->get_collection_params(),
				'permission_callback' => function( $request ) {
					return current_user_can( 'manage_creatives' );
				}
			) );

			// /zapier/creatives/deleted
			register_rest_route( $this->namespace, '/' . $this->rest_base . '/creatives/deleted', array(
				'methods'  => \WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_logs_deleted' ),
				'args'     => $this->get_collection_params(),
				'permission_callback' => function( $request ) {
					return current_user_can( 'manage_creatives' );
				}
			) );

		}

		if ( $this->enabled( 'payouts' ) ) {

			// /zapier/payouts/deleted
			register_rest_route( $this->namespace, '/' . $this->rest_base . '/payouts/deleted', array(
				'methods'  => \WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_logs_deleted' ),
				'args'     => $this->get_collection_params( 'payouts', 'deleted' ),
				'permission_callback' => function( $request ) {
					return current_user_can( 'manage_payouts' );
				}
			) );
		}

	}

	/**
	 * Endpoint to retrieve a log by ID.
	 *
	 * @since 1.0
	 * @access public
	 *
	 * @param  \WP_REST_Request            $request Request arguments.
	 * @return \affwp_zapier_log|\WP_Error          Log object or \WP_Error object if not found.
	 */
	public function get_item( $request ) {
		if ( ! $log = affwp_zapier_get_log( $request['log_id'] ) ) {
			$log = new \WP_Error(
				'invalid_log_id',
				'Invalid log ID',
				array( 'status' => 404 )
			);
		}

		return $this->response( $log );
	}

	/**
	 * Retrieves the collection parameters for creatives.
	 *
	 * @since 1.0
	 * @access public
	 * @return array
	 */
	public function get_collection_params() {
		$params = parent::get_collection_params();

		$params['context']['default'] = 'view';

		/*
		 * Pass top-level args as query vars:
		 * // /zapier/<object plural>/?status=inactive&order=desc
		 */
		$params['log_id'] = array(
			'description'       => __( 'The log ID or array of IDs to query for.', 'affiliatewp-zapier' ),
			'sanitize_callback' => 'absint',
			'validate_callback' => function( $param, $request, $key ) {
				return is_numeric( $param );
			},
		);

		$params['action'] = array(
			'description'       => __( 'The action for which to query; either `update` or `delete`. In the case of payout objects, only `delete` is a valid endpoint action parameter.', 'affiliatewp-zapier' ),
			'sanitize_callback' => 'sanitize_text_field',
			'validate_callback' => function( $param, $request, $key ) {
				return $action;
			},
		);

		$params['object'] = array(
			'description'       => __( 'The AffiliateWP core object type to query.', 'affiliatewp-zapier' ),
			'sanitize_callback' => 'sanitize_text_field',
			'validate_callback' => function( $param, $request, $key ) {
				return $param;
			},
		);


		$params['orderby'] = array(
			'description'       => __( 'Zapier logs table column to order by.', 'affiliatewp-zapier' ),
			'validate_callback' => function( $param, $request, $key ) {
				return array_key_exists( $param, affiliatewp_zapier()->logs->get_columns() );
			}
		);

		$params['filter'] = array(
			'description' => __( 'Use any get_logs() arguments to modify the response.', 'affiliatewp-zapier' )
		);

		/**
		 * Filters the collection parameters used to retrieve logs in a REST request.
		 *
		 * @since 1.0
		 *
		 * @param array            $params    Request parameters.
		 */
		return apply_filters( 'affwp_zapier_collection_params', $params );
	}

	/**
	 * Get the log items for updated obects.
	 *
	 * @since  1.0
	 *
	 * @param  \WP_REST_Request $request The request object.
	 *
	 * @return \WP_REST_Response         The response object.
	 */
	public function get_logs_updated( $request ) {

		$args = array();

		$args['number']  = isset( $request['number'] )  ? $request['number'] : 20;
		$args['offset']  = isset( $request['offset'] )  ? $request['offset'] : 0;
		$args['order']   = isset( $request['order'] )   ? $request['order'] : 'ASC';
		$args['orderby'] = isset( $request['orderby'] ) ? $request['orderby'] : '';
		$args['fields']  = isset( $request['fields'] )  ? $request['fields'] : '*';
		$args['log_id']  = isset( $request['log_id'] )  ? $request['log_id'] : 0;

		$args['object']  = isset( $request['object'] )  ? $request['object'] : '';

		if ( ! isset( $args['object'] ) ) {
			$args['object']  = isset( $_GET['object'] ) ? $_GET['object']    : '';
		}

		$args['action']  = isset( $request['action'] )  ? $request['action'] : 'update';

		if ( ! isset( $args['action'] ) ) {
			$args['action']  = isset( $_GET['action'] ) ? $_GET['action']    : 'update';
		}

		if ( is_array( $request['filter'] ) ) {
			$args = array_merge( $args, $request['filter'] );
			unset( $request['filter'] );
		}

		/**
		 * Filters the query arguments used to retrieve logs in a REST request.
		 *
		 * @since 1.0
		 *
		 * @param array            $args    Arguments.
		 * @param \WP_REST_Request $request Request.
		 */
		$args = apply_filters( 'affwp_zapier_rest_logs_updated_query_args', $args, $request );

		$logs = affiliatewp_zapier()->logs->get_logs( $args );

		if ( empty( $logs ) ) {
			$logs = new \WP_Error(
				'affwp_zapier_no_updated_object_logs',
				'No logs were found. Try updating an item on your site, and re-testing this Zap.',
				array( 'status' => 404 )
			);
		}

		/**
		 * Fires immediately before a rest reponse is returned to Zapier.
		 *
		 * @since  1.0
		 * @param  array             $logs     Log objects.
		 * @param  \WP_REST_Request  $request  Request object.
		 * @param  \WP_REST_Response $request  Response object.
		 */
		do_action( 'affwp_zapier_pre_return_response', $logs, $request );

		return $this->response( $logs );
	}

	/**
	 * Get the log items for deleted objects.
	 *
	 * @since  1.0
	 *
	 * @param  \WP_REST_Request $request The request object.
	 *
	 * @return \WP_REST_Response         The response object.
	 */
	public function get_logs_deleted( $request ) {

		$args = array();

		$args['number']  = isset( $request['number'] )  ? $request['number'] : 20;
		$args['offset']  = isset( $request['offset'] )  ? $request['offset'] : 0;
		$args['order']   = isset( $request['order'] )   ? $request['order'] : 'ASC';
		$args['orderby'] = isset( $request['orderby'] ) ? $request['orderby'] : '';
		$args['fields']  = isset( $request['fields'] )  ? $request['fields'] : '*';
		$args['log_id']  = isset( $request['log_id'] )  ? $request['log_id'] : 0;
		$args['object']  = isset( $request['object'] )  ? $request['object'] : '';
		$args['action']  = isset( $request['action'] )  ? $request['action'] : 'delete';

		if ( is_array( $request['filter'] ) ) {
			$args = array_merge( $args, $request['filter'] );
			unset( $request['filter'] );
		}

		/**
		 * Filters the query arguments used to retrieve logs in a REST request.
		 *
		 * @since 1.0
		 *
		 * @param array            $args    Arguments.
		 * @param \WP_REST_Request $request Request.
		 */
		$args = apply_filters( 'affwp_zapier_rest_logs_deleted_query_args', $args, $request );

		$logs = affiliatewp_zapier()->logs->get_logs( $args );

		if ( empty( $logs ) ) {
			$logs = new \WP_Error(
				'affwp_zapier_no_deleted_object_logs',
				'No logs were found. Try deleting an item on your site, and re-testing this Zap.',
				array( 'status' => 404 )
			);
		}

		/**
		 * This action is documented in the method `Zapier_Endpoints::get_logs_updated`.
		 */
		do_action( 'affwp_zapier_pre_return_response', $logs, $request );

		return $this->response( $logs );
	}

	/**
	 * Set a Zapier log items queried row to true
	 *
	 * @since  1.0
	 *
	 * @param  object  $logs    Log objects.
	 * @param  object  $request API request.
	 *
	 * @return void
	 *
	 * @see    AffWP\Zapier_Endpoints::get_logs_updated
	 * @see    AffWP\Zapier_Endpoints::get_logs_deleted
	 *
	 * @uses   affwp_zapier_update_log()
	 */
	public function set_queried( $logs, $request ) {

		if ( ! $logs || ! $request ) {
			return false;
		}

		$data = array(
			'queried' => true
		);

		if ( $logs ) {
			foreach ( $logs as $log ) {

				$log_id = $log->log_id;

				affwp_zapier_update_log( $log_id, $data );
			}
		}
	}

}

new Zapier_Endpoints;
