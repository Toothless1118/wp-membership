<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

$c = new MeprCoupon();

return array(
  'coupon_code' => array(
    'name' => __('Coupon Code', 'memberpress-developer-tools'),
    'type' => 'string',
    'default' => '&lt;auto-generated code&gt;',
    'required' => false,
    'desc' => __('This is the coupon code to be associated with the coupon. This should be a unique code with no spaces or special characters other than \'-\'. If spaces are present the value will be accepted but MemberPress will replace them with dashes. Also, if this value is left blank, MemberPress will automatically generate a unique code for this coupon.', 'memberpress-developer-tools')
  ),
  'should_expire' => array(
    'name' => __('Expire Coupon', 'memberpress-developer-tools'),
    'type' => 'bool',
    'default' => 'false',
    'required' => __('Required', 'memberpress-developer-tools'),
    'desc' => __('Set this to true if you want the coupon to expire sometime in the future.', 'memberpress-developer-tools')
  ),
  'expires_on' => array(
    'name' => __('Expire Coupon On Date', 'memberpress-developer-tools'),
    'type' => 'timestamp',
    'default' => 'null',
    'required' => __('Required if \'should_expire\' is true', 'memberpress-developer-tools'),
    'desc' => __('Set this to the unix timestamp of when you\'d like the coupon to expire.', 'memberpress-developer-tools')
  ),
  'usage_amount' => array(
    'name' => __('Limit Coupon Usage', 'memberpress-developer-tools'),
    'type' => 'integer',
    'default' => '0',
    'valid_values' => __('0 or more', 'memberpress-developer-tools'),
    'required' => false,
    'desc' => __('Set this to a number greater than zero if you want to limit the number of times the coupon can be used.', 'memberpress-developer-tools')
  ),
  'discount_type' => array(
    'name' => __('Discount Type', 'memberpress-developer-tools'),
    'type' => 'enum',
    'default' => 'percent',
    'required' => false,
    'valid_values' => "'".implode(__("' or '", 'memberpress-developer-tools'), $c->discount_types)."'",
    'desc' => __('This will alter the way MemberPress interprets the \'discount_amount\' to either be a percentage or fixed amount.', 'memberpress-developer-tools')
  ),
  'discount_amount' => array(
    'name' => __('Discount Amount', 'memberpress-developer-tools'),
    'type' => 'decimal',
    'default' => '0.00',
    'required' => false,
    'valid_values' => __('0.00 or more', 'memberpress-developer-tools'),
    'desc' => __('The amount of the discount. Depending on what \'discount_type\' is set to, this can either be a fixed amount or a percentage.', 'memberpress-developer-tools')
  ),
  'valid_memberships' => array(
    'name' => __('Valid Memberships', 'memberpress-developer-tools'),
    'type' => 'array',
    'default' => '[]',
    'required' => __('Required', 'memberpress-developer-tools'),
    'desc' => __('You can set an array of membership ids here that the coupon will apply to.', 'memberpress-developer-tools')
  ),
  'trial' => array(
    'name' => __('Override Trial Period', 'memberpress-developer-tools'),
    'type' => 'bool',
    'default' => 'false',
    'required' => false,
    'desc' => __('Set this to true if you want to override the trial period of any subscription that this coupon is applied to.', 'memberpress-developer-tools')
  ),
  'trial_days' => array(
    'name' => __('Number of Days in Trial Period', 'memberpress-developer-tools'),
    'type' => 'integer',
    'default' => '0',
    'valid_values' => __('0 or more', 'memberpress-developer-tools'),
    'required' => false,
    'desc' => __('You can set an array of product ids here that the coupon will apply to.', 'memberpress-developer-tools')
  ),
  'trial_amount' => array(
    'name' => __('Amount Charged for Trial', 'memberpress-developer-tools'),
    'type' => 'decimal',
    'default' => '0.00',
    'valid_values' => __('0.00 or more', 'memberpress-developer-tools'),
    'required' => false,
    'desc' => __('You can set an array of product ids here that the coupon will apply to.', 'memberpress-developer-tools')
  ),
);

