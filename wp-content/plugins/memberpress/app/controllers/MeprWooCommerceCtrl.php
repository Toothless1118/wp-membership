<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
/*
Integration of WooCommerce into MemberPress
*/
class MeprWooCommerceCtrl extends MeprBaseCtrl {
  public function load_hooks() {
    add_filter('woocommerce_is_purchasable', 'MeprWooCommerceCtrl::override_is_purchasable', 11, 2);
    add_filter('woocommerce_product_is_visible', 'MeprWooCommerceCtrl::override_is_visible', 11, 2);
    add_filter('mepr-pre-run-rule-content', 'MeprWooCommerceCtrl::dont_hide_woocommerce_product_content', 11, 3);
  }

  public static function override_is_purchasable($is, $prd) {
    if(!$is) { return $is; } //if it's already not purchasable, no need to go further

    $post = get_post($prd->id);

    return !MeprRule::is_locked($post);
  }

  public static function override_is_visible($is, $prd_id) {
    if(!$is) { return $is; } //if it's already not visible, no need to go further

    $post = get_post($prd_id);

    return !MeprRule::is_locked($post);
  }

  //Never hide WooCommerce the_content
  public static function dont_hide_woocommerce_product_content($protect, $post, $uri) {
    if(isset($post) && isset($post->post_type) && $post->post_type == 'product') { return false; }

    return $protect;
  }
} //End class
