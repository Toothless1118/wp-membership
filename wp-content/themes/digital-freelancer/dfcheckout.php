<?php
/**
 * Template Name: DF Checkout Page Template
 *
 * Template for displaying a page just with the header and footer area and a "naked" content area in between.
 * Good for landingpages and other types of pages where you want to add a lot of custom markup.
 *
 * @package understrap
 */

 ?>

<? get_header('checkout'); 
$container   = get_theme_mod( 'understrap_container_type' );
$sidebar_pos = get_theme_mod( 'understrap_sidebar_position' );
?>
<div class="wrapper" id="page-wrapper">
  <div class="df-checkout-container" id="content" tabindex="-1">
    <div class="row">
      <?php the_content(); ?>
      <?php get_sidebar( 'checkout-sidebar' ); ?>
    </div><!-- .row -->
  </div><!-- Container end -->
</div><!-- Wrapper end -->
 
<?php get_footer('checkout'); ?>
