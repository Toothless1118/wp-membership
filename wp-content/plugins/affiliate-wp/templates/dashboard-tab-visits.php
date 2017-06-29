<div id="affwp-affiliate-dashboard-visits" class="affwp-tab-content">

	<h4><?php _e( 'Referral URL Visits', 'affiliate-wp' ); ?></h4>

	<span id="affwp-table-summary" class="screen-reader-text">
		<?php _e( 'Column one lists the visit URL in relative format, column two lists the referrer, and column three indicates whether the visit converted into a referral.', 'affiliate-wp' ); ?>
	</span>

	<?php
	if ( wp_script_is( 'affwp-forms', 'enqueued' ) ) {
		affwp_enqueue_script( 'dashicons', 'visits' );
	}

	$per_page = 30;
	$page     = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
	$pages    = absint( ceil( affwp_get_affiliate_visit_count( affwp_get_affiliate_id() ) / $per_page ) );
	$visits   = affiliate_wp()->visits->get_visits(
		array(
			'number'       => $per_page,
			'offset'       => $per_page * ( $page - 1 ),
			'affiliate_id' => affwp_get_affiliate_id(),
		)
	);
	?>

	<table id="affwp-affiliate-dashboard-visits" class="affwp-table" aria-describedby="affwp-table-summary">
		<thead>
			<tr>
				<th class="visit-url"><?php _e( 'URL', 'affiliate-wp' ); ?></th>
				<th class="referring-url"><?php _e( 'Referring URL', 'affiliate-wp' ); ?></th>
				<th class="referral-status"><?php _e( 'Converted', 'affiliate-wp' ); ?></th>
				<th class="visit-date"><?php _e( 'Date', 'affiliate-wp' ); ?></th>
			</tr>
		</thead>

		<tbody>
			<?php if ( $visits ) : ?>

				<?php foreach ( $visits as $visit ) : ?>
					<tr>
						<td>
							<a href="<?php echo esc_url( $visit->url ); ?>" title="<?php echo esc_attr( $visit->url ); ?>">
								<?php echo affwp_make_url_human_readable( $visit->url ); ?>
							</a>
						</td>
						<td><?php echo ! empty( $visit->referrer ) ? $visit->referrer : __( 'Direct traffic', 'affiliate-wp' ); ?></td>
						<td>
							<?php $converted = ! empty( $visit->referral_id ) ? 'yes' : 'no'; ?>
							<span class="visit-converted <?php echo esc_attr( $converted ); ?>" aria-label="<?php printf( esc_attr__( 'Visit converted: %s', 'affiliate-wp' ), $converted ); ?>">
								<i></i>
							</span>
						</td>
						<td>
							<?php echo esc_html( date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $visit->date ) ) ); ?>
						</td>
					</tr>
				<?php endforeach; ?>

			<?php else : ?>

				<tr>
					<td colspan="4"><?php _e( 'You have not received any visits yet.', 'affiliate-wp' ); ?></td>
				</tr>

			<?php endif; ?>
		</tbody>
	</table>

	<?php if ( $pages > 1 ) : ?>

		<p class="affwp-pagination">
			<?php
			echo paginate_links(
				array(
					'current'      => $page,
					'total'        => $pages,
					'add_fragment' => '#affwp-affiliate-dashboard-visits',
					'add_args'     => array(
						'tab' => 'visits',
					),
				)
			);
			?>
		</p>

	<?php endif; ?>

</div>
