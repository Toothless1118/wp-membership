<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>
<?php if( !empty($mepr_current_user->user_message) ): ?>
  <div id="mepr-account-user-message">
    <?php echo MeprHooks::apply_filters('mepr-user-message', wpautop(do_shortcode($mepr_current_user->user_message)), $mepr_current_user); ?>
  </div>
<?php endif; ?>

<?php MeprView::render('/shared/errors', get_defined_vars()); ?>
<div class="settings-input pt-5 pb-5 col-sm-8 offset-sm-2">
  <form class="mepr-account-form mepr-form" id="mepr_account_form" action="" method="post" novalidate>

    <input type="hidden" name="mepr-process-account" value="Y" />
    <?php MeprHooks::do_action('mepr-account-home-before-name', $mepr_current_user); ?>
    

    <label for="basic-url">Your Name</label>
    <div class="input-group setting-btn">
      <!--<input type="text" class="form-control" id="basic-url" aria-describedby="basic-addon3" placeholder="Connor Black">-->
      
      <input type="text" name="user_first_name" id="user_first_name" class="form-control mepr-form-input" value="<?php echo $mepr_current_user->first_name; ?>"  />
      <input type="hidden" name="user_last_name" id="user_last_name" class="form-control mepr-form-input" value="<?php echo $mepr_current_user->last_name; ?>"  />
      
      <input type="submit" class="btn dfbtn-green-setting" name="mepr-account-form" value="<?php _ex('Update', 'ui', 'memberpress'); ?>" class="mepr-submit mepr-share-button" />
    </div>
    <label class="" for="basic-url">Email Address</label>
    <div class="input-group setting-btn">
      <!--<input type="text" class="form-control" id="basic-url" aria-describedby="basic-addon3" placeholder="connor@digitalfreelancer.io">-->
      <input type="email" id="user_email" name="user_email" class="form-control mepr-form-input" value="<?php echo $mepr_current_user->user_email; ?>" required />
      <input type="submit" class="btn dfbtn-green-setting" name="mepr-account-form" value="<?php _ex('Update', 'ui', 'memberpress'); ?>" class="mepr-submit mepr-share-button" />
    </div>
    <?php
      //MeprUsersHelper::render_custom_fields();
      MeprHooks::do_action('mepr-account-home-fields', $mepr_current_user);
    ?>
    <br>
  </form>
    <?php MeprHooks::do_action('mepr_account_home', $mepr_current_user); ?>

  <form action="" class="mepr-newpassword-form mepr-form" method="post" id="mepr_password_form" novalidate>
    <input type="hidden" name="plugin" value="mepr" />
    <input type="hidden" name="action" value="updatepassword" />

    <label class="" for="basic-url">Password</label>
    <div class="input-group setting-btn">
      <!--<input type="password" class="form-control" id="basic-url" aria-describedby="basic-addon3" placeholder="*******">-->
      <input type="password" id="mepr-new-password" name="mepr-new-password" class="form-control mepr-form-input mepr-new-password" required />
      <input type="hidden" id="mepr-confirm-password" name="mepr-confirm-password" class="mepr-form-input mepr-new-password-confirm" required />
      <?php MeprHooks::do_action('mepr-account-after-password-fields', $mepr_current_user); ?>
      <input type="submit" id="new-password-submit" name="new-password-submit" value="<?php _ex('Update', 'ui', 'memberpress'); ?>" class="btn dfbtn-green-setting mepr-submit" />
    </div>
  </form>
  <?php MeprHooks::do_action('mepr_account_password', $mepr_current_user); ?>
</div>
<script>
  jQuery(document).ready(function ($) {
    $(".billing-label").css("display","none");
    $(".billing-label-text").css("display","none");
    $(".billing-label-payment").css("display","none");

    $("#new-password-submit").on("click", function(){
      $("#mepr-confirm-password").val($("#mepr-new-password").val());
    });
    
    function hideMessage(){

      var len = $(".mepr_updated").length;
      if(len > 0)
        $(".mepr_updated").css("display","none");
    }
    setTimeout(hideMessage, 2000);
  });
</script>
