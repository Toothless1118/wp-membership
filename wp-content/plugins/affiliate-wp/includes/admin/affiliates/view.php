<?php

$affiliate_id = isset( $_GET['affiliate_id'] ) ? absint( $_GET['affiliate_id'] ) : 0;

?>
<div class="wrap">
	<h2><?php printf( __( 'Affiliate: #%d %s', 'affiliate-wp' ), $affiliate_id, affiliate_wp()->affiliates->get_affiliate_name( $affiliate_id ) ); ?></h2>

	<?php
	/**
	 * Fires at the top of the view-affiliate report admin screen.
	 */
	do_action( 'affwp_view_affiliate_report_top' );
	?>

	<h3><?php _e( 'Earnings', 'affiliate-wp' ); ?></h3>

	<table id="affwp_affiliate_stats" class="affwp_table">

		<thead>

			<tr>
				<th><?php _e( 'Total earnings', 'affiliate-wp' ); ?></th>
				<th><?php _e( 'Total unpaid earnings', 'affiliate-wp' ); ?></th>
				<th><?php _e( 'Total paid referrals', 'affiliate-wp' ); ?></th>
				<th><?php _e( 'Total unpaid referrals', 'affiliate-wp' ); ?></th>
				<th><?php _e( 'Total pending referrals', 'affiliate-wp' ); ?></th>
				<th><?php _e( 'Total rejected referrals', 'affiliate-wp' ); ?></th>
				<th><?php _e( 'Total visits', 'affiliate-wp' ); ?></th>
				<th><?php _e( 'Conversion rate', 'affiliate-wp' ); ?></th>
				<?php
				/**
				 * Fires in the view-affiliate-report screens table element header.
				 *
				 * @param int $affiliate_id Affiliate ID.
				 */
				do_action( 'affwp_view_affiliate_report_table_header', $affiliate_id );
				?>
			</tr>

		</thead>

		<tbody>

			<tr>
				<td><?php echo affwp_get_affiliate_earnings( $affiliate_id, true ); ?></td>
				<td><?php echo affwp_get_affiliate_unpaid_earnings( $affiliate_id, true ); ?></td>
				<td><?php echo affwp_get_affiliate_referral_count( $affiliate_id ); ?></td>
				<td><?php echo affiliate_wp()->referrals->count( array( 'affiliate_id' => $affiliate_id, 'status' => 'unpaid' ) ); ?></td>
				<td><?php echo affiliate_wp()->referrals->count( array( 'affiliate_id' => $affiliate_id, 'status' => 'pending' ) ); ?></td>
				<td><?php echo affiliate_wp()->referrals->count( array( 'affiliate_id' => $affiliate_id, 'status' => 'rejected' ) ); ?></td>
				<td><?php echo affwp_get_affiliate_visit_count( $affiliate_id ); ?></td>
				<td><?php echo affwp_get_affiliate_conversion_rate( $affiliate_id ); ?></td>
				<?php
				/**
				 * Fires at the bottom of view-affiliate-report screens table element rows.
				 *
				 * @param int $affiliate_id Affiliate ID.
				 */
				do_action( 'affwp_view_affiliate_report_table_row', $affiliate_id );
				?>
			</tr>

		</tbody>

	</table>
	<?php
	$graph = new Affiliate_WP_Referrals_Graph;
	$graph->set( 'x_mode', 'time' );
	$graph->set( 'affiliate_id', $affiliate_id );
	$graph->display();

	// Recent Payouts.
	$payouts_table = new AffWP_Payouts_Table( array(
		'query_args' => array(
			'affiliate_id' => $affiliate_id
		),
		'display_args' => array(
			'hide_bulk_options'    => true,
			'columns_to_hide'      => array( 'status' ),
			'hide_column_controls' => true,
		),
	) );
	$payouts_table->prepare_items();
	?>
	<h2><?php _e( 'Recent Payouts', 'affiliate-wp' ); ?></h2>

	<?php $payouts_table->views(); ?>
	<?php $payouts_table->display(); ?>

	<?php
	/**
	 * Fires at the bottom of view-affiliate-report screens.
	 */
	do_action( 'affwp_view_affiliate_report_bottom' );
	?>

</div>
