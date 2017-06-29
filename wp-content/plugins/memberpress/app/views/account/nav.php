<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>

<div class="settings-nav">
  <a class="nav-a" href="<?php echo MeprHooks::apply_filters('mepr-account-nav-home-link',$account_url.$delim.'action=home'); ?>">
    <div class="media <?php echo ($_GET['action']=='home' || !isset($_GET['action']))?'active':'';?>">
      <div class="ico-wrapper d-flex align-self-center mr-3">
        <span class="ico-a"><i class="fa fa-user-o"></i></span>
      </div>
      <div class="media-body align-self-center">
        <h5 class="mt-0">My Profile</h5>
        Your main account settings
      </div>
    </div>
  </a>
  <a class="nav-a" href="<?php echo MeprHooks::apply_filters('mepr-account-nav-billing-link',$account_url.$delim.'action=billing'); ?>">
    <div class="media <?php echo ($_GET['action']=='billing')?'active':'';?>">
      <div class="ico-wrapper d-flex align-self-center mr-3">
        <span class="ico-a">
        <i class="fa fa-shopping-cart"></i>
        </span>
      </div>
      <div class="media-body align-self-center">
        <h5 class="mt-0">Billing and Subscriptions</h5>
        Configure your billing info
      </div>
    </div>
  </a>
  <a class="nav-a" href="#">
    <div class="media">
      <div class="ico-wrapper d-flex align-self-center mr-3">
        <span class="ico-a" >
        <i class="fa fa-support"></i>
        </span>
      </div>
      <div class="media-body align-self-center">
        <h5 class="mt-0">Support</h5>
        Do you need help?
      </div>
    </div>
  </a>
  </div>
<!--

<div class="mp_wrapper">
  <div id="mepr-account-nav">
    <span class="mepr-nav-item <?php MeprAccountHelper::active_nav('home'); ?>">
      <a href="<?php echo MeprHooks::apply_filters('mepr-account-nav-home-link',$account_url.$delim.'action=home'); ?>" id="mepr-account-home"><?php _ex('Home', 'ui', 'memberpress'); ?></a>
    </span>
    <span class="mepr-nav-item <?php MeprAccountHelper::active_nav('subscriptions'); ?>">
      <a href="<?php echo MeprHooks::apply_filters('mepr-account-nav-subscriptions-link',$account_url.$delim.'action=subscriptions'); ?>" id="mepr-account-subscriptions"><?php _ex('Subscriptions', 'ui', 'memberpress'); ?></a></span>
    <span class="mepr-nav-item <?php MeprAccountHelper::active_nav('payments'); ?>">
      <a href="<?php echo MeprHooks::apply_filters('mepr_account-nav-payments-link',$account_url.$delim.'action=payments'); ?>" id="mepr-account-payments"><?php _ex('Payments', 'ui', 'memberpress'); ?></a>
    </span>
    <?php MeprHooks::do_action('mepr_account_nav', $mepr_current_user); ?>
    <span class="mepr-nav-item"><a href="<?php echo MeprUtils::logout_url(); ?>" id="mepr-account-logout"><?php _ex('Logout', 'ui', 'memberpress'); ?></a></span>
  </div>
</div>
-->
<?php
if(isset($expired_subs) and !empty($expired_subs)) {
  $account_url = MeprUtils::get_permalink(); // $mepr_options->account_page_url();
  $delim = preg_match('#\?#',$account_url) ? '&' : '?';
  $errors = array(sprintf(_x('You have a problem with one or more of your subscriptions. To prevent any lapses in your subscriptions please visit your %sSubscriptions%s page to update them.', 'ui', 'memberpress'),'<a href="'.$account_url.$delim.'action=subscriptions">','</a>'));
  MeprView::render('/shared/errors', get_defined_vars());
}
