<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

class MeprGroupsCtrl extends MeprCptCtrl {
  public function load_hooks() {
    add_action('admin_enqueue_scripts', 'MeprGroupsCtrl::enqueue_scripts');
    add_filter('the_content', 'MeprGroupsCtrl::render_pricing_boxes', 10);
    add_action('manage_pages_custom_column', 'MeprGroupsCtrl::custom_columns', 10, 2);
    add_filter('manage_edit-memberpressgroup_columns', 'MeprGroupsCtrl::columns');
    add_filter('template_include', 'MeprGroupsCtrl::template_include');
    add_action('save_post', 'MeprGroupsCtrl::save_postdata');
    add_action('init', 'MeprGroup::cleanup_db');
    add_action('wp_ajax_mepr_is_product_already_in_group', 'MeprGroupsCtrl::is_product_already_in_group');
    add_shortcode('mepr-group-price-boxes', 'MeprGroupsCtrl::shortcode_group_price_boxes');

    // Cleanup list view
    add_filter('views_edit-'.MeprGroup::$cpt, 'MeprAppCtrl::cleanup_list_view' );
  }

  public function register_post_type() {
    $mepr_options = MeprOptions::fetch();
    $this->cpt = (object)array(
      'slug' => MeprGroup::$cpt,
      'config' => array(
        'labels' => array(
          'name' => __('Groups', 'memberpress'),
          'singular_name' => __('Group', 'memberpress'),
          'add_new_item' => __('Add New Group', 'memberpress'),
          'edit_item' => __('Edit Group', 'memberpress'),
          'new_item' => __('New Group', 'memberpress'),
          'view_item' => __('View Group', 'memberpress'),
          'search_items' => __('Search Group', 'memberpress'),
          'not_found' => __('No Group found', 'memberpress'),
          'not_found_in_trash' => __('No Group found in Trash', 'memberpress'),
          'parent_item_colon' => __('Parent Group:', 'memberpress')
        ),
        'public' => true,
        'show_ui' => true, //MeprUpdateCtrl::is_activated(),
        'show_in_menu' => 'memberpress',
        'capability_type' => 'page',
        'hierarchical' => true,
        'register_meta_box_cb' => 'MeprGroupsCtrl::add_meta_boxes',
        'rewrite' => array("slug" => $mepr_options->group_pages_slug, "with_front" => false),
        'supports' => array('title', 'editor', 'page-attributes', 'comments', 'thumbnail')
      )
    );
    register_post_type( $this->cpt->slug, $this->cpt->config );
  }

  public static function render_pricing_boxes($content, $manual = false) {
    $current_post = MeprUtils::get_current_post();

    //This isn't a post? Just return the content then
    if($current_post === false) { return $content; }

    //WARNING the_content CAN be run more than once per page load
    //so this static var prevents stuff from happening twice
    //like cancelling a subscr or resuming etc...
    static $already_run = array();
    static $new_content = array();
    static $content_length = array();
    //Init this posts static values
    if(!isset($new_content[$current_post->ID]) || empty($new_content[$current_post->ID])) {
      $already_run[$current_post->ID] = false;
      $new_content[$current_post->ID] = '';
      $content_length[$current_post->ID] = -1;
    }

    if($already_run[$current_post->ID] && strlen($content) == $content_length[$current_post->ID] && !$manual) //shortcode may pass
      return $new_content[$current_post->ID];

    $content_length[$current_post->ID] = strlen($content);
    $already_run[$current_post->ID] = true;

    if(isset($current_post) && is_a($current_post, 'WP_Post') && $current_post->post_type == MeprGroup::$cpt) {
      $group = new MeprGroup($current_post->ID);

      //Short circuiting for any of the following reasons
      if( $group->ID === null || //Bad group for some reason
          (!$manual && $group->manual_append_price_boxes()) || //the_content filter and show manually is enabled
          ($manual && !$group->manual_append_price_boxes()) ) { //do_shortcode and show manually is disabled
        //See notes above
        $new_content[$current_post->ID] = $content;
        return $new_content[$current_post->ID];
      }

      ob_start();
      self::display_pricing_boxes($group);
      $content .= ob_get_clean();
    }

    //See notes above
    $new_content[$current_post->ID] = $content;
    return $new_content[$current_post->ID];
  }

  public static function display_pricing_boxes($group, $theme=null) {
    MeprView::render('/groups/front_groups_page', get_defined_vars());
  }

  public static function columns($columns) {
    $columns = array(
      "cb" => "<input type=\"checkbox\" />",
      "ID" => __("ID", 'memberpress'),
      "title" => __("Group Title", "memberpress"),
      "url" => __("URL", "memberpress"),
      "group-products" => __("Memberships in Group", "memberpress")
    );
    return $columns;
  }

  public static function custom_columns($column, $post_id) {
    $group = new MeprGroup($post_id);

    if($group->ID !== null) {
      if("ID" == $column) {
        echo $group->ID;
      }
      elseif("group-products" == $column) {
        echo implode(', ', $group->products('titles'));
      }
      elseif("url" == $column) {
        echo $group->url();
      }
    }
  }

  // Template selection
  public static function template_include($template) {
    global $post, $wp_query;

    if(!is_singular()) { return $template; }

    if(isset($post) && is_a($post, 'WP_Post') && $post->post_type == MeprGroup::$cpt) {
      $group = new MeprGroup($post->ID);

      if(!$group->pricing_page_disabled) {
        $new_template = $group->get_page_template();
      }
      elseif($group->pricing_page_disabled && !empty($group->alternate_group_url)) {
        MeprUtils::wp_redirect($group->alternate_group_url);
      }
      else {
        $wp_query->is_404 = true;
        $new_template = locate_template(array('404.php'));
      }
    }

    if(isset($new_template) && !empty($new_template)) { return $new_template; }

    return $template;
  }

  public static function add_meta_boxes() {
    global $post_id;
    $group = new MeprGroup($post_id);

    add_meta_box("memberpress-group-meta", __("Group Options", 'memberpress'), "MeprGroupsCtrl::group_meta_box", MeprGroup::$cpt, "normal", "high", array('group' => $group));
    add_meta_box("memberpress-custom-template", __('Custom Page Template', 'memberpress'), "MeprGroupsCtrl::custom_page_template", MeprGroup::$cpt, "side", "default", array('group' => $group));
  }

  public static function save_postdata($post_id) {
    $post = get_post($post_id);

    if(!wp_verify_nonce((isset($_POST[MeprGroup::$nonce_str]))?$_POST[MeprGroup::$nonce_str]:'', MeprGroup::$nonce_str.wp_salt())) {
      return $post_id; //Nonce prevents meta data from being wiped on move to trash
    }

    if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
      return $post_id;
    }

    if(defined('DOING_AJAX')) {
      return;
    }

    if(!empty($post) && $post->post_type == MeprGroup::$cpt) {
      $group = new MeprGroup($post_id);
      $group->pricing_page_disabled = isset($_POST[MeprGroup::$pricing_page_disabled_str]);
      $group->disable_change_plan_popup = isset($_POST[MeprGroup::$disable_change_plan_popup_str]);
      $group->is_upgrade_path = isset($_POST[MeprGroup::$is_upgrade_path_str]);
      $group->upgrade_path_reset_period = isset($_POST[MeprGroup::$upgrade_path_reset_period_str]);
      //$group->group_page_style_options = self::get_style_options_array();
      $group->group_theme = $_POST[MeprGroup::$group_theme_str];
      $group->page_button_class = $_POST[MeprGroup::$page_button_class_str];
      $group->page_button_highlighted_class = $_POST[MeprGroup::$page_button_highlighted_class_str];
      $group->page_button_disabled_class = $_POST[MeprGroup::$page_button_disabled_class_str];
      $group->alternate_group_url = stripslashes($_POST[MeprGroup::$alternate_group_url_str]);
      self::store_chosen_products($group->ID);
      $group->use_custom_template = isset($_POST['_mepr_use_custom_template']);
      $group->custom_template = isset($_POST['_mepr_custom_template'])?$_POST['_mepr_custom_template']:'';

      $group->store_meta();

      //Some themes rely on this meta key to be set to use the custom template, and they don't use locate_template
      if($group->use_custom_template && !empty($group->custom_template)) {
        update_post_meta($group->ID, '_wp_page_template', $group->custom_template);
      }
      else {
        update_post_meta($group->ID, '_wp_page_template', '');
      }
    }
  }

  // public static function get_style_options_array() {
    // $styles = array();

    // $styles['layout']       = $_POST[MeprGroup::$group_page_layout_str];
    // $styles['style']        = $_POST[MeprGroup::$group_page_style_str];
    // $styles['button_size']  = $_POST[MeprGroup::$group_page_button_size_str];
    // $styles['bullet_style'] = $_POST[MeprGroup::$group_page_bullet_style_str];
    // $styles['font_style']   = $_POST[MeprGroup::$group_page_font_style_str];
    // $styles['font_size']    = $_POST[MeprGroup::$group_page_font_size_str];
    // $styles['button_color'] = $_POST[MeprGroup::$group_page_button_color_str];

    // return $styles;
  // }

  public static function store_chosen_products($group_id) {
    if(isset($_POST[MeprGroup::$products_str]['product'])) {
      //Zero out old memberships before assigning the new ones
      self::zero_out_old_products($group_id);

      for($index=0; $index < (count($_POST[MeprGroup::$products_str]['product']) - 1); $index++) {
        $product_id = $_POST[MeprGroup::$products_str]['product'][$index];
        $prd = new MeprProduct($product_id);

        if($prd->ID) {
          $prd->group_id = $group_id;
          $prd->group_order = $index;
          $prd->store_meta();
        }
      }
    }
  }

  //Deletes all memberships from the given group. We purge memberships before saving the new one's.
  public static function zero_out_old_products($group_id) {
    $group = new MeprGroup($group_id);
    $products = $group->products();

    if(!empty($products)) {
      foreach($products as $product) {
        $product->group_id = 0;
        $product->store_meta();
      }
    }
  }

  public static function group_meta_box($post, $args) { //Don't use $post here, it is null on new group - use args instead
    $group = $args['args']['group'];

    MeprView::render('/admin/groups/form', get_defined_vars());
  }

  public static function custom_page_template($post, $args) {
    $group = $args['args']['group'];

    MeprView::render('/admin/groups/custom_page_template_form', get_defined_vars());
  }

  public static function enqueue_scripts($hook) {
    global $current_screen;

    if($current_screen->post_type == MeprGroup::$cpt) {
      wp_register_style('mepr-settings-table', MEPR_CSS_URL.'/settings_table.css', array(), MEPR_VERSION);
      wp_enqueue_style('mepr-groups-css', MEPR_CSS_URL.'/admin-groups.css', array('mepr-settings-table'), MEPR_VERSION);

      wp_dequeue_script('autosave'); //Disable auto-saving

      wp_register_script('mepr-settings-table', MEPR_JS_URL.'/settings_table.js', array(), MEPR_VERSION);
      wp_enqueue_script('mepr-groups-js', MEPR_JS_URL.'/admin_groups.js', array('jquery','jquery-ui-sortable','mepr-settings-table'), MEPR_VERSION);
    }
  }

  public static function is_product_already_in_group() {
    if(!isset($_POST['product_id'])) {
      _e('Unknown error has occured.', 'memberpress');
      die();
    }

    $groups = get_posts(array('numberposts' => -1, 'post_type' => MeprGroup::$cpt, 'post_status' => 'publish'));

    if(empty($groups)) { die(); }

    foreach($groups as $g) {
      $group = new MeprGroup($g->ID);
      $products = $group->products();

      if(empty($products)) { continue; }

      foreach($products as $p) {
        if($p->ID == $_POST['product_id']) {
          _e('This membership already belongs to another group. If you assign it to this group, it will be removed from the other.', 'memberpress');
          die();
        }
      }
    }

    die(); //No matches so let's kill this thing
  }

  public static function shortcode_group_price_boxes($atts, $content = '') {
    if(isset($atts['group_id']) and $group = new MeprGroup($atts['group_id'])) {
      $theme = (isset($atts['theme']) ? $atts['theme'] : null);

      ob_start();

      self::display_pricing_boxes($group,$theme);

      return ob_get_clean();
    }
    else {
      //No validation needed here as the below function does it all
      //This is just a wrapper
      return self::render_pricing_boxes('', true);
    }
  }
} //End class
