<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://mythemeshop.com
 * @since      1.0.0
 *
 * @package    MTSNB
 * @subpackage MTSNB/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the dashboard-specific stylesheet and JavaScript.
 *
 * @package    MTSNB
 * @subpackage MTSNB/public
 * @author     Your Name <email@example.com>
 */
class MTSNB_Shared {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Notification bar id
	 *
	 * @since    1.0.0
	 *
	 * @var      boolean
	 */
	private $bar_id = false;

	/**
	 * Bar settings.
	 *
	 * @since    1.0.0
	 *
	 * @var      boolean
	 */
	private $bar_data = false;

	/**
	 * A/B test variation.
	 *
	 * @since    1.0.3
	 *
	 * @var      boolean
	 */
	private $bar_variation = false;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Check if Notification Bar should be displayed on front end
	 *
	 * @since    1.0.0
	 */
	public function get_notification_bar_data() {

		if ( is_admin() ) return;

		$bar_id   = false;
		$bar_data = false;

		$default_supported_post_types = array( 'post', 'page' );
		$cpt_supp = get_option( 'mtsnb_supported_custom_post_types', array() );
		$cpt_supp = !empty( $cpt_supp ) ? $cpt_supp : array();
		// Filter supported post types where user can override bar on single view ( if user want to disable it on posts or pages for example )
		$force_bar_supported_post_types = apply_filters( 'mtsnb_force_bar_post_types', array_merge( $cpt_supp, $default_supported_post_types ) );

		if ( ( $key = array_search( 'mts_notification_bar', $force_bar_supported_post_types ) ) !== false ) {

			unset( $force_bar_supported_post_types[ $key ] );
		}

		if ( is_singular( $force_bar_supported_post_types ) ) {

			global $post;
			$bar = get_post_meta( $post->ID, '_mtsnb_override_bar', true );

			if ( $bar && !empty( $bar ) ) {

				$bar_id = isset( $bar[0] ) ? $bar[0] : false;

				if ( $bar_id && !empty( $bar_id ) ) {

					$meta_values = get_post_meta( $bar_id, '_mtsnb_data', true );

					$passed_time_conditions     = $this->test_time( $meta_values );
					$passed_mobile_conditions   = $this->test_mobile( $meta_values );
					$passed_logged_conditions   = $this->test_logged( $meta_values );
					$passed_less_conditions     = $this->test_less( $bar_id, $meta_values );
					$passed_referrer_conditions = $this->test_referrer( $meta_values );
					$passed_utm_conditions      = $this->test_utm( $meta_values );
					$passed_after_conditions    = $this->test_after( $bar_id, $meta_values );

					if ( $passed_time_conditions && $passed_mobile_conditions && $passed_logged_conditions && $passed_less_conditions && $passed_referrer_conditions && $passed_utm_conditions && $passed_after_conditions ) {

						$this->bar_id   = $bar_id;
						$this->bar_data = $meta_values;

						// A/B Testing
						$this->ab_test();

						return;
					}
				}
			}
		}

		$args = array(
			'post_type' => 'mts_notification_bar',
			'posts_per_page' => -1,
			'post_status' => 'publish',
		);

		$all_bars = get_posts( $args );
		foreach( $all_bars as $post ) :
			setup_postdata( $post );

			$bar_id = $post->ID;
			$meta_values = get_post_meta( $bar_id, '_mtsnb_data', true );

			$passed_location_conditions = $this->test_location( $meta_values );
			$passed_time_conditions     = $this->test_time( $meta_values );
			$passed_mobile_conditions   = $this->test_mobile( $meta_values );
			$passed_logged_conditions   = $this->test_logged( $meta_values );
			$passed_less_conditions     = $this->test_less( $bar_id, $meta_values );
			$passed_referrer_conditions = $this->test_referrer( $meta_values );
			$passed_utm_conditions      = $this->test_utm( $meta_values );
			$passed_after_conditions    = $this->test_after( $bar_id, $meta_values );

			if ( $passed_location_conditions && $passed_time_conditions && $passed_mobile_conditions && $passed_logged_conditions && $passed_less_conditions && $passed_referrer_conditions && $passed_utm_conditions && $passed_after_conditions ) {
				
				$this->bar_id   = $bar_id;
				$this->bar_data = $meta_values;

				// A/B Testing
				$this->ab_test();

				break;
			}

		endforeach; wp_reset_postdata();
	}

	/**
	 * A/B Testing.
	 *
	 * @since    1.1.0
	 */
	public function ab_test() {

		$this->bar_variation = 'none';

		if ( !is_admin() && !current_user_can('edit_published_posts') ) {

			$ab_test = isset( $this->bar_data['b_enabled'] ) ? $this->bar_data['b_enabled'] : '';

			if ( !empty( $ab_test ) ) {

				$mtsnb_stats = get_option( 'mtsnb_stats', array() );

				$ab_count = isset( $mtsnb_stats[ $this->bar_id ]['ab_count'] ) ? $mtsnb_stats[ $this->bar_id ]['ab_count'] : 1;
				$a_count  = isset( $mtsnb_stats[ $this->bar_id ]['a_count'] ) ? $mtsnb_stats[ $this->bar_id ]['a_count'] : 0;

				if ( !isset( $_COOKIE[ 'mtsnb_ab_'.$this->bar_id ] ) || empty( $_COOKIE[ 'mtsnb_ab_'.$this->bar_id ] ) ) {

					// First visitor
					if ( 1 == $ab_count ) {

						// Random variation
						$this->bar_variation = ( 1 == rand( 1, 2 ) ) ? 'a' : 'b';

					} else {

						$a_traffic = isset( $this->bar_data['a_traffic'] ) ? (int) $this->bar_data['a_traffic'] : 50;

						$this->bar_variation = ( $a_count*100/$ab_count < $a_traffic ) ? 'a' : 'b';
					}

					// Set cookie
					$cookies_expiry = (int) get_option( 'notification_bar_cookies_expiry', 365 );
					$secure = ( 'https' === parse_url( home_url(), PHP_URL_SCHEME ) );// maybe not needed
					setcookie( 'mtsnb_ab_'.$this->bar_id, $this->bar_variation, time() + ( 86400 * $cookies_expiry ), COOKIEPATH, COOKIE_DOMAIN, $secure ); // 86400 = 1 day

					$mtsnb_stats[ $this->bar_id ]['ab_count'] = $ab_count + 1;

					if ( !isset( $mtsnb_stats[ $this->bar_id ][ $this->bar_variation.'_count' ] ) ) {

						$mtsnb_stats[ $this->bar_id ][ $this->bar_variation.'_count' ] = 1;

					} else {

						$mtsnb_stats[ $this->bar_id ][ $this->bar_variation.'_count' ] = (int) $mtsnb_stats[ $this->bar_id ][ $this->bar_variation.'_count' ] + 1;
					}

					update_option( 'mtsnb_stats', $mtsnb_stats );

				} else {
					
					$this->bar_variation = $_COOKIE[ 'mtsnb_ab_'.$this->bar_id ];
				}
			}
		}
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		if ( is_admin() && function_exists('get_current_screen') ) {// Needed for Notification Bar preview on admin side ( checking for 'get_current_screen' func because of one plugin conflict report )

			$screen = get_current_screen();
			$screen_id = $screen->id;

			if ( 'mts_notification_bar' === $screen_id ) {

				wp_enqueue_style( 'fontawesome', MTSNB_PLUGIN_URL . 'public/css/font-awesome.min.css', array(), $this->version, 'all' );
				wp_enqueue_style( 'mtsnb-flipclock', MTSNB_PLUGIN_URL . 'public/css/flipclock.css', array(), $this->version, 'all' );
				wp_enqueue_style( 'mtsnb-magnific', MTSNB_PLUGIN_URL . 'public/css/magnific-popup.css', array(), $this->version, 'all' );
				wp_enqueue_style( 'mtsnb-owl', MTSNB_PLUGIN_URL . 'public/css/owl.carousel.css', array(), $this->version, 'all' );
				wp_enqueue_style( $this->plugin_name.'admin', MTSNB_PLUGIN_URL . 'public/css/mts-notification-bar-public.css', array(), $this->version, 'all' );
			}

		} else {

			if ( $this->bar_id && $this->bar_data ) {

				$content_type = isset( $this->bar_data['content_type'] ) ? $this->bar_data['content_type'] : '';

				if ( !empty( $content_type ) ) {
					
					if ( 'countdown' === $content_type || 'countdown-b' === $content_type ) {
						wp_enqueue_style( 'mtsnb-flipclock', MTSNB_PLUGIN_URL . 'public/css/flipclock.css', array(), $this->version, 'all' );
					}
					if ( 'popup' === $content_type ) {
						wp_enqueue_style( 'mtsnb-magnific', MTSNB_PLUGIN_URL . 'public/css/magnific-popup.css', array(), $this->version, 'all' );
					}
					if ( 'posts' === $content_type || 'facebook' === $content_type || 'twitter' === $content_type ) {
						wp_enqueue_style( 'mtsnb-owl', MTSNB_PLUGIN_URL . 'public/css/owl.carousel.css', array(), $this->version, 'all' );
					}

					wp_enqueue_style( 'fontawesome', MTSNB_PLUGIN_URL . 'public/css/font-awesome.min.css', array(), $this->version, 'all' );
					wp_enqueue_style( $this->plugin_name, MTSNB_PLUGIN_URL . 'public/css/mts-notification-bar-public.css', array(), $this->version, 'all' );
				}
			}
		}
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		if ( is_admin() ) {// Needed for Notification Bar preview on admin side

			$screen = get_current_screen();
			$screen_id = $screen->id;

			if ( 'mts_notification_bar' === $screen_id ) {

				wp_enqueue_script( 'mtsnb-magnific', MTSNB_PLUGIN_URL . 'public/js/jquery.magnific-popup.min.js', array( 'jquery' ), $this->version, false );
				wp_enqueue_script( 'mtsnb-flipclock', MTSNB_PLUGIN_URL . 'public/js/flipclock.min.js', array( 'jquery' ), $this->version, false );
				wp_enqueue_script( 'mtsnb-owl', MTSNB_PLUGIN_URL . 'public/js/owl.carousel.min.js', array( 'jquery' ), $this->version, false );
			}

		} else {

			$cookies_expiry = (int) get_option( 'notification_bar_cookies_expiry', 365 );

			wp_enqueue_script( 'mtsnb-cookie', MTSNB_PLUGIN_URL . 'public/js/jquery.cookie.js', array( 'jquery' ), $this->version, false );

			wp_register_script( $this->plugin_name, MTSNB_PLUGIN_URL . 'public/js/mts-notification-bar-public.js', array( 'jquery' ), $this->version, false );
			wp_localize_script(
				$this->plugin_name,
				'mtsnb_data',
				array(
					'ajaxurl'=> admin_url( 'admin-ajax.php' ),
					'cookies_expiry'=> $cookies_expiry,
				)
			);

			if ( $this->bar_id && $this->bar_data ) {

				$content_type = isset( $this->bar_data['content_type'] ) ? $this->bar_data['content_type'] : '';

				if ( !empty( $content_type ) ) {

					if ( 'countdown' === $content_type || 'countdown-b' === $content_type ) {
						wp_enqueue_script( 'mtsnb-flipclock', MTSNB_PLUGIN_URL . 'public/js/flipclock.min.js', array( 'jquery' ), $this->version, false );
					}
					if ( 'popup' === $content_type ) {
						wp_enqueue_script( 'mtsnb-magnific', MTSNB_PLUGIN_URL . 'public/js/jquery.magnific-popup.min.js', array( 'jquery' ), $this->version, false );
					}
					if ( 'posts' === $content_type || 'facebook' === $content_type || 'twitter' === $content_type ) {
						wp_enqueue_script( 'mtsnb-owl', MTSNB_PLUGIN_URL . 'public/js/owl.carousel.min.js', array( 'jquery' ), $this->version, false );
					}
					
					wp_enqueue_script( $this->plugin_name );
				}
			}
		}
	}

	/**
	 * Display Notification Bar on front end
	 *
	 * @since    1.0.0
	 */
	public function display_bar() {

		if ( $this->bar_id && $this->bar_data ) {

			$this->bar_output( $this->bar_id, $this->bar_data );
		}
	}

	/**
	 * Notification bar output.
	 *
	 * @since    1.0.0
	 */
	public function bar_output( $bar_id, $meta_values ) {

		$button_type = $meta_values['button'];
		$close_icon  = isset( $meta_values['close_icon'] ) ? $meta_values['close_icon'] : '';
		$show_icon   = isset( $meta_values['show_icon'] ) ? $meta_values['show_icon'] : '';

		$button_close_icon = empty( $close_icon ) ? '<span>+</span>' : '<i class="fa fa-'.$meta_values['close_icon'].'"></i>';
		$button_open_icon  = empty( $show_icon ) ? '<span>+</span>' : '<i class="fa fa-'.$meta_values['show_icon'].'"></i>';

		$button_bg_color = isset( $meta_values['button_bg_color'] ) ? $meta_values['button_bg_color'] : '#3071A9';
		$button_color    = isset( $meta_values['button_color'] ) ? $meta_values['button_color'] : '#ffffff';

		$style = 'background-color:'.$meta_values['bg_color'].';color:'.$meta_values['txt_color'].';';
		$btn_style = 'background-color:'.$button_bg_color.';color:'.$button_color.';';
		
		$link_color = isset( $meta_values['link_color'] ) ? $meta_values['link_color'] : '#dedede';
		$link_hover_color = isset( $meta_values['link_hover_color'] ) ? $meta_values['link_hover_color'] : '#ffffff';

		$shadow = isset( $meta_values['shadow'] ) ? '-webkit-box-shadow: 0 3px 4px rgba(0, 0, 0, 0.4);box-shadow: 0 3px 4px rgba(0, 0, 0, 0.4);' : '';

		$width = ( isset( $meta_values['content_width'] ) && !empty( $meta_values['content_width'] ) ) ? $meta_values['content_width'] : '960';
		$width = (int)$width+120;

		$padding = isset( $meta_values['padding'] ) && !empty( $meta_values['padding'] ) ? $meta_values['padding'] : '10';

		$font_weight = isset( $meta_values['font_weight'] ) ? $meta_values['font_weight'] : 'bold';
		$line_height = isset( $meta_values['line_height'] ) ? $meta_values['line_height'] : '1.4';

		$screen_position = isset( $meta_values['screen_position'] ) ? $meta_values['screen_position'] : 'top';
		$screen_position_class = ' mtsnb-'.$screen_position;

		$css_position = isset( $meta_values['css_position'] ) ? $meta_values['css_position'] : 'fixed';
		$css_position_class = ' mtsnb-'.$css_position;

		$animation = isset( $meta_values['animation'] ) ? $meta_values['animation'] : '';
		$content_animation = isset( $meta_values['content_animation'] ) ? $meta_values['content_animation'] : '';
		$visibility_class = empty( $animation ) ? '' : ' mtsnb-invisible';

		$initial_state = ( isset( $meta_values['initial_state'] ) && 'toggle_button' === $button_type ) ? $meta_values['initial_state'] : '';
		$state_class = ( 'closed' === $initial_state ) ? 'mtsnb-hidden' : 'mtsnb-shown';

		$remember_state_class = ( isset( $meta_values['remember_state'] ) && 'no_button' !== $button_type ) ? ' mtsnb-remember-state' : '';

		$mtsnb_state = '';
		if ( !empty( $remember_state_class ) && !is_admin() && isset( $_COOKIE[ 'mtsnb_state_'.$bar_id ] ) ) {

			$mtsnb_state = $_COOKIE[ 'mtsnb_state_'.$bar_id ];

			if ( !empty( $mtsnb_state ) ) {

				$state_class = 'closed' === $mtsnb_state ? 'mtsnb-hidden' : 'mtsnb-shown';
			}
		}

		if ( 'mtsnb-hidden' == $state_class || !empty( $mtsnb_state ) ) {

			$animation = $content_animation = $visibility_class = '';
		}
		?>
		<div class="mtsnb <?php echo $state_class.$visibility_class.$remember_state_class.$screen_position_class.$css_position_class; ?>" id="mtsnb-<?php echo $bar_id; ?>" data-mtsnb-id="<?php echo $bar_id; ?>" style="<?php echo $style;?>" data-bar-animation="<?php echo $animation; ?>" data-bar-content-animation="<?php echo $content_animation; ?>">
			<style type="text/css">
				.mtsnb { position: <?php echo $css_position;?>; <?php echo $shadow;?>}
				.mtsnb .mtsnb-container { width: <?php echo $width;?>px; font-size: <?php echo $meta_values['font_size'];?>px; font-weight: <?php echo $font_weight;?>; line-height: <?php echo $line_height;?>}
				.mtsnb a { color: <?php echo $link_color;?>;}
				.mtsnb a:hover { color: <?php echo $link_hover_color;?>;}
				.mtsnb .mtsnb-button { color: <?php echo $button_color;?>!important; background-color: <?php echo $button_bg_color;?>;}
			</style>
			<div class="mtsnb-container-outer">
				<div class="mtsnb-container mtsnb-clearfix" style="padding-top: <?php echo $padding; ?>px; padding-bottom: <?php echo $padding; ?>px;">
					<?php do_action('before_mtsnb_content'); ?>
					<?php $this->bar_content( $meta_values ); ?>
					<?php do_action('after_mtsnb_content'); ?>
				</div>
				<?php if ( 'no_button' !== $button_type ) { ?>
				<?php if ( 'toggle_button' === $button_type ) {?><a href="#" class="mtsnb-show" style="<?php echo $btn_style; ?> padding: <?php echo $padding; ?>px;"><?php echo $button_open_icon; ?></a><?php } ?>
				<a href="#" class="mtsnb-hide" style="<?php echo $style; ?>"><?php echo $button_close_icon; ?></a>
				<?php } ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Notification bar content.
	 *
	 * @since    1.0.0
	 */
	public function bar_content( $options ) {

		// A/B Testing
		$b_prefix = '';
		$variation = $this->bar_variation;
		if ( 'b' === $variation ) {

			$b_prefix = 'b_';
		}

		// Output
		echo '<div class="mtsnb-'.$options[ $b_prefix.'content_type' ].'-type mtsnb-content" data-mtsnb-variation="'.esc_attr( $variation ).'">';
		switch ( $options[$b_prefix.'content_type'] ) {
			case 'button':
			case 'button-b':

				$text       = esc_html( $options[$b_prefix.'basic_text'] );
				$link_text  = esc_html( $options[$b_prefix.'basic_link_text'] );
				$link_url   = esc_url( $options[$b_prefix.'basic_link_url'] );
				$link_style = $options[$b_prefix.'basic_link_style'];

				echo '<span class="mtsnb-text">'.$text.'</span><a href="'.$link_url.'" class="mtsnb-'.$link_style.'">'.$link_text.'</a>';

			break;
			case 'posts':
			case 'posts-b':
				$text          = $options[$b_prefix.'posts_text'];
				$posts_number  = intval( $options[$b_prefix.'posts_number'] );
				$posts_to_show = $options[$b_prefix.'posts_to_show'];
				$post_thumb    = (bool) isset( $options[$b_prefix.'post_thumb'] );

				$args = array(
					'ignore_sticky_posts' => 1,
					'post_status' => 'publish',
					'posts_per_page' => $posts_number,
				);

				if ( 'recent' !== $posts_to_show ) {

					if ( 'related' === $posts_to_show ) {
						if ( !is_singular( 'post' ) ) {
							return;
						}
						$id = get_the_ID();
						$args['post__not_in'] = array( $id );

					} else if ( 'custom_posts' === $posts_to_show ) {

						$posts_arr = explode( ',', $options[$b_prefix.'custom_posts'] );

						$args['post__in'] = $options[$b_prefix.'custom_posts'];

					} else {

						$tax = ( 'custom_tags' === $posts_to_show ) ? 'post_tag' : 'category';
						$args['tax_query'] = array(array(
					        'taxonomy' => $tax,
					        'terms' => $options[ $b_prefix.$posts_to_show ],
					        'field' => 'id',
					        'operator' => 'IN'
					    ));
					}
				}

				if ( !empty( $text ) ) echo '<span class="mtsnb-text">'.esc_html( $text ).'</span>';

				$posts_query = new WP_Query( $args );
				if ( 1 < $posts_number ) {
					?>
					<div class="mtsnb-slider-container loading">
						<div class="mtsnb-slider">
					<?php
				} else {
					?>
					<div class="mtsnb-posts">
					<?php
				}
				if ( $posts_query->have_posts() ) :
					?>
					
					<?php
					while ( $posts_query->have_posts() ) : $posts_query->the_post();?>
					<div class="mtsnb-post">
						<?php if ( has_post_thumbnail() && $post_thumb ) {?>
						<div class="mtsnb-post-img">
							<a rel="nofollow" href="<?php echo esc_url( get_the_permalink() ); ?>" title="<?php echo esc_attr( get_the_title() ); ?>">
								<?php the_post_thumbnail( 'mtsnb-thumb', array( 'title' => '' ) ); ?>
							</a>
						</div>
						<?php } ?>
						<div class="mtsnb-post-data">
							<div class="mtsnb-post-title">
								<a rel="nofollow" href="<?php echo esc_url( get_the_permalink() ); ?>" title="<?php echo esc_attr( get_the_title() ); ?>"><?php echo esc_html( get_the_title() ); ?></a>
							</div>
						</div>
					</div>
				<?php
					endwhile;
					?>
					</div>
					<?php
				endif; wp_reset_postdata();

				if ( 1 < $posts_number ) {
					?>
						</div>
					</div>
					<?php
				} else {
					?>
					</div>
					<?php
				}

			break;
			case 'newsletter':
			case 'newsletter-b':
			
				$type       = $options[$b_prefix.'newsletter_type'];
				$text       = $options[$b_prefix.'newsletter_text'];
				$btn_txt    = isset( $options[$b_prefix.'newsletter_btn_text'] ) && !empty( $options[$b_prefix.'newsletter_btn_text'] ) ? $options[$b_prefix.'newsletter_btn_text'] : __( 'Subscribe', $this->plugin_name );
				$include_name     = (bool) isset( $options[$b_prefix.'newsletter_name'] );
				$include_lastname = (bool) isset( $options[$b_prefix.'newsletter_lastname'] );
				$feedburner_id = $options[$b_prefix.'newsletter_fb_id'];

				if ( 'fb' === $type ) { ?>

					<form class="mtsnb-fb-form" method="post" action="http://feedburner.google.com/fb/a/mailverify" target="mtsnbwindow">
						<?php if ( !empty( $text ) ) { ?>
							<span class="mtsnb-text"><?php echo esc_html( $text ); ?></span>
						<?php } ?>
						<fieldset class="mtsnb-fieldset">
							<input name="mtsnb-newsletter-email" type="text" size="30px" placeholder="<?php _e( 'Email', $this->plugin_name ); ?>"/>
							<input value="<?php echo esc_attr( $btn_txt ); ?>" type="submit" class="mtsnb-submit mtsnb-button mtsnb-newsletter-button" name="mtsnb-submit"/>
						</fieldset>
					</form>
					<script type="text/javascript">
						jQuery(document).ready(function($){
							$(".mtsnb-fb-form").submit(function(e){
								e.preventDefault();
								window.open('http://feedburner.google.com/fb/a/mailverify?uri=<?php echo esc_attr( $feedburner_id ); ?>', 'mtsnbwindow', 'scrollbars=yes,width=550,height=520');
								return true;
							});
						});
					</script>
				<?php
			 	} else {
			 		?>

			 		<form id="mtsnb-newsletter" class="mtsnb-form" method="post" novalidate="true">
						<?php if ( !empty( $text ) ) { ?>
							<span class="mtsnb-text"><?php echo esc_html( $text ); ?></span>
						<?php } ?>
						<fieldset class="mtsnb-fieldset">
							<?php if ( $include_name ) { ?>
								<input id="mtsnb-first-name" name="mtsnb-fname" type="text" placeholder="<?php _e('First Name', $this->plugin_name ); ?>"/>
							<?php } ?>

							<?php if ( $include_lastname ) { ?>
								<input id="mtsnb-last-name" name="mtsnb-lname" type="text" placeholder="<?php _e('Last Name', $this->plugin_name ); ?>"/>
							<?php } ?>

							<p style="display: none;" id="mtsnb-newsletter-type"><?php echo $type; ?></p>
							<input id="mtsnb-email" name="mtsnb-email" type="email" placeholder="<?php _e('Email', $this->plugin_name ); ?>"/>
							<input value="<?php echo $btn_txt; ?>" type="submit" class="mtsnb-submit mtsnb-button mtsnb-newsletter-button" name="mtsnb-submit"/>

							<label class="mtsnb-message"></label>
						</fieldset>
					</form>
					<?php
			 	}
			break;
			case 'social':
			case 'social-b':
				$text      = $options[$b_prefix.'social_text'];
				$networks  = isset( $options[$b_prefix.'social'] ) ? $options[$b_prefix.'social'] : array();
				if ( !empty( $text ) ) {
				?>
					<span class="mtsnb-text"><?php echo esc_html( $text ); ?></span>
				<?php
				}
				if ( !empty( $networks ) ) {
					?>
					<div class="mtsnb-social-icons">
					<?php
					foreach ( $networks as $key => $value ) {
						?>
						<a class="mtsnb-social-icon-link" href="<?php echo esc_url($value['url']); ?>" target="_blank"><i class="mtsnb-social-icon fa <?php echo $value['type']; ?>"></i></a>
						<?php
					}
					?>
					</div>
					<?php
				}
			break;
			case 'facebook':
			case 'facebook-b':
				$text            = $options[$b_prefix.'fb_text'];
				$fb_page_id      = is_numeric( $options[$b_prefix.'fb_page_id'] ) ? $options[$b_prefix.'fb_page_id'] : '';
				$fb_posts_number = intval( $options[$b_prefix.'fb_posts_number'] );
				$fb_app_id       = $options[$b_prefix.'fb_app_id'];
				$fb_app_secret   = $options[$b_prefix.'fb_app_secret'];
				
				if ( !empty( $text ) ) echo '<span class="mtsnb-text">'.esc_html( $text ).'</span>';

				if ( !empty( $fb_app_id ) && !empty( $fb_app_secret ) && !empty( $fb_page_id ) ) {

					$content = get_transient('mtsnb_facebook_feed');

					if ( false === $content ) {

						if ( ! class_exists('Facebook') ) {
							include_once( MTSNB_PLUGIN_DIR . '/includes/facebook-sdk/facebook.php' );
						}
								
						// Make our facebook connection.
						$facebook = new Facebook(array(
							'appId'  => $fb_app_id,
							'secret' => $fb_app_secret,
						));

						$content = $facebook->api('/'. $fb_page_id .'/feed');

						set_transient('mtsnb_facebook_feed', $content, 300 );
					}

					$output = "";
					
					if ( $content && count( $content['data'] ) > 0 ) {
						
						$count = 0;

						if ( 1 == $fb_posts_number ) {
							$output .= '<div class="mtsnb-bf-feed">';
						} else {
							$output .= '<div class="mtsnb-bf-feed mtsnb-slider-container loading"><div class="mtsnb-slider">';
						}
									
						foreach( $content['data'] as $item ) {
							
							if ( empty($item) )
								continue;
											
							if ( $fb_page_id != $item['from']['id'] )
								continue;

							$message = isset($item['message']) ? trim($item['message']) : null;
							$message = preg_replace(array('{\b((https?|ftp)://[-a-zA-Z0-9+&@#/%?=~_|!:,.;]*[a-zA-Z0-9+&@#/%=~_|])}', '/\n/'), array("<a href='$1'>\\1</a>", '<br />'), $message);

							$descript = isset($item['description']) ? trim($item['description']) : null;
							// Turn urls into links and replace new lines with <br />
							$descript = preg_replace(array('{\b((https?|ftp)://[-a-zA-Z0-9+&@#/%?=~_|!:,.;]*[a-zA-Z0-9+&@#/%=~_|])}', '/\n/'), array("<a href='$1'>\\1</a>", '<br />'), $descript);
							
							$story = isset($item['story']) ? trim($item['story']) : null;
							$story = preg_replace('/\n/', '<br />', $story);

							// Item opening tag
							$output .= "<div class='mtsnb-fb-feed-item mtsnb-fb-item-". $count ."' id='mtsnb-fb-feed-". $item['id'] ."'>\n";
								
								// The actual users status
								if ( $message != null  )
									$output .= "<p class='mtsnb-fb-message'>". $message ."</p>\n";
								else if ( $story != null )
									$output .= "<p class='mtsnb-fb-story'>". $story ."</p>\n";
							
							$output .= "</div>\n";
							
							$count++;

							if ( $count == $fb_posts_number ) {
								break;
							}

						}

						if ( 1 === $fb_posts_number ) {
							$output .= '</div>';
						} else {
							$output .= '</div></div>';
						}
					}

					echo $output;
				}
				?>
				<?php
			break;
			case 'twitter':
			case 'twitter-b':
				if ( version_compare( PHP_VERSION, '5.4.0' ) >= 0 ) {

					$args = array();

					$text = $options[$b_prefix.'twitts_text'];

					$args['twitts_number'] = $options[$b_prefix.'twitts_number'];
					$args['api_key']       = $options[$b_prefix.'twitter_api_key'];
					$args['api_secret']   = $options[$b_prefix.'twitter_api_secret'];
					$args['token']         = $options[$b_prefix.'twitter_token'];
					$args['token_secret']  = $options[$b_prefix.'twitter_token_secret'];

					if ( !empty( $text ) ) echo '<span class="mtsnb-text">'.esc_html( $text ).'</span>';

					if ( ! class_exists('Codebird') ) {

						include_once( MTSNB_PLUGIN_DIR . '/includes/twitter/codebird.php' );
					}

					require_once( MTSNB_PLUGIN_DIR . '/includes/twitter/functions.php' );

					$out = mtsnb_get_twitter_content( $args );

				} else {

					$out = '<div class="mtsnb-twitter-feed">';
					$out .= __( 'The Twitter Feed content requires at least PHP version 5.4.0', $this->plugin_name );
					$out .= '</div>';
				}

				echo $out;
				
			break;
			case 'countdown':
			case 'countdown-b':
				$text = $options[$b_prefix.'counter_text'];
				$btn_text = $options[$b_prefix.'counter_btn_text'];
				$btn_url = esc_url( $options[$b_prefix.'counter_btn_url'] );
				$till_date = !empty( $options[$b_prefix.'counter_date'] ) ? strtotime( $options[$b_prefix.'counter_date'] ) : '';
				$till_time = !empty( $options[$b_prefix.'counter_time'] ) ? strtotime( $options[$b_prefix.'counter_time'] ) : '';
				$future_time = strtotime( $options[$b_prefix.'counter_date'].' '.$options[$b_prefix.'counter_time'] );
				$time = current_time('timestamp');
				if ( !empty( $till_date ) && !empty( $till_time ) ) {
					$count_amount = $future_time-$time;
					?>
					<?php if ( !empty( $text ) ) { ?><span class="mtsnb-text"><?php echo esc_html( $text ); ?></span><?php } ?>
					<div class="mtsnb-clock"></div>
					<input type="hidden" class="mtsnb-clock-till" value="<?php echo $count_amount; ?>" />
					<?php if ( !empty( $btn_text ) && !empty( $btn_url ) ) { ?><div class="mtsnb-counter-button-wrap"><a href="<?php echo $btn_url; ?>" class="mtsnb-button"><?php echo esc_html( $btn_text ); ?></a></div><?php } ?>
					<?php
				} else {
					
					_e( 'Please choose date and/or time in future', $this->plugin_name );
				}
			break;
			case 'popup':
			case 'popup-b':
				$text         = $options[$b_prefix.'popup_text'];
				$type         = $options[$b_prefix.'popup_video_type'];
				$youtube_link = $options[$b_prefix.'popup_youtube_link'];
				$vimeo_link   = $options[$b_prefix.'popup_vimeo_link'];
				$link = ( 'vimeo' === $type ) ? $vimeo_link : $youtube_link;
				if ( !empty( $text ) && !empty( $link ) ) {
				?>
					<a href="<?php echo esc_url( $link ); ?>" class="mtsnb-text mtsnb-popup-<?php echo $type; ?>"><?php echo esc_html( $text ); ?></a>
				<?php
				} else {
				?>
					<span class="mtsnb-text"><?php _e( 'Please enter text and link to your video', $this->plugin_name )?></span>
				<?php
				}
			break;
			case 'search':
			case 'search-b':
				$text = $options[$b_prefix.'search_text'];
				if ( !empty( $text ) ) echo '<span class="mtsnb-text">'.esc_html( $text ).'</span>';
				echo '<div class="mtsnb-search-form">';
					$this->get_search_form();
				echo '</div>';
			break;
			case 'custom':
			case 'custom-b':
				echo '<div class="mtsnb-custom-content">';
					echo do_shortcode( html_entity_decode( $options[$b_prefix.'custom_content'] ) );
				echo '</div>';
			break;
		}
		echo '</div>';
	}

	/**
	 * Modified version of WP's native get_search_form
	 *
	 * @since    1.0.0
	 */
	public function get_search_form( $echo = true ) {
		do_action( 'pre_get_search_form' );
		$format = current_theme_supports( 'html5', 'search-form' ) ? 'html5' : 'xhtml';
		$format = apply_filters( 'search_form_format', $format );
		if ( 'html5' == $format ) {
			$form = '<form role="search" method="get" class="mtsnb-search-form" action="' . esc_url( home_url( '/' ) ) . '">
				<label>
					<span class="screen-reader-text">' . _x( 'Search for:', 'label' ) . '</span>
					<input type="search" class="search-field" placeholder="' . esc_attr_x( 'Search &hellip;', 'placeholder' ) . '" value="' . get_search_query() . '" name="s" title="' . esc_attr_x( 'Search for:', 'label' ) . '" />
				</label>
				<input type="submit" class="search-submit" value="'. esc_attr_x( 'Search', 'submit button' ) .'" />
			</form>';
		} else {
			$form = '<form role="search" method="get" id="mtsnb-searchform" class="mtsnb-search-form" action="' . esc_url( home_url( '/' ) ) . '">
				<div>
					<label class="screen-reader-text" for="s">' . _x( 'Search for:', 'label' ) . '</label>
					<input type="text" value="' . get_search_query() . '" name="s" id="mtsnb-s" />
					<input type="submit" id="mtsnb-searchsubmit" class="mtsnb-submit mtsnb-button" value="'. esc_attr_x( 'Search', 'submit button' ) .'" />
				</div>
			</form>';
		}
		$result = apply_filters( 'get_search_form', $form );
		if ( null === $result )
			$result = $form;
		if ( $echo )
			echo $result;
		else
			return $result;
	}

	/**
	 * Notification bar admin preview.
	 *
	 * @since    1.0.0
	 */
	public function preview_bar() {
		
		$data = $_POST['form_data'];

		parse_str( $data, $options );

		$id = $options['post_ID'];
		$meta_values = $options['mtsnb_fields'];

		// fix slashes
		foreach ( $meta_values as $key => $value ) {

			if ( is_string( $value ) ) {

				$meta_values[ $key ] = stripslashes( $value );
			}
		}

		$this->bar_output( $id, $meta_values );

		die();
	}

	
	/**
	 * Tests if bar can be displayed based on referrer settings
	 *
	 * @since  1.0.0
	 */
	public function test_referrer( $meta_values ) {

		$no_condition = (bool) ( empty( $meta_values['conditions']['referrer']['state'] ) && empty( $meta_values['conditions']['notreferrer']['state'] ) );

		if ( $no_condition ) return true; // not set, can be displayed

		// Show for specific referrer
		if ( !empty( $meta_values['conditions']['referrer']['state'] ) ) {

			$referer = $this->get_referrer();
			if ( !$referer || empty( $referer ) ) return false; // not set, don't display
			$is_search_engine = $this->test_searchengine( $referer );

			$bar_referrers = isset( $meta_values['conditions']['referrer']['data'] ) ? $meta_values['conditions']['referrer']['data'] : array();
			$search_enabled = (bool) ( isset( $meta_values['conditions']['referrer']['search'] ) && !empty( $meta_values['conditions']['referrer']['search'] ) );

			if ( $search_enabled && $is_search_engine ) {

				return true;// referrer is search engine
			}

			if ( !empty( $bar_referrers ) && isset( $meta_values['conditions']['referrer']['custom'] ) && '1' === $meta_values['conditions']['referrer']['custom'] ) {

				foreach ( $bar_referrers as $bar_referrer ) {

					if ( false !== strpos( $referer, $bar_referrer ) ) {

						return true;// referrer matched
					}
				}
			}

			return false;
		}

		// Don't show for specific referrer
		if ( !empty( $meta_values['conditions']['notreferrer']['state'] ) ) {

			$referer = $this->get_referrer();
			if ( !$referer || empty( $referer ) ) return true; // not set, display bar
			$is_search_engine = $this->test_searchengine( $referer );

			$bar_referrers = isset( $meta_values['conditions']['notreferrer']['data'] ) ? $meta_values['conditions']['notreferrer']['data'] : array();
			$search_enabled = (bool) ( isset( $meta_values['conditions']['notreferrer']['search'] ) && !empty( $meta_values['conditions']['notreferrer']['search'] ) );

			if ( $search_enabled && $is_search_engine ) {

				return false;// block if referrer is search engine
			}

			if ( !empty( $bar_referrers ) && isset( $meta_values['conditions']['notreferrer']['custom'] ) && '1' === $meta_values['conditions']['notreferrer']['custom'] ) {

				foreach ( $bar_referrers as $bar_referrer ) {

					if ( false !== strpos( $referer, $bar_referrer ) ) {

						return false;// block if referrer matched
					}
				}
			}

			return true;
		}
	}

	/**
	 * Tests if bar can be displayed based on location settings
	 *
	 * @since  1.0.0
	 */
	public function test_location( $meta_values ) {

		$no_condition = (bool) ( empty( $meta_values['conditions']['location']['state'] ) && empty( $meta_values['conditions']['notlocation']['state'] ) );

		if ( $no_condition ) return true; // not set, can be displayed

		// Enable on locations
		if ( !empty( $meta_values['conditions']['location']['state'] ) ) {

			if (
				'page' === get_option('show_on_front') &&
				'0' !== get_option('page_for_posts') &&
				'0' !== get_option('page_on_front') &&
				( ( is_front_page() && isset( $meta_values['conditions']['location']['home'] ) ) || ( is_home() && isset( $meta_values['conditions']['location']['blog_home'] ) ) )
			) {

				return true;

			} else if ( is_front_page() && isset( $meta_values['conditions']['location']['home'] ) ) {

				return true;

			} else if ( is_singular( 'post' ) && isset( $meta_values['conditions']['location']['posts'] ) ) {

				$posts_list = isset( $meta_values['conditions']['location']['custom_posts'] ) ? $meta_values['conditions']['location']['custom_posts'] : '';
				if ( is_array( $posts_list ) ) {// backward compatibility
					$posts_arr = $posts_list;
				} else {
					$posts_arr = explode( ',', $posts_list );
				}

				if ( empty( $posts_list ) ) return true;

				if ( is_single( $posts_arr ) ) return true;

				return false;

			} else if ( is_page() && isset( $meta_values['conditions']['location']['pages'] ) ) {

				$pages_arr = isset( $meta_values['conditions']['location']['custom_pages'] ) ? $meta_values['conditions']['location']['custom_pages'] : array();

				if ( empty( $pages_arr ) ) return true;

				if ( is_page( $pages_arr ) ) return true;

				return false;

			} else if ( is_category() && isset( $meta_values['conditions']['location']['categories'] ) ) {
				
				$cat_arr = isset( $meta_values['conditions']['location']['custom_categories'] ) ? $meta_values['conditions']['location']['custom_categories'] : array();

				if ( empty( $cat_arr ) ) return true;

				$current_category = get_queried_object();
				$current_category_id = $current_category->term_id;

				foreach ( $cat_arr as $cat_id ) {

					if ( $current_category_id == $cat_id ) {

						return true;
					}
				}

				return false;

			} else if ( is_tag() && isset( $meta_values['conditions']['location']['tags'] ) ) {

				$tag_arr = isset( $meta_values['conditions']['location']['custom_tags'] ) ? $meta_values['conditions']['location']['custom_tags'] : array();

				if ( empty( $tag_arr ) ) return true;

				$current_tag = get_queried_object();
				$current_tag_id = $current_tag->term_id;

				foreach ( $tag_arr as $tag_id ) {

					if ( $current_tag_id == $tag_id ) {

						return true;
					}
				}

				return false;

			} else if ( is_date() && isset( $meta_values['conditions']['location']['date'] ) ) {

				return true;

			} else if ( is_author() && isset( $meta_values['conditions']['location']['author'] ) ) {

				return true;

			} else if ( is_search() && isset( $meta_values['conditions']['location']['search'] ) ) {

				return true;

			} else if ( is_404() && isset( $meta_values['conditions']['location']['404'] ) ) {

				return true;

			} else if ( isset( $meta_values['conditions']['location']['custom'] ) ) {

				global $wp;
				$current_url = home_url( add_query_arg( array(), $wp->request) );
				$custom_urls = isset( $meta_values['conditions']['location']['data'] ) ? $meta_values['conditions']['location']['data'] : array();

				if ( !empty( $custom_urls ) ) {

					foreach ( $custom_urls as $custom_url ) {

						if ( trailingslashit( $current_url ) === trailingslashit( $custom_url ) ) {

							return true;
						}
					}

					return false;
				}

			} else {

				$cpt_supp = get_option( 'mtsnb_supported_custom_post_types', array() );
				if ( !empty( $cpt_supp ) ) {

					if ( is_singular( $cpt_supp ) ) {

						foreach ( $cpt_supp as $cpt ) {

							if ( is_singular( $cpt ) && isset( $meta_values['conditions']['location'][ $cpt ] ) ) {

								$c_posts_list = isset( $meta_values['conditions']['location'][ 'custom_'.$cpt ] ) ? $meta_values['conditions']['location'][ 'custom_'.$cpt ] : '';

								if ( empty( $c_posts_list ) ) return true;

								$c_posts_arr = explode( ',', $c_posts_list );

								if ( is_single( $c_posts_arr ) ) return true;

								return false;
							}
						}

					} else if ( is_tax() ) {

						$current_post_type = get_post_type();

						if ( in_array( $current_post_type, $cpt_supp ) ) {

							$queried_object = get_queried_object();
							$current_taxonomy = $queried_object->taxonomy;

							if ( isset( $meta_values['conditions']['location'][ $current_taxonomy ] ) ) {

								$term_arr = isset( $meta_values['conditions']['location'][ 'custom_'.$current_taxonomy ] ) ? $meta_values['conditions']['location'][ 'custom_'.$current_taxonomy ] : array();

								if ( empty( $term_arr ) ) return true;

								$current_term_id = $queried_object->term_id;

								foreach ( $term_arr as $term_id ) {

									if ( $current_term_id == $term_id ) {

										return true;
									}
								}

								return false;
							}
						}
					}
				}

				return false;
			}
		}

		// Disable on locations
		if ( !empty( $meta_values['conditions']['notlocation']['state'] ) ) {

			if (
				'page' === get_option('show_on_front') &&
				'0' !== get_option('page_for_posts') &&
				'0' !== get_option('page_on_front') &&
				( ( is_front_page() && isset( $meta_values['conditions']['notlocation']['home'] ) ) || ( is_home() && isset( $meta_values['conditions']['notlocation']['blog_home'] ) ) )
			) {

				return false;
			
			} else if ( is_front_page() && isset( $meta_values['conditions']['notlocation']['home'] ) ) {

				return false;

			} else if ( is_singular( 'post' ) && isset( $meta_values['conditions']['notlocation']['posts'] ) ) {

				$posts_list = isset( $meta_values['conditions']['notlocation']['custom_posts'] ) ? $meta_values['conditions']['notlocation']['custom_posts'] : '';
				if ( is_array( $posts_list ) ) {// backward compatibility
					$posts_arr = $posts_list;
				} else {
					$posts_arr = explode( ',', $posts_list );
				}
				

				if ( empty( $posts_list ) ) return false;

				if ( is_single( $posts_list ) ) return false;

				return true;

			} else if ( is_page() && isset( $meta_values['conditions']['notlocation']['pages'] ) ) {

				$pages_arr = isset( $meta_values['conditions']['notlocation']['custom_pages'] ) ? $meta_values['conditions']['notlocation']['custom_pages'] : array();

				if ( empty( $pages_arr ) ) return false;

				if ( is_page( $pages_arr ) ) return false;

				return true;

			} else if ( is_category() && isset( $meta_values['conditions']['notlocation']['categories'] ) ) {

				$cat_arr = isset( $meta_values['conditions']['notlocation']['custom_categories'] ) ? $meta_values['conditions']['notlocation']['custom_categories'] : array();

				if ( empty( $cat_arr ) ) return false;

				$current_category = get_queried_object();
				$current_category_id = $current_category->term_id;

				foreach ( $cat_arr as $cat_id ) {

					if ( $current_category_id == $cat_id ) {

						return false;
					}
				}

				return true;

			} else if ( is_tag() && isset( $meta_values['conditions']['notlocation']['tags'] ) ) {

				$tag_arr = isset( $meta_values['conditions']['notlocation']['custom_tags'] ) ? $meta_values['conditions']['notlocation']['custom_tags'] : array();

				if ( empty( $tag_arr ) ) return false;

				$current_tag = get_queried_object();
				$current_tag_id = $current_tag->term_id;

				foreach ( $tag_arr as $tag_id ) {

					if ( $current_tag_id == $tag_id ) {

						return false;
					}
				}

				return true;

			} else if ( is_date() && isset( $meta_values['conditions']['notlocation']['date'] ) ) {

				return false;

			} else if ( is_author() && isset( $meta_values['conditions']['notlocation']['author'] ) ) {

				return false;

			} else if ( is_search() && isset( $meta_values['conditions']['notlocation']['search'] ) ) {

				return false;

			} else if ( is_404() && isset( $meta_values['conditions']['notlocation']['404'] ) ) {

				return false;

			} else if ( isset( $meta_values['conditions']['notlocation']['custom'] ) ) {

				global $wp;
				$current_url = home_url( add_query_arg( array(), $wp->request) );
				$custom_urls = isset( $meta_values['conditions']['notlocation']['data'] ) ? $meta_values['conditions']['notlocation']['data'] : array();

				if ( !empty( $custom_urls ) ) {

					foreach ( $custom_urls as $custom_url ) {

						if ( trailingslashit( $current_url ) === trailingslashit( $custom_url ) ) {

							return false;
						}
					}

					return true;
				}

			} else {

				$cpt_supp = get_option( 'mtsnb_supported_custom_post_types', array() );
				if ( !empty( $cpt_supp ) ) {

					if ( is_singular( $cpt_supp ) ) {

						foreach ( $cpt_supp as $cpt ) {

							if ( is_singular( $cpt ) && isset( $meta_values['conditions']['notlocation'][ $cpt ] ) ) {

								$c_posts_list = isset( $meta_values['conditions']['notlocation'][ 'custom_'.$cpt ] ) ? $meta_values['conditions']['notlocation'][ 'custom_'.$cpt ] : '';

								if ( empty( $c_posts_list ) ) return false;

								$c_posts_arr = explode( ',', $c_posts_list );

								if ( is_single( $c_posts_arr ) ) return false;

								return true;
							}
						}

					} else if ( is_tax() ) {

						$current_post_type = get_post_type();

						if ( in_array( $current_post_type, $cpt_supp ) ) {

							$queried_object = get_queried_object();
							$current_taxonomy = $queried_object->taxonomy;

							if ( isset( $meta_values['conditions']['notlocation'][ $current_taxonomy ] ) ) {

								$term_arr = isset( $meta_values['conditions']['notlocation'][ 'custom_'.$current_taxonomy ] ) ? $meta_values['conditions']['notlocation'][ 'custom_'.$current_taxonomy ] : array();

								if ( empty( $term_arr ) ) return false;

								$current_term_id = $queried_object->term_id;

								foreach ( $term_arr as $term_id ) {

									if ( $current_term_id == $term_id ) {

										return false;
									}
								}

								return true;
							}
						}
					}
				}

				return true;
			}
		}
	}

	/**
	 * Tests if bar can be displayed based on mobile settings
	 *
	 * @since  1.0.0
	 */
	public function test_mobile( $meta_values ) {

		$no_condition = (bool) ( empty( $meta_values['conditions']['onmobile']['state'] ) && empty( $meta_values['conditions']['notonmobile']['state'] ) );

		if ( $no_condition ) return true; // not set, can be displayed

		do_action( 'mtsnb_test_mobile', $meta_values );

		if ( wp_is_mobile() ) {

			if ( !empty( $meta_values['conditions']['onmobile']['state'] ) ) { return true; }
			if ( !empty( $meta_values['conditions']['notonmobile']['state'] ) ) { return false; }

		} else {

			if ( !empty( $meta_values['conditions']['onmobile']['state'] ) ) { return false; }
			if ( !empty( $meta_values['conditions']['notonmobile']['state'] ) ) { return true; }
		}

		return true;
	}

	/**
	 * Tests if bar can be displayed based on logged in settings
	 *
	 * @since  1.0.0
	 */
	public function test_logged( $meta_values ) {

		$no_condition = (bool) ( empty( $meta_values['conditions']['logged']['state'] ) && empty( $meta_values['conditions']['notlogged']['state'] ) );

		if ( $no_condition ) return true; // not set, can be displayed

		do_action( 'mtsnb_test_logged', $meta_values );

		if ( is_user_logged_in() ) {

			if ( !empty( $meta_values['conditions']['logged']['state'] ) ) { return true; }
			if ( !empty( $meta_values['conditions']['notlogged']['state'] ) ) { return false; }

		} else {

			if ( !empty( $meta_values['conditions']['logged']['state'] ) ) { return false; }
			if ( !empty( $meta_values['conditions']['notlogged']['state'] ) ) { return true; }
		}

		return true;
	}

	/**
	 * Tests if bar can be displayed based on less than settings
	 *
	 * @since  1.0.0
	 */
	public function test_less( $id, $meta_values ) {

		$no_condition = (bool) ( empty( $meta_values['conditions']['less']['state'] ) );

		if ( $no_condition ) return true; // not set, can be displayed

		$seen_times = isset( $_COOKIE['mtsnb_seen_'.$id] ) ? intval( $_COOKIE['mtsnb_seen_'.$id] ) : 0;
		$max_times  = ( isset( $meta_values['conditions']['less']['data'] ) && is_numeric( $meta_values['conditions']['less']['data'] ) && 0 < intval( $meta_values['conditions']['less']['data'] ) ) ? intval( $meta_values['conditions']['less']['data'] ) : 0;

		$after_times = isset( $_COOKIE['mtsnb_'.$id.'_after'] ) ? intval( $_COOKIE['mtsnb_'.$id.'_after'] ) : 0;
		$after_n_times = ( isset( $meta_values['conditions']['after']['data'] ) && is_numeric( $meta_values['conditions']['after']['data'] ) && 0 < intval( $meta_values['conditions']['after']['data'] ) ) ? intval( $meta_values['conditions']['after']['data'] ) : 0;

		if ( 0 < $after_times && 0 < $after_n_times ) {

			return false;
		}

		if ( 0 === $max_times || 0 === $seen_times ) {

			return true;
		}

		if ( $seen_times < $max_times ) {

			return true;

		} else {

			return false;
		}

		return true;
	}

	/**
	 * Tests if bar can be displayed based on "Show after N times" condition
	 *
	 * @since  1.0.0
	 */
	public function test_after( $id, $meta_values ) {

		$no_condition = (bool) ( empty( $meta_values['conditions']['after']['state'] ) );

		if ( $no_condition ) return true; // not set, can be displayed

		$after_times = isset( $_COOKIE['mtsnb_'.$id.'_after'] ) ? intval( $_COOKIE['mtsnb_'.$id.'_after'] ) : false;
		$after_n_times = ( isset( $meta_values['conditions']['after']['data'] ) && is_numeric( $meta_values['conditions']['after']['data'] ) && 0 < intval( $meta_values['conditions']['after']['data'] ) ) ? intval( $meta_values['conditions']['after']['data'] ) : 0;
		
		if ( 0 === $after_times && 0 < $after_n_times ) {

			return true;

		} else {

			return false;
		}
	}

	/**
	 * Tests if bar can be displayed based on UTM tags in settings
	 *
	 * @since  1.0.3
	 */
	public function test_utm( $meta_values ) {

		$no_condition = (bool) ( empty( $meta_values['conditions']['utm']['state'] ) && empty( $meta_values['conditions']['notutm']['state'] ) );

		if ( $no_condition ) return true; // not set, can be displayed

		// Show
		if ( !empty( $meta_values['conditions']['utm']['state'] ) ) {

			if ( isset( $meta_values['conditions']['utm']['tags'] ) ) {

				$utm_operator = isset( $meta_values['conditions']['utm']['operator'] ) ? $meta_values['conditions']['utm']['operator'] : 'or';

				foreach ( $meta_values['conditions']['utm']['tags'] as $tag ) {

					if ( 'or' === $utm_operator ) {

						if ( isset( $_GET[ $tag['name'] ] ) && !empty( $tag['value'] ) ) {

							if ( $_GET[ $tag['name'] ] === $tag['value'] ) {

								return true; // found
							}
						}

					} else {// all must match

						if ( !empty( $tag['value'] ) ) {

							if ( !isset( $_GET[ $tag['name'] ] ) ) {

								return false;// no such param
							}

							if ( $_GET[ $tag['name'] ] !== $tag['value'] ) {

								return false; // one failed to mach
							}
						}
					}
					
				}

				if ( 'and' === $utm_operator ) {

					return true; // all matched
				}
			}

			return false;
		}

		// Don't show
		if ( !empty( $meta_values['conditions']['notutm']['state'] ) ) {

			if ( isset( $meta_values['conditions']['notutm']['tags'] ) ) {

				$notutm_operator = isset( $meta_values['conditions']['notutm']['operator'] ) ? $meta_values['conditions']['notutm']['operator'] : 'or';

				foreach ( $meta_values['conditions']['notutm']['tags'] as $tag ) {

					if ( 'or' === $notutm_operator ) {

						if ( isset( $_GET[ $tag['name'] ] ) && !empty( $tag['value'] ) ) {

							if ( $_GET[ $tag['name'] ] === $tag['value'] ) {

								return false; // found
							}
						}

					} else {// all must match

						if ( !empty( $tag['value'] ) ) {

							if ( !isset( $_GET[ $tag['name'] ] ) ) {

								return true;// no such param
							}

							if ( $_GET[ $tag['name'] ] !== $tag['value'] ) {

								return true; // one failed to mach
							}
						}
					}
					
				}

				if ( 'and' === $notutm_operator ) {

					return false; // all matched
				}
			}

			return true;
		}
	}

	/**
	 * Tests if bar can be displayed based on date settings
	 *
	 * @since  1.1.5
	 */
	public function test_time( $meta_values ) {

		$condition_time_state    = isset( $meta_values['conditions'] ) && isset( $meta_values['conditions']['time'] ) && isset( $meta_values['conditions']['time']['state'] ) ? $meta_values['conditions']['time']['state'] : '';
		$condition_nottime_state = isset( $meta_values['conditions'] ) && isset( $meta_values['conditions']['nottime'] ) && isset( $meta_values['conditions']['nottime']['state'] ) ? $meta_values['conditions']['nottime']['state'] : '';

		$no_condition = (bool) ( empty( $condition_time_state ) && empty( $condition_nottime_state ) );

		do_action( 'mtsnb_test_time', $meta_values );

		if ( $no_condition ) return true; // not set, can be displayed

		$current_time = current_time('timestamp');

		if ( !empty( $condition_time_state ) ) {

			$show_from_time_enabled = isset( $meta_values['conditions']['show_from_time'] ) && isset( $meta_values['conditions']['show_from_time']['enabled'] ) ? $meta_values['conditions']['show_from_time']['enabled'] : '';
			$show_from_time_date    = isset( $meta_values['conditions']['show_from_time'] ) && isset( $meta_values['conditions']['show_from_time']['date'] ) ? $meta_values['conditions']['show_from_time']['date'] : '';
			$show_from_time_time    = isset( $meta_values['conditions']['show_from_time'] ) && isset( $meta_values['conditions']['show_from_time']['time'] ) ? $meta_values['conditions']['show_from_time']['time'] : '';
			$show_to_time_enabled = isset( $meta_values['conditions']['show_to_time'] ) && isset( $meta_values['conditions']['show_to_time']['enabled'] ) ? $meta_values['conditions']['show_to_time']['enabled'] : '';
			$show_to_time_date    = isset( $meta_values['conditions']['show_to_time'] ) && isset( $meta_values['conditions']['show_to_time']['date'] ) ? $meta_values['conditions']['show_to_time']['date'] : '';
			$show_to_time_time    = isset( $meta_values['conditions']['show_to_time'] ) && isset( $meta_values['conditions']['show_to_time']['time'] ) ? $meta_values['conditions']['show_to_time']['time'] : '';

			$show_from_time = !empty( $show_from_time_date ) ? strtotime( $show_from_time_date . ' ' . $show_from_time_time ) : '';
			$show_to_time   = !empty( $show_to_time_date ) ? strtotime( $show_to_time_date . ' ' . $show_to_time_time ) : '';

			if ( !empty( $show_from_time_enabled ) && !empty( $show_to_time_enabled ) ) {

				if ( empty( $show_from_time ) || empty( $show_to_time ) ) { return true; } // date(s) not set - show

				return ( $current_time > $show_from_time && $current_time < $show_to_time ); // show if current time is between start and end times, otherwise hide
			
			} elseif ( !empty( $show_from_time_enabled ) ) {

				if ( empty( $show_from_time ) ) { return true; } // date not set - show

				return ( $current_time > $show_from_time ); // show if current time is higher then start time, otherwise hide

			} elseif ( !empty( $show_to_time_enabled ) ) {

				if ( empty( $show_to_time ) ) { return true; } // date not set - show

				return ( $current_time < $show_to_time ); // show if current time is lesser then end time, otherwise hide
			}

			return true;
		}

		if ( !empty( $condition_nottime_state ) ) {

			$hide_from_time_enabled = isset( $meta_values['conditions']['hide_from_time'] ) && isset( $meta_values['conditions']['hide_from_time']['enabled'] ) ? $meta_values['conditions']['hide_from_time']['enabled'] : '';
			$hide_from_time_date    = isset( $meta_values['conditions']['hide_from_time'] ) && isset( $meta_values['conditions']['hide_from_time']['date'] ) ? $meta_values['conditions']['hide_from_time']['date'] : '';
			$hide_from_time_time    = isset( $meta_values['conditions']['hide_from_time'] ) && isset( $meta_values['conditions']['hide_from_time']['time'] ) ? $meta_values['conditions']['hide_from_time']['time'] : '';
			$hide_to_time_enabled = isset( $meta_values['conditions']['hide_to_time'] ) && isset( $meta_values['conditions']['hide_to_time']['enabled'] ) ? $meta_values['conditions']['hide_to_time']['enabled'] : '';
			$hide_to_time_date    = isset( $meta_values['conditions']['hide_to_time'] ) && isset( $meta_values['conditions']['hide_to_time']['date'] ) ? $meta_values['conditions']['hide_to_time']['date'] : '';
			$hide_to_time_time    = isset( $meta_values['conditions']['hide_to_time'] ) && isset( $meta_values['conditions']['hide_to_time']['time'] ) ? $meta_values['conditions']['hide_to_time']['time'] : '';

			$hide_from_time = !empty( $hide_from_time_date ) ? strtotime( $hide_from_time_date . ' ' . $hide_from_time_time ) : '';
			$hide_to_time   = !empty( $hide_to_time_date ) ? strtotime( $hide_to_time_date . ' ' . $hide_to_time_time ) : '';

			if ( !empty( $hide_from_time_enabled ) && !empty( $hide_to_time_enabled ) ) {

				if ( empty( $hide_from_time ) || empty( $hide_to_time ) ) { return false; } // date(s) not set - hide

				return !( $current_time > $hide_from_time && $current_time < $hide_to_time ); // hide if current time is between start and end times, otherwise show
			
			} elseif ( !empty( $hide_from_time_enabled ) ) {

				if ( empty( $hide_from_time ) ) { return false; } // date not set - hide

				return !( $current_time > $hide_from_time ); // hide if current time is higher then start time, otherwise show

			} elseif ( !empty( $hide_to_time_enabled ) ) {

				if ( empty( $hide_to_time ) ) { return false; } // date not set - hide

				return !( $current_time < $hide_to_time ); // hide if current time is lesser then end time, otherwise show
			}

			return false;
		}
	}

	/**
	 * Tests if the current referrer is a search engine.
	 *
	 * @since  1.0.0
	 */
	public function test_searchengine( $referrer ) {
		$response = false;

		$patterns = array(
			'/search?',
			'.google.',
			'web.info.com',
			'search.',
			'del.icio.us/search',
			'soso.com',
			'/search/',
			'.yahoo.',
			'.bing.',
		);

		foreach ( $patterns as $url ) {
			if ( false !== stripos( $referrer, $url ) ) {
				if ( $url == '.google.' ) {
					if ( $this->is_googlesearch( $referrer ) ) {
						$response = true;
					} else {
						$response = false;
					}
				} else {
					$response = true;
				}
				break;
			}
		}
		return $response;
	}

	/**
	 * Checks if the referrer is a google web-source.
	 *
	 * @since  1.0.0
	 */
	public function is_googlesearch( $referrer = '' ) {
		$response = true;

		// Get the query strings and check its a web source.
		$qs = parse_url( $referrer, PHP_URL_QUERY );
		$qget = array();

		foreach ( explode( '&', $qs ) as $keyval ) {
			$kv = explode( '=', $keyval );
			if ( count( $kv ) == 2 ) {
				$qget[ trim( $kv[0] ) ] = trim( $kv[1] );
			}
		}

		if ( isset( $qget['source'] ) ) {
			$response = $qget['source'] == 'web';
		}

		return $response;
	}

	/**
	 * Display hidden divs needed for "Show after N times" condition
	 *
	 * @since    1.0.3
	 */
	public function display_hidden_bars() {

		if ( get_option( 'mtsnb_show_after_data' ) !== false ) {

			$hidden_bars_data = get_option( 'mtsnb_show_after_data' );

			if ( !empty( $hidden_bars_data ) ) {

				$default_supported_post_types = array( 'post', 'page' );
				$cpt_supp = get_option( 'mtsnb_supported_custom_post_types', array() );
				$cpt_supp = !empty( $cpt_supp ) ? $cpt_supp : array();
				// Filter supported post types where user ca override bar on single view ( if user want to disable it on posts or pages for example )
				$force_bar_supported_post_types = apply_filters( 'mtsnb_force_bar_post_types', array_merge( $cpt_supp, $default_supported_post_types ) );

				if ( ( $key = array_search( 'mts_notification_bar', $force_bar_supported_post_types ) ) !== false ) {

					unset( $force_bar_supported_post_types[ $key ] );
				}

				if ( is_singular( $force_bar_supported_post_types ) ) {

					global $post;
					$bar = get_post_meta( $post->ID, '_mtsnb_override_bar', true );

					if ( $bar && !empty( $bar ) ) {

						$bar_id = isset( $bar[0] ) ? $bar[0] : false;

						if ( $bar_id && !empty( $bar_id ) && in_array( $bar_id, $hidden_bars_data ) ) {

							$meta_values = get_post_meta( $bar_id, '_mtsnb_data', true );
							$value = $hidden_bars_data[ $bar_id ];
							unset( $hidden_bars_data[ $bar_id ] );

							$passed_time_conditions     = $this->test_time( $meta_values );
							$passed_mobile_conditions   = $this->test_mobile( $meta_values );
							$passed_logged_conditions   = $this->test_logged( $meta_values );
							$passed_referrer_conditions = $this->test_referrer( $meta_values );
							$passed_utm_conditions      = $this->test_utm( $meta_values );
							$published                  = 'publish' === get_post_status( $bar_id );

							if ( $passed_time_conditions && $passed_mobile_conditions && $passed_logged_conditions && $passed_referrer_conditions && $passed_utm_conditions && $published ) {

								echo '<div class="mtsnb-delayed" data-mtsnb-id="'.$bar_id.'" data-mtsnb-after="'.$value.'"></div>';
							}
						}
					}
				}

				foreach ( $hidden_bars_data as $id => $value ) {

					$meta_values = get_post_meta( $id, '_mtsnb_data', true );

					$passed_location_conditions = $this->test_location( $meta_values );
					$passed_time_conditions     = $this->test_time( $meta_values );
					$passed_mobile_conditions   = $this->test_mobile( $meta_values );
					$passed_logged_conditions   = $this->test_logged( $meta_values );
					$passed_referrer_conditions = $this->test_referrer( $meta_values );
					$passed_utm_conditions      = $this->test_utm( $meta_values );
					$published                  = 'publish' === get_post_status ( $id );

					if ( $passed_location_conditions && $passed_time_conditions && $passed_mobile_conditions && $passed_logged_conditions && $passed_referrer_conditions && $passed_utm_conditions && $published ) {

						echo '<div class="mtsnb-delayed" data-mtsnb-id="'.$id.'" data-mtsnb-after="'.$value.'"></div>';
					}
				}

				wp_enqueue_script( $this->plugin_name );
			}
		}
	}

	/**
	 * Get referrer
	 *
	 * @since    1.0.3
	 */
	public function get_referrer() {

		$referer = wp_unslash( $_SERVER['HTTP_REFERER'] );

		if ( $referer && !empty( $referer ) ) {

			$secure = ( 'https' === parse_url( home_url(), PHP_URL_SCHEME ) );// maybe not needed
			setcookie( 'mtsnb_referrer', esc_url( $referer ), 0, COOKIEPATH, COOKIE_DOMAIN, $secure ); // session

		} else {

			if ( isset( $_COOKIE['mtsnb_referrer'] ) ) {

				// Stored referrer url
				$referer = $_COOKIE['mtsnb_referrer'];
			}
		}

		return $referer;
	}
}
