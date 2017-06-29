<?php
namespace AffWP\Visit\Admin\Reports;

use AffWP\Admin\Reports;

/**
 * Implements a core 'Visits' tab for the Reports screen.
 *
 * @since 1.9
 *
 * @see \AffWP\Admin\Reports\Tab
 */
class Tab extends Reports\Tab {

	/**
	 * Sets up the Visits tab for Reports.
	 *
	 * @access public
	 * @since  1.9
	 */
	public function __construct() {
		$this->tab_id   = 'visits';
		$this->label    = __( 'Visits', 'affiliate-wp' );
		$this->priority = 0;
		$this->graph    = new \Affiliate_WP_Visits_Graph;

		parent::__construct();
	}

	/**
	 * Registers the Visits tab tiles.
	 *
	 * @access public
	 * @since  1.9
	 */
	public function register_tiles() {
		$this->register_tile( 'total_visits', array(
			'label'           => __( 'Total Visits', 'affiliate-wp' ),
			'type'            => 'number',
			'data'            => affiliate_wp()->visits->count(),
			'comparison_data' => __( 'All Time', 'affiliate-wp' ),
		) );

		$this->register_tile( 'total_visits_date', array(
			'label'           => __( 'Total Visits', 'affiliate-wp' ),
			'type'            => 'number',
			'context'         => 'secondary',
			'data'            => affiliate_wp()->visits->count( array(
				'date' => $this->date_query,
			) ),
			'comparison_data' => $this->get_date_comparison_label(),
		) );

		$converted = affiliate_wp()->visits->count( array(
			'number'          => -1,
			'referral_status' => 'converted',
			'date'            => $this->date_query,
		) );

		$this->register_tile( 'converted_visits', array(
			'label'           => __( 'Successful Conversions', 'affiliate-wp' ),
			'type'            => 'number',
			'context'         => 'tertiary',
			'data'            => $converted,
			'comparison_data' => $this->get_date_comparison_label(),
		) );

		$total_visits = affiliate_wp()->visits->count( array(
			'date'   => $this->date_query,
		) );

		$this->register_tile( 'conversion_rate', array(
			'label'           => __( 'Conversion Rate', 'affiliate-wp' ),
			'type'            => 'rate',
			'data'            => $total_visits > 0 ? round( ( $converted / $total_visits ), 2 ) : 0,
			'comparison_data' => $this->get_date_comparison_label(),
		) );

		$this->top_referrer_tile();
	}

	/**
	 * Registers the 'Top Referrer' date-based tile.
	 *
	 * @access public
	 * @since  1.9
	 */
	public function top_referrer_tile() {
		// Top Referrer.
		$referrers = affiliate_wp()->visits->get_visits( array(
			'number' => -1,
			'fields' => 'referrer',
			'date'   => $this->date_query,
		) );

		if ( $referrers ) {
			$counts = array_count_values( array_map( function( $referrer ) {
				if ( empty( $referrer ) ) {
					$referrer = 'direct';
				}
				return $referrer;
			}, $referrers ) );

			arsort( $counts );

			$top_referrer = array_keys( $counts );
			$top_referrer = reset( $top_referrer );

			$this->register_tile( 'top_referrer', array(
				'label'           => __( 'Top Referrer', 'affiliate-wp' ),
				'context'         => 'secondary',
				'type'            => 'url',
				'data'            => 'direct' === $top_referrer ? __( 'Direct Traffic', 'affiliate-wp' ) : $top_referrer,
				'comparison_data' => $this->get_date_comparison_label(),
			) );
		} else {
			$this->register_tile( 'top_referrer', array(
				'label'           => __( 'Top Referrer', 'affiliate-wp' ),
				'context'         => 'secondary',
				'data'            => '',
				'comparison_data' => $this->get_date_comparison_label(),
			) );
		}
	}

	/**
	 * Handles displaying the 'Trends' graph.
	 *
	 * @access public
	 * @since  1.9
	 */
	public function display_trends() {
		$this->graph->set( 'show_controls', false );
		$this->graph->set( 'x_mode', 'time' );
		$this->graph->display();
	}

}
