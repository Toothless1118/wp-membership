<?php
namespace AffWP\Referral\Admin\Reports;

use AffWP\Admin\Reports;

/**
 * Implements a core 'Referrals' tab for the Reports screen.
 *
 * @since 1.9
 *
 * @see \AffWP\Admin\Reports\Tab
 */
class Tab extends Reports\Tab {

	/**
	 * Sets up the Referrals tab for Reports.
	 *
	 * @access public
	 * @since  1.9
	 */
	public function __construct() {
		$this->tab_id   = 'referrals';
		$this->label    = __( 'Referrals', 'affiliate-wp' );
		$this->priority = 0;
		$this->graph    = new \Affiliate_WP_Referrals_Graph;

		parent::__construct();
	}

	/**
	 * Registers the Referrals tab tiles.
	 *
	 * @access public
	 * @since  1.9
	 */
	public function register_tiles() {
		$this->register_tile( 'all_time_paid_earnings', array(
			'label'           => __( 'Paid Earnings', 'affiliate-wp' ),
			'type'            => 'amount',
			'data'            => array_sum( affiliate_wp()->referrals->get_referrals( array(
				'number' => -1,
				'fields' => 'amount',
				'status' => 'paid'
			) ) ),
			'comparison_data' => __( 'All Time', 'affiliate-wp' )
		) );

		$this->register_tile( 'paid_earnings', array(
			'label'           => __( 'Paid Earnings', 'affiliate-wp' ),
			'context'         => 'secondary',
			'type'            => 'amount',
			'data'            => affiliate_wp()->referrals->paid_earnings( $this->date_query, 0, false ),
			'comparison_data' => $this->get_date_comparison_label(),
		) );

		$this->register_tile( 'unpaid_earnings', array(
			'label'           => __( 'Unpaid Earnings', 'affiliate-wp' ),
			'context'         => 'tertiary',
			'type'            => 'amount',
			'data'            => affiliate_wp()->referrals->unpaid_earnings( $this->date_query, 0, false ),
			'comparison_data' => $this->get_date_comparison_label(),
		) );

		$this->register_tile( 'unpaid_referrals', array(
			'label'   => __( 'Unpaid Referrals', 'affiliate-wp' ),
			'type'    => 'number',
			'data'    => affiliate_wp()->referrals->unpaid_count( $this->date_query ),
			'comparison_data' => $this->get_date_comparison_label(),
		) );

		$all_referrals = affiliate_wp()->referrals->get_referrals( array(
			'number' => -1,
			'fields' => 'amount',
		) );

		if ( ! $all_referrals ) {
			$all_referrals = array( 0 );
		}

		$this->register_tile( 'average_referral', array(
			'label'           => __( 'Average Referral Amount', 'affiliate-wp' ),
			'type'            => 'amount',
			'context'         => 'secondary',
			'data'            => array_sum( $all_referrals ) / count( $all_referrals ),
			'comparison_data' => $this->get_date_comparison_label(),
		) );
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
