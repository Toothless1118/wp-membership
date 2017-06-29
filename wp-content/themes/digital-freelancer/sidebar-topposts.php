<?php
/**
 * Sidebar setup for Top Posts.
 *
 * @package understrap
 */

$container   = get_theme_mod( 'understrap_container_type' );

?>

<?php if ( is_active_sidebar( 'topposts' ) ) : ?>

    <!-- ******************* The Topposts Widget Area ******************* -->

    <?php dynamic_sidebar( 'topposts' ); ?>

<?php endif; ?>
