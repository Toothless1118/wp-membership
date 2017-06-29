<?php
/**
 * Sidebar setup for Checkout.
 *
 * @package understrap
 */

$container   = get_theme_mod( 'understrap_container_type' );

?>

<?php if ( is_active_sidebar( 'checkout' ) ) : ?>

    <!-- ******************* The Checkout Sidebar Widget Area ******************* -->

    <?php dynamic_sidebar( 'checkout' ); ?>

<?php endif; ?>
