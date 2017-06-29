<?php
class OptimizePress_Sections_Social_Integration {
	function sections(){
		static $sections;
		if(!isset($sections)){
			$sections = array(
				'facebook_app_id' => array(
					'title' => __('Facebook App', 'optimizepress'),
					'action' => array($this,'facebook_app_id'), 
					'save_action' => array($this,'save_facebook_app_id')
				),
                'twitter_app' => array(
                    'title' => __('Twitter App', 'optimizepress'),
                    'action' => array($this,'twitter_app'),
                    'save_action' => array($this,'save_twitter_app')
                )
			);
			$sections = apply_filters('op_edit_sections_social_integration',$sections);
		}
		return $sections;
	}
	
	/* Facebook App ID Section */
	function facebook_app_id(){
		echo op_load_section('facebook_app_id', array(), 'social_integration');
	}
	
	function save_facebook_app_id($op){
		//if ($facebook_app_id = op_get_var($op, 'facebook_app_id')){
		$comments = op_default_option('comments');
		if (!is_array($comments)) {
			$comments = array();
		}
		$comments['facebook']['id'] = $op['facebook_app_id'];
        $comments['facebook']['secret'] = $op['facebook_app_secret'];

        op_update_option('comments', $comments);
		//}
	}

    /* Facebook App ID Section */
    function twitter_app(){
        echo op_load_section('twitter_app', array(), 'social_integration');
    }

    function save_twitter_app($op){
        $comments = op_default_option('comments');
        if (!is_array($comments)) {
            $comments = array();
        }
        $comments['twitter']['consumer_key'] = $op['twitter_consumer_key'];
        $comments['twitter']['consumer_secret'] = $op['twitter_consumer_secret'];
        $comments['twitter']['oauth_access_token'] = $op['twitter_oauth_access_token'];
        $comments['twitter']['oauth_access_token_secret'] = $op['twitter_oauth_access_token_secret'];

        op_update_option('comments', $comments);
    }
	
	
	
	/* Favicon Section */
	function favicon(){
		echo op_load_section('favicon');
	}
	
	function save_favicon($op){
		op_update_option('favicon',op_get_var($op,'favicon'));
	}
	
	
	
	
	/* Default colour scheme functionality can be overridden in the 'op_wizard_step2_sections' filter */
	function color_scheme(){
		echo op_load_section('color_scheme',array('color_fields'=>$this->color_fields()));
	}
	
	function save_color_scheme($op){
		if(isset($op['color_scheme'])){
			op_update_option('color_scheme',$op['color_scheme']);
		}
		$current = op_default_option('color_scheme_fields');
		$current = is_array($current) ? $current : array();
		$fields = isset($op['color_scheme_fields']) ? $op['color_scheme_fields'] : array();
		$color_fields = $this->color_fields();
		if(count($color_fields) > 0){
			foreach($color_fields as $name => $title){
				if(isset($fields[$name])){
					$current[$name] = $fields[$name];
				}
			}
		}
		op_update_option('color_scheme_fields',$current);
	}
	
	function color_fields(){
		$color_fields = array(
			'footer_bg' => __('Footer background colour', 'optimizepress'),
			'topbar_bg' => __('Topbar BG Colour', 'optimizepress'),
			'link_color' => __('Link Colour', 'optimizepress'),
		);
		return apply_filters('op_color_scheme_fields',$color_fields);
	}
	
	
	
	/* Default nav colour scheme functionality can be overridden in the 'op_wizard_step2_sections' filter */
	function nav_color_scheme(){
		echo op_load_section('nav_color_scheme');
	}
	
	function save_nav_color_scheme($op){
		if(isset($op['nav_color_scheme'])){
			op_update_option('nav_color_scheme',$op['nav_color_scheme']);
		}
	}
	
	/* Default nav colour scheme functionality can be overridden in the 'op_wizard_step2_sections' filter */
	function copyright_notice(){
		echo op_load_section('copyright_notice');
	}
	
	function save_copyright_notice($op){
		if(isset($op['copyright_notice'])){
			op_update_option('copyright_notice',$op['copyright_notice']);
		}
	}
	
	
	
	function info_bar(){
		echo op_load_section('info_bar');
	}
	
	function save_info_bar($op){
		if(isset($op['info_bar'])){
			$info_bar = array();
			$fields = array('twitter','email','rss');
			foreach($fields as $field){
				$val = op_get_var($op['info_bar'],$field);
				if(!empty($val)){
					$info_bar[$field] = $val;
				}
			}
			op_update_option('info_bar',$info_bar);
		}
	}
	
	
	function typography(){
		echo op_load_section('typography');
	}
	
	function save_typography($op){
		if(isset($op['typography'])){
			$op = $op['typography'];
			$typography = op_get_option('typography');
			$typography = is_array($typography) ? $typography : array();
			$typography_elements = op_typography_elements();
			$typography['font_elements'] = op_get_var($typography,'font_elements',array());
			$typography['color_elements'] = op_get_var($typography,'font_elements',array());
			if(isset($typography_elements['font_elements'])){
				foreach($typography_elements['font_elements'] as $name => $options){
					$tmp = op_get_var($op,$name,op_get_var($typography['font_elements'],$name,array()));
					$typography['font_elements'][$name] = array(
						'size' => op_get_var($tmp,'size'),
						'font' => op_get_var($tmp,'font'),
						'style' => op_get_var($tmp,'style'),
						'color' => op_get_var($tmp,'color'),
					);
				}
			}
			if(isset($typography_elements['color_elements'])){
				foreach($typography_elements['color_elements'] as $name => $options){
					$typography['color_elements'][$name] = op_get_var($op,$name,op_get_var($typography['color_elements'],$name,array()));
				}
			}
			op_update_option('typography',$typography);
		}
	}
}