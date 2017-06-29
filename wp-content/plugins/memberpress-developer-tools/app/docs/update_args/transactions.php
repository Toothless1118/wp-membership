<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

return array(
  'trans_num' => array(
    'name' => __('Transaction Number', 'memberpress-developer-tools'),
    'type' => 'string',
    'default' => 'mp-txn-xxx...',
    'required' => false,
    'desc' => __('The unique Transaction Number.', 'memberpress-developer-tools')
  ),
  'amount' => array(
    'name' => __('Transaction Sub-Total', 'memberpress-developer-tools'),
    'type' => 'decimal',
    'default' => '0.00',
    'required' => false,
    'desc' => __('The base price for the Transaction (not including tax)', 'memberpress-developer-tools') //BLAIR TODO - Is this correct?
  ),
  'total' => array(
    'name' => __('Transaction Total', 'memberpress-developer-tools'),
    'type' => 'decimal',
    'default' => '0.00',
    'required' => false,
    'desc' => __('The total price for this Transaction (including tax)', 'memberpress-developer-tools') //BLAIR TODO - Is this correct?
  ),
  'member' => array(
    'name' => __('Member ID', 'memberpress-developer-tools'),
    'type' => 'integer',
    'default' => '0',
    'required' => __('Required', 'memberpress-developer-tools'),
    'desc' => __('The Member\'s WordPress User ID.', 'memberpress-developer-tools')
  ),
  'membership' => array(
    'name' => __('Membership ID', 'memberpress-developer-tools'),
    'type' => 'integer',
    'default' => '0',
    'required' => __('Required', 'memberpress-developer-tools'),
    'desc' => __('The Membership ID associated with this Transaction.', 'memberpress-developer-tools')
  ),
  'coupon' => array(
    'name' => __('Coupon ID', 'memberpress-developer-tools'),
    'type' => 'integer',
    'default' => '0',
    'required' => __('Required', 'memberpress-developer-tools'),
    'desc' => __('The Coupon ID associated with this Transaction.', 'memberpress-developer-tools')
  ),
  'status' => array(
    'name' => __('Transaction Status', 'memberpress-developer-tools'),
    'type' => 'string',
    'default' => 'pending',
    'required' => __('Required', 'memberpress-developer-tools'),
    'valid_values' => __('pending, complete, failed, or refunded', 'memberpress-developer-tools'),
    'desc' => __('The status of this Transaction.', 'memberpress-developer-tools')
  ),
  'response' => array(
    'name' => __('Transaction Response', 'memberpress-developer-tools'),
    'type' => 'string',
    'default' => '',
    'required' => false,
    'desc' => __('A place where you can store Payment Gateway POST or GET responses for later reference.', 'memberpress-developer-tools')
  ),
  'gateway' => array(
    'name' => __('Gateway ID', 'memberpress-developer-tools'),
    'type' => 'string',
    'default' => 'manual',
    'required' => false,
    'valid_values' => __('manual, free, or the ID of any live Gateway setup in your MemberPress Options', 'memberpress-developer-tools'),
    'desc' => __('The Payment Gateway to use for this Transaction.', 'memberpress-developer-tools')
  ),
  'subscription' => array(
    'name' => __('Subscription ID', 'memberpress-developer-tools'),
    'type' => 'integer',
    'default' => '0',
    'required' => __('Required', 'memberpress-developer-tools'),
    'desc' => __('The ID of the Recurring Subscription CPT associated with this Transaction.', 'memberpress-developer-tools')
  ),
  'created_at' => array( //BLAIR I ADDED THIS ONE
    'name' => __('Created At Date', 'memberpress-developer-tools'),
    'type' => 'string',
    'default' => 'null',
    'required' => __('Required', 'memberpress-developer-tools'),
    'desc' => __('The date the Transaction was created. This should be in a MySQL datetime format. All dates stored in the database should be in UTC timezone.', 'memberpress-developer-tools')
  ),
  'expires_at' => array( //BLAIR I ADDED THIS ONE
    'name' => __('Expires At Date', 'memberpress-developer-tools'),
    'type' => 'string',
    'default' => '0000-00-00 00:00:00',
    'required' => false,
    'desc' => __('The date the Transaction will expire on. This should be in a MySQL datetime format. All dates stored in the database should be in UTC timezone. Note: Leave at default to create a Transaction that last\'s a lifetime (aka never expires).', 'memberpress-developer-tools')
  ),
);

