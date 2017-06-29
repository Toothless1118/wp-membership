<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>

<?php if( mpdt_rest_api_available() ): ?>
  <?php $api = MpdtCtrlFactory::fetch('api'); ?>
  <?php $routes = $api->routes(); ?>

  <div class="mepr-page-title"><?php _e('REST API', 'memberpress-developer-tools'); ?></div>
  <p><?php _e('View dynamic API route documentation and examples.', 'memberpress-developer-tools'); ?></p>

  <h3><?php _e('Select an API Route:', 'memberpress-developer-tools'); ?></h3>

  <div class="mpdt_select_wrap mpdt_routes_dropdown_wrap">
    <select id="mpdt_routes_dropdown" class="mpdt_select">
      <option value="-1">-- <?php _e('Select a Route', 'memberpress-developer-tools'); ?> --</option>
      <?php foreach($routes as $slug => $route): ?>
        <option value="<?php echo $slug; ?>"><?php echo $route->name; ?></option>
      <?php endforeach; ?>
    </select>
    <span class="mpdt_rolling">
      <?php echo file_get_contents(MPDT_IMAGES_PATH . '/rolling.svg'); ?>
    </span>
  </div>

  <div>&nbsp;</div>

  <div id="mpdt_route_display" class="mepr-sub-box" style="display: none;">
    <div class="mepr-arrow mepr-gray mepr-up mepr-sub-box-arrow"> </div>
    <div id="mpdt_route">
    </div>
  </div>
<?php else: ?>
  <h2><?php _e('You\'re not running WordPress 4.7 and the WordPress REST API plugin isn\'t active', 'memberpress-developer-tools'); ?></h2>
  <p><?php _e('The MemberPress REST API requires at least WordPress 4.7 or relies on WordPress\'s REST API plugin **Version 2** being installed and activated on this site.', 'memberpress-developer-tools'); ?></p>
  <p><?php printf('You can get the plugin from %1$shere%2$s.', '<a href="https://wordpress.org/plugins/rest-api/">', '</a>'); ?></p>
<?php endif; ?>

