<?php

add_filter( 'if_menu_conditions', 'if_menu_basic_conditions' );

function if_menu_basic_conditions( $conditions ) {
  global $wp_roles;

  foreach ($wp_roles->role_names as $roleId => $role) {
    $conditions[] = array(
      'name'      =>  sprintf('User is %s', $role),
      'condition' =>  function() use($roleId) {
        global $current_user;
        if( is_user_logged_in() ) return in_array( $roleId, $current_user->roles );
        return false;
      },
      'group'     =>  'User state & roles'
    );
  }


	$conditions[] = array(
		'name'		=>	__( 'User is logged in', 'if-menu' ),
		'condition'	=>	'is_user_logged_in',
    'group'     =>  'User state & roles'
	);

	$conditions[] = array(
		'name'		=>	__( 'Front Page', 'if-menu' ),
		'condition'	=>	'is_front_page',
    'group'     =>  'Page type'
	);

	$conditions[] = array(
		'name'		=>	__( 'Single Post', 'if-menu' ),
		'condition'	=>	'is_single',
    'group'     =>  'Page type'
	);

  $conditions[] = array(
    'name'    =>  __( 'Page', 'if-menu' ),
    'condition' =>  'is_page',
    'group'     =>  'Page type'
  );

	$conditions[] = array(
		'name'		=>	__( 'Mobile', 'if-menu' ),
		'condition'	=>	'wp_is_mobile',
    'group'     =>  'Device'
	);

	if (defined('WP_ALLOW_MULTISITE') && WP_ALLOW_MULTISITE === true) {
		$conditions[] = array(
			'name'			=>	__( 'User is logged in for current site', 'if-menu' ),
			'condition'	=>	'if_menu_basic_condition_read_cap',
      'group'     =>  'User state & roles'
		);
	}

	return $conditions;
}

function if_menu_basic_condition_read_cap() {
	return current_user_can('read');
}
