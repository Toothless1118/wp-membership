<?php
/**
 * The checkout header for our theme.
 *
 * Displays checkout page of the <head> section
 *
 * @package understrap
 */

$container = get_theme_mod( 'understrap_container_type' );
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="<?php bloginfo( 'name' ); ?> - <?php bloginfo( 'description' ); ?>">
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<!-- <img style="position: absolute; opacity: .5; z-index:100;" src="/wp-content/themes/digital-freelancer/dist/images/z11.png" alt="Generic placeholder image"> -->
<div class="hfeed site" id="page" style="opacity:1;z-index: 2;">

    <!-- ******************* The Navbar Area ******************* -->
    <div class="wrapper wrapper-navbar">

        <div class="df-checkout-container df-checkout-header" id="checkout-header-full-content" tabindex="-1">

            <div class="row">

                <div class="col-md-9 col-sm-12  no-padding logo-wrapper">
                    <img class="align-self-center footer-logo" src="/wp-content/uploads/2017/04/logo-alt-footer.png" />
                </div>
                <div class="col-md-3 col-sm-12 no-padding tel-wrapper">
                    <div class="tel">
                        <?php printf( esc_html__( '%s', 'understrap' ),'<i class="fa fa-phone"></i>+1(111)111-1111' );?>
                    </div>
                </div>
            </div>
        </div>

    </div><!-- .wrapper-navbar end -->
