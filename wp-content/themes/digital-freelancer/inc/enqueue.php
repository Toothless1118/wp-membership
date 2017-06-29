<?php
/**
 * Understrap enqueue scripts
 *
 * @package understrap
 */
if ( ! function_exists( 'df_signup_ajax_request' ) ) {
	function df_signup_ajax_request(){
		$ch = crul_init('http://localhost:8080/wp-json/mp/v1/members');
		$encoded = '';
		foreach($POST as $name => $value){
			$encoded .= urlencode($name).'='.urlencode($value).'&';
		}
		$encoded = substr($encoded, 0, strlen($encoded)-1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $encoded);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_POST, 1);
		$output = curl_exec($ch);
		curl_close($ch);
		echo json_encode($output);
		die();

	}
}

add_action('wp_ajax_df_signup_ajax_request', 'df_signup_ajax_request');
add_action('wp_ajax_noprive_df_signup_ajax_request', 'df_signup_ajax_request');

if ( ! function_exists( 'understrap_scripts' ) ) {
	/**
	 * Load theme's JavaScript sources.
	 */
	function understrap_scripts() {
		// Get the theme data.
		$the_theme = wp_get_theme();
		wp_enqueue_style( 'understrap-styles', get_stylesheet_directory_uri() . '/css/theme.min.css', array(), $the_theme->get( 'Version' ) );
		wp_enqueue_style( 'understrap-styles1', get_stylesheet_directory_uri() . '/dist/css/style.css', array(), $the_theme->get( 'Version' ) );
		wp_enqueue_script( 'jquery' );
		
		wp_enqueue_script( 'understrap-scripts', get_template_directory_uri() . '/js/theme.min.js', array(), $the_theme->get( 'Version' ), true );
		wp_localize_script('wp-api', 'wpApiSettings', array('root'=>esc_url_raw(rest_url()), 'nonce'=>wp_create_nonce('wp_rest')));
		wp_enqueue_script('wp-api');
		//wp_enqueue_script( 'ajax-script', get_template_directory_uri() . '/js/my-ajax-script.js', array('jquery') );
    	
		wp_enqueue_script( 'ajax-script', get_template_directory_uri() . '/dist/js/script.js', array('jquery'), $the_theme->get( 'Version' ), true );
		wp_localize_script( 'ajax-script', 'my_ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );

		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}

	}
} // endif function_exists( 'understrap_scripts' ).

add_action( 'wp_enqueue_scripts', 'understrap_scripts' );


