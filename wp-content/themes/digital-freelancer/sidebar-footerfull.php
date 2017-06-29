<?php
/**
 * Sidebar setup for footer full.
 *
 * @package understrap
 */

$container   = get_theme_mod( 'understrap_container_type' );

?>
<?php
    // Thank you modal after users sign up
    if (isset($_GET['membership'])) {
?>
<div class="modal fade bd-example-modal-lg" id="df_thankyou_modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Thank you</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            Thank you for your registration on Digital Freelancer.
        </div>
    </div>
  </div>
</div>
<?php
    }
?>
<?php if ( is_active_sidebar( 'footerfull' ) ) : ?>

	<!-- ******************* The Footer Full-width Widget Area ******************* -->

	<div class="wrapper-fluid" id="wrapper-footer-full">

		<div class="container-fluid" id="footer-full-content" tabindex="-1">

			<div class="row">

                <div class="col-xl-3 no-padding logo-wrapper">
                    <img class="align-self-center footer-logo" src="/wp-content/uploads/2017/04/logo-alt-footer.png" />
                </div>

                <div class="col-xl-6 no-padding menu-wrapper">
                    <?php dynamic_sidebar( 'footerfull' ); ?>
                </div>

                <div class="col-xl-3 no-padding mark-wrapper">
                    <div class="mark">
                        <?php printf( esc_html__( '@ 2017 %s', 'understrap' ),'Digital Freelancer' );?>
                    </div>
                </div>
				

			</div>

		</div>

	</div><!-- #wrapper-footer-full -->

<?php endif; ?>
