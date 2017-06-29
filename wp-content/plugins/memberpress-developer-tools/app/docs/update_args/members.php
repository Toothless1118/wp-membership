<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

return array(
  'first_name' => array(
    'name' => __('First Name', 'memberpress-developer-tools'),
    'type' => 'string',
    'default' => '',
    'required' => false,
    'desc' => __('The User\'s First Name.', 'memberpress-developer-tools')
  ),
  'last_name' => array(
    'name' => __('Last Name', 'memberpress-developer-tools'),
    'type' => 'string',
    'default' => '',
    'required' => false,
    'desc' => __('The User\'s Last Name.', 'memberpress-developer-tools')
  ),
  'email' => array(
    'name' => __('Email Address', 'memberpress-developer-tools'),
    'type' => 'string',
    'default' => '',
    'required' => __('Required', 'memberpress-developer-tools'),
    'desc' => __('The User\'s Email Address.', 'memberpress-developer-tools')
  ),
  'username' => array(
    'name' => __('Username', 'memberpress-developer-tools'),
    'type' => 'string',
    'default' => '',
    'required' => __('Required', 'memberpress-developer-tools'),
    'desc' => __('The username for this User. If you\'re using email addresses as the Username, then both username and email should be set to the same string.', 'memberpress-developer-tools')
  ),
  'password' => array(
    'name' => __('Plaintext Password', 'memberpress-developer-tools'),
    'type' => 'string',
    'default' => '',
    'required' => __('Required', 'memberpress-developer-tools'),
    'desc' => __('A plaintext password which will be hashed and stored with this user.', 'memberpress-developer-tools')
  ),
);

