<?php
/**
 * Sidebar setup for Top Products.
 *
 * @package understrap
 */

$container   = get_theme_mod( 'understrap_container_type' );

?>

<?php if ( is_active_sidebar( 'topproducts' ) ) : ?>

    <!-- ******************* The Top Products Widget Area ******************* -->

    <?php dynamic_sidebar( 'topproducts' ); ?>

<?php endif; ?>
