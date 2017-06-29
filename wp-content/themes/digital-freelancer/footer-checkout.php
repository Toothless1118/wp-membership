<?php
/**
 * The template for displaying the checkout footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package understrap
 */

$the_theme = wp_get_theme();
$container = get_theme_mod( 'understrap_container_type' );
?>

    <div class="wrapper">
        <div class="df-checkout-container" tabindex="-1">
            <div class="row df-checkout-footer">              
                DigitalFreelancer.com 2017 | Privacy Policy | Terms of Service | Customer Support
            </div>
        </div>
    </div>

</div><!-- #page -->

<?php wp_footer(); ?>

</body>

</html>
