<?php
	$affiliate_id = affwp_get_affiliate_id();
?>

<div class="subscription-data">
<!-- Referrals Table -->
	<div class="sub-table  col-lg-6">
		<?php
		$per_page  = 30;
		$page      = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
		$pages     = absint( ceil( affwp_count_referrals( $affiliate_id ) / $per_page ) );
		$referrals = affiliate_wp()->referrals->get_referrals(
			array(
				'number'       => $per_page,
				'offset'       => $per_page * ( $page - 1 ),
				'affiliate_id' => $affiliate_id,
				'status'       => array( 'paid', 'unpaid', 'rejected' ),
			)
		);
		?>

		<?php
		/**
		 * Fires before the referrals dashbaord data able within the referrals template.
		 *
		 * @param int $affiliate_id Affiliate ID.
		 */
		do_action( 'affwp_referrals_dashboard_before_table', $affiliate_id );
		?>
		<table class="table table-hover">
	          <thead>
	            <tr>
	              <th class="center"><?php _e( 'Amount', 'affiliate-wp' ); ?></th>
	              <th class="center"><?php _e( 'Description', 'affiliate-wp' ); ?></th>
	              <th class="center"><?php _e( 'Status', 'affiliate-wp' ); ?></th>
	              <th class="center"><?php _e( 'Date', 'affiliate-wp' ); ?></th>
	            </tr>
	          </thead>
	          <tbody>
	          	<?php if ( $referrals ) : ?>
					<?php foreach ( $referrals as $referral ) : ?>
						<tr>
			              <th scope="row"><?php echo affwp_currency_filter( affwp_format_amount( $referral->amount ) ); ?></th>
			              <td><?php echo wp_kses_post( nl2br( $referral->description ) ); ?></td>
			              <td><?php echo affwp_get_referral_status_label( $referral ); ?></td>
			              <td><?php echo date_i18n( get_option( 'date_format' ), strtotime( $referral->date ) ); ?></td>
			              <?php
							/**
							 * Fires within the table data of the dashboard referrals template.
							 *
							 * @param \AffWP\Referral $referral Referral object.
							 */
							do_action( 'affwp_referrals_dashboard_td', $referral ); ?>
			            </tr>
			        <?php endforeach; ?>
			    <?php else : ?>
			    	<tr>
						<td colspan="4"><?php _e( 'You have not made any referrals yet.', 'affiliate-wp' ); ?></td>
					</tr>
				<?php endif; ?>
			</tbody>
		</table>

		<?php
		/**
		 * Fires after the data table within the affiliate area referrals template.
		 *
		 * @param int $affiliate_id Affiliate ID.
		 */
		do_action( 'affwp_referrals_dashboard_after_table', $affiliate_id );
		?>

		<?php if ( $pages > 1 ) : ?>

			<p class="affwp-pagination">
				<?php
				echo paginate_links(
					array(
						'current'      => $page,
						'total'        => $pages,
						'add_fragment' => '#affwp-affiliate-dashboard-referrals',
						'add_args'     => array(
							'tab' => 'referrals',
						),
					)
				);
				?>
			</p>

		<?php endif; ?>

	</div>
<!-- end Referrals Table -->
<!-- Referral Url -->
	<div class="referal-area col-lg-6">
        <h5 class="referal-title col-10 offset-1">Earn $50 every time someone signs up using your referral link.</h5>

		<?php
		/**
		 * Fires at the top of the Affiliate URLs dashboard tab.
		 *
		 * @param int $affiliate_id Affiliate ID of the currently logged-in affiliate.
		 */
		do_action( 'affwp_affiliate_dashboard_urls_top', $affiliate_id );
		?>

		<?php
		/**
		 * Fires just before the Referral URL Generator.
		 *
		 * @since 2.0.5
		 *
		 * @param int $affiliate_id Affiliate ID of the currently logged-in affiliate.
		 */
		do_action( 'affwp_affiliate_dashboard_urls_before_generator', $affiliate_id );
		?>

		<form id="affwp-generate-ref-url" class="affwp-form" method="get" action="#affwp-generate-ref-url">
			<div class="input-group ref-link ">
	          <input type="text" class="form-control copy-text" placeholder="<?php echo esc_url( urldecode( affwp_get_affiliate_referral_url() ) );?>" aria-describedby="basic-addon2" value="<?php echo esc_url( urldecode( affwp_get_affiliate_referral_url() ) );?>" readonly />
	          <a href="javascript: void(0);" class="input-group-addon copy-button" id="basic-addon2">Copy Link</a>
	        </div>
	        <?php
	        $twitter_default_text = affiliate_wp()->settings->get( 'ads_twitter_sharing_text' ) ? affiliate_wp()->settings->get( 'ads_twitter_sharing_text' ) : get_bloginfo( 'name' );

			?>
	        <!-- email sharing -->
	        <?php
				$share_url = esc_url( urldecode( affwp_get_affiliate_referral_url() ) );

					if ( affiliate_wp()->settings->get( 'ads_campaign_tracking' ) ) {
						$email_share_url = add_query_arg( 'utm_source', 'email', $share_url );
					} else {
						$email_share_url = $share_url;
					}

					$email_share_url = rawurlencode( $email_share_url );

					// email subject
					$email_subject = affiliate_wp()->settings->get( 'ads_email_subject' ) ? affiliate_wp()->settings->get( 'ads_email_subject' ) : get_bloginfo( 'name' );
					$email_subject = apply_filters( 'affwp_ads_subject', rawurlencode( $email_subject ) );

					// email body
					$email_body = affiliate_wp()->settings->get( 'ads_email_body' ) ? affiliate_wp()->settings->get( 'ads_email_body' ) : __( 'I thought you might be interested in this:', 'affwp-affiliate-dashboard-sharing' );
					$email_body = apply_filters( 'affwp_ads_body', $email_body . ' ' . $email_share_url, $email_body, $email_share_url );

					$email_share_text = apply_filters( 'affwp_ads_email_share_text', __( 'Share via email', 'affwp-affiliate-dashboard-sharing' ) );

			?>

			<!-- facebook sharing -->
			<?php

				$data_share             = affiliate_wp()->settings->get( 'ads_facebook_share_button' ) ? 'true' : 'false';
				$facebook_button_layout = 'button';

				if ( affiliate_wp()->settings->get( 'ads_campaign_tracking' ) ) {
					$facebook_share_url = esc_url( add_query_arg( 'utm_source', 'facebook', $share_url ) );
				} else {
					$facebook_share_url = $share_url;
				}
			?>

			<!-- twitter sharing -->
			<?php

				$twitter_count_box 		= 'vertical';
				$twitter_button_size 	= 'medium';


				$twitter_share_url = $share_url;
				
			?>

			<div class="input-group ref-btn-group">
				<a class="btn dfbtn-green" href="mailto:?subject=<?php echo $email_subject; ?>&amp;body=<?php echo $email_body; ?>"><i class="fa fa-envelope"></i> Email</a>
				<div class="fb-share-button" data-href="<?php echo $facebook_share_url; ?>" data-send="true" data-action="share" data-layout="<?php echo $facebook_button_layout; ?>" data-share="<?php echo $data_share; ?>" data-width="" data-show-faces="true" date-type="button" data-title="share" data-size="large"></div>
				<a class=" btn dfbtn-blue" href="https://twitter.com/share" data-text="<?php echo $twitter_default_text; ?>" data-lang="en" data-count="<?php echo $twitter_count_box; ?>" data-size="<?php echo $twitter_button_size; ?>" data-counturl="<?php echo $twitter_share_url; ?>" data-url="<?php echo $twitter_share_url; ?>"><i class="fa fa-twitter"></i> 
					<?php _e( 'Tweet', 'affwp-affiliate-dashboard-sharing' ); ?>
				</a>

			</div>
			<!--
			<div class="affwp-referral-url-submit-wrap">
				<input type="hidden" class="affwp-affiliate-id" value="<?php echo esc_attr( urldecode( affwp_get_referral_format_value() ) ); ?>" />
				<input type="hidden" class="affwp-referral-var" value="<?php echo esc_attr( affiliate_wp()->tracking->get_referral_var() ); ?>" />
				<input type="submit" class="button" value="<?php _e( 'Generate URL', 'affiliate-wp' ); ?>" />
			</div>
			-->
		</form>

	<?php
	/**
	 * Fires at the bottom of the Affiliate URLs dashboard tab.
	 *
	 * @since 2.0.5
	 *
	 * @param int $affiliate_id Affiliate ID of the currently logged-in affiliate.
	 */
	do_action( 'affwp_affiliate_dashboard_urls_bottom', $affiliate_id );
	?>

</div>
</div>
<!-- end Referral Url -->
<style>
  .fb-share-button span,.fb-share-button span iframe, 
	{
		width: 140px !important;
		height: 39px !important;
	}
	/*
  .fb-share-button
{
	width: 140px !important;
		height: 39px !important;
transform: scale(1.8, 1.4);
-ms-transform: scale(1.8, 1.4);
-webkit-transform: scale(1.8, 1.4);
-o-transform: scale(1.8, 1.4);
-moz-transform: scale(1.8, 1.4);
transform-origin: top left;
-ms-transform-origin: top left;
-webkit-transform-origin: top left;
-moz-transform-origin: top left;
-webkit-transform-origin: top left;
}
*/
</style>
<script>
	jQuery(document).ready(function ($) {
		if ( typeof (twttr) != 'undefined' ) {
			twttr.widgets.load();
		}
		else {
			$.getScript('//platform.twitter.com/widgets.js');
		}

		if ( typeof (FB) != 'undefined' ) {
			FB.init({
				status: true,
				cookie: true,
				xfbml: true,
				version: 'v2.6' // https://developers.facebook.com/docs/apps/changelog#versions
			});
		}
		else {
			$.getScript("//connect.facebook.net/en_US/all.js#xfbml=1", function () {
				FB.init({
					status: true,
					cookie: true,
					xfbml: true,
					version: 'v2.6'
				});
			});
		}

		
	});
	var copyTextareaBtn = document.querySelector('.copy-button');

		copyTextareaBtn.addEventListener('click', function(event) {
		  var copyTextarea = document.querySelector('.copy-text');
		  copyTextarea.select();

		  try {
		    var successful = document.execCommand('copy');
		    var msg = successful ? 'successful' : 'unsuccessful';
		    console.log('Copying text command was ' + msg);
		  } catch (err) {
		    console.log('Oops, unable to copy');
		  }
		});
</script>
<!--
<?php $active_tab = affwp_get_active_affiliate_area_tab(); ?>

<div id="affwp-affiliate-dashboard">

	<?php if ( 'pending' == affwp_get_affiliate_status( affwp_get_affiliate_id() ) ) : ?>

		<p class="affwp-notice"><?php _e( 'Your affiliate account is pending approval', 'affiliate-wp' ); ?></p>

	<?php elseif ( 'inactive' == affwp_get_affiliate_status( affwp_get_affiliate_id() ) ) : ?>

		<p class="affwp-notice"><?php _e( 'Your affiliate account is not active', 'affiliate-wp' ); ?></p>

	<?php elseif ( 'rejected' == affwp_get_affiliate_status( affwp_get_affiliate_id() ) ) : ?>

		<p class="affwp-notice"><?php _e( 'Your affiliate account request has been rejected', 'affiliate-wp' ); ?></p>

	<?php endif; ?>

	<?php if ( 'active' == affwp_get_affiliate_status( affwp_get_affiliate_id() ) ) : ?>

		<?php
		/**
		 * Fires at the top of the affiliate dashboard.
		 *
		 * @since 0.2
		 * @since 1.8.2 Added the `$active_tab` parameter.
		 *
		 * @param int|false $affiliate_id ID for the current affiliate.
		 * @param string    $active_tab   Slug for the currently-active tab.
		 */
		do_action( 'affwp_affiliate_dashboard_top', affwp_get_affiliate_id(), $active_tab );
		?>

		<?php if ( ! empty( $_GET['affwp_notice'] ) && 'profile-updated' == $_GET['affwp_notice'] ) : ?>

			<p class="affwp-notice"><?php _e( 'Your affiliate profile has been updated', 'affiliate-wp' ); ?></p>

		<?php endif; ?>

		<?php
		/**
		 * Fires inside the affiliate dashboard above the tabbed interface.
		 *
		 * @since 0.2
		 * @since 1.8.2 Added the `$active_tab` parameter.
		 *
		 * @param int|false $affiliate_id ID for the current affiliate.
		 * @param string    $active_tab   Slug for the currently-active tab.
		 */
		do_action( 'affwp_affiliate_dashboard_notices', affwp_get_affiliate_id(), $active_tab );
		?>

		<ul id="affwp-affiliate-dashboard-tabs">
			<?php if ( affwp_affiliate_area_show_tab( 'urls' ) ) : ?>
			<li class="affwp-affiliate-dashboard-tab<?php echo $active_tab == 'urls' ? ' active' : ''; ?>">
				<a href="<?php echo esc_url( affwp_get_affiliate_area_page_url( 'urls' ) ); ?>"><?php _e( 'Affiliate URLs', 'affiliate-wp' ); ?></a>
			</li>
			<?php endif; ?>

			<?php if ( affwp_affiliate_area_show_tab( 'stats' ) ) : ?>
			<li class="affwp-affiliate-dashboard-tab<?php echo $active_tab == 'stats' ? ' active' : ''; ?>">
				<a href="<?php echo esc_url( affwp_get_affiliate_area_page_url( 'stats' ) ); ?>"><?php _e( 'Statistics', 'affiliate-wp' ); ?></a>
			</li>
			<?php endif; ?>

			<?php if ( affwp_affiliate_area_show_tab( 'graphs' ) ) : ?>
			<li class="affwp-affiliate-dashboard-tab<?php echo $active_tab == 'graphs' ? ' active' : ''; ?>">
				<a href="<?php echo esc_url( affwp_get_affiliate_area_page_url( 'graphs' ) ); ?>"><?php _e( 'Graphs', 'affiliate-wp' ); ?></a>
			</li>
			<?php endif; ?>

			<?php if ( affwp_affiliate_area_show_tab( 'referrals' ) ) : ?>
			<li class="affwp-affiliate-dashboard-tab<?php echo $active_tab == 'referrals' ? ' active' : ''; ?>">
				<a href="<?php echo esc_url( affwp_get_affiliate_area_page_url( 'referrals' ) ); ?>"><?php _e( 'Referrals', 'affiliate-wp' ); ?></a>
			</li>
			<?php endif; ?>

			<?php if ( affwp_affiliate_area_show_tab( 'payouts' ) ) : ?>
			<li class="affwp-affiliate-dashboard-tab<?php echo $active_tab == 'payouts' ? ' active' : ''; ?>">
				<a href="<?php echo esc_url( affwp_get_affiliate_area_page_url( 'payouts' ) ); ?>"><?php _e( 'Payouts', 'affiliate-wp' ); ?></a>
			</li>
			<?php endif; ?>

			<?php if ( affwp_affiliate_area_show_tab( 'visits' ) ) : ?>
			<li class="affwp-affiliate-dashboard-tab<?php echo $active_tab == 'visits' ? ' active' : ''; ?>">
				<a href="<?php echo esc_url( affwp_get_affiliate_area_page_url( 'visits' ) ); ?>"><?php _e( 'Visits', 'affiliate-wp' ); ?></a>
			</li>
			<?php endif; ?>

			<?php if ( affwp_affiliate_area_show_tab( 'creatives' ) ) : ?>
			<li class="affwp-affiliate-dashboard-tab<?php echo $active_tab == 'creatives' ? ' active' : ''; ?>">
				<a href="<?php echo esc_url( affwp_get_affiliate_area_page_url( 'creatives' ) ); ?>"><?php _e( 'Creatives', 'affiliate-wp' ); ?></a>
			</li>
			<?php endif; ?>

			<?php if ( affwp_affiliate_area_show_tab( 'settings' ) ) : ?>
			<li class="affwp-affiliate-dashboard-tab<?php echo $active_tab == 'settings' ? ' active' : ''; ?>">
				<a href="<?php echo esc_url( affwp_get_affiliate_area_page_url( 'settings' ) ); ?>"><?php _e( 'Settings', 'affiliate-wp' ); ?></a>
			</li>
			<?php endif; ?>

			<?php
			/**
			 * Fires immediately after core Affiliate Area tabs are output,
			 * but before the 'Log Out' tab is output (if enabled).
			 *
			 * @since 0.2
			 *
			 * @param int    $affiliate_id ID of the current affiliate.
			 * @param string $active_tab   Slug of the active tab.
			 */
			do_action( 'affwp_affiliate_dashboard_tabs', affwp_get_affiliate_id(), $active_tab );
			?>

			<?php if ( affiliate_wp()->settings->get( 'logout_link' ) ) : ?>
			<li class="affwp-affiliate-dashboard-tab">
				<a href="<?php echo esc_url( affwp_get_logout_url() ); ?>"><?php _e( 'Log out', 'affiliate-wp' ); ?></a>
			</li>
			<?php endif; ?>

		</ul>

		<?php
		if ( ! empty( $active_tab ) && affwp_affiliate_area_show_tab( $active_tab ) ) :
			affiliate_wp()->templates->get_template_part( 'dashboard-tab', $active_tab );
		endif;
		?>

		<?php
		/**
		 * Fires at the bottom of the affiliate dashboard.
		 *
		 * @since 0.2
		 * @since 1.8.2 Added the `$active_tab` parameter.
		 *
		 * @param int|false $affiliate_id ID for the current affiliate.
		 * @param string    $active_tab   Slug for the currently-active tab.
		 */
		do_action( 'affwp_affiliate_dashboard_bottom', affwp_get_affiliate_id(), $active_tab );
		?>

	<?php endif; ?>

</div>
-->