<?php
/**
 * The Freelance Chat Page template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package understrap
 */

$the_theme = wp_get_theme();
$container = get_theme_mod( 'understrap_container_type' );
?>

    <div class="df-wrapper chat-footer">
      <div class="chat-footer-logo">freelance</div>
      <div class="chat-footer-links">
        <ul>
          <li><a href="">TERMS</a></li>
          <li><a href="">CONTACT</a></li>
        </ul>
      </div>
    </div>

</div><!-- #page -->

<?php wp_footer(); ?>

</body>

</html>
