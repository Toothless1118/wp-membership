<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>

<?php if( !empty($mepr_current_user->user_message) ): ?>
  <div id="mepr-account-user-message">
    <?php echo MeprHooks::apply_filters('mepr-user-message', wpautop(do_shortcode($mepr_current_user->user_message)), $mepr_current_user); ?>
  </div>
<?php endif; ?>

<div class="settings-input billing-form pt-5 col-sm-8 offset-sm-2">
  <form class="mepr-account-form mepr-form" id="mepr_account_form" action="" method="post" novalidate>
      <input type="hidden" name="mepr-process-account" value="Y" />
      <input type="hidden" id="user_email" name="user_email" class="mepr-form-input" value="<?php echo $mepr_current_user->user_email; ?>"  />
      <input type="hidden" name="user_first_name" id="user_first_name" class="form-control mepr-form-input" value="<?php echo $mepr_current_user->first_name; ?>"  />
      <input type="hidden" name="user_last_name" id="user_last_name" class="input" placeholder="Last Name" value="<?php echo $last_name; ?>"  />
    <?php
      MeprUsersHelper::render_custom_fields();
      MeprHooks::do_action('mepr-account-home-fields', $mepr_current_user);
    ?>
    <br>
  </form>
  <?php MeprHooks::do_action('mepr_account_home', $mepr_current_user); ?>

</div>
<div class="settings-description col-sm-8 offset-sm-2">
  <p class="txt-1">Subscriptions</p>
  <p class="txt-2">
    <ul>
      <li>Pipeline Professional - $197/mo</li>
    </ul>
  </p>
  <p class="txt-3">To cancel a subscription please email subscriptions@digitalfreelancer.io</p>
</div>

