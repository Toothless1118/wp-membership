<?php
/**
 * "Big Image" Layout Template File
 * 
 * DO NOT MODIFY THIS FILE!
 * 
 * To override, copy the /fpw2_views/ folder to your active theme's folder.
 * Modify the file in the theme's folder and the plugin will use it.
 * See: http://wordpress.org/extend/plugins/feature-a-page-widget/faq/
 * 
 * Note: Feature a Page Widget provides a variety of filters and options that may alter the output of the_title, the_excerpt, and the_post_thumbnail in this template.
 */
global $post;
$excerpt = shortcode_unautop(wpautop(convert_chars(convert_smilies(wptexturize($post->post_excerpt)))));    
?>
<a href="<?php the_permalink(); ?>">
  <div class="card">
    <!--<img class="card-img-top" src="/wp-content/themes/digital-freelancer/dist/images/background.jpeg" alt="Card image cap">-->
    <?php the_post_thumbnail( 'fpw_big' ); ?>
    <div class="card-block">
      <span class="fpw-featured-link"><h4 class="card-title"><?php the_title(); ?></h4></span>
      <p class="card-text"><?php echo $excerpt; ?></p>
    </div>
  </div>
</a>
