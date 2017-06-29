<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>

<div class="mp_wrapper1">
  <!--<h3><?php _ex('Request a Password Reset', 'ui', 'memberpress'); ?></h3>-->
  <form name="mepr_forgot_password_form" id="mepr_forgot_password_form" action="" method="post" class="pt-5 pb-3">
    <div class="form-group row">

      <!--<label><?php _ex('Enter Your Username or Email Address', 'ui', 'memberpress'); ?></label>-->
        <input class="form-control" type="text" name="mepr_user_or_email" id="mepr_user_or_email" value="<?php echo isset($mepr_user_or_email)?$mepr_user_or_email:''; ?>" />

    </div>
    
    <div class="form-group row">

        <input type="submit" name="wp-submit" id="wp-submit" class="button-primary mepr-share-button btn btn-primary col-md-12 mepr-submit" value="<?php _ex('Request Password Reset', 'ui', 'memberpress'); ?>" />
        <input type="hidden" name="mepr_process_forgot_password_form" value="true" />

    </div>
  </form>
</div>

