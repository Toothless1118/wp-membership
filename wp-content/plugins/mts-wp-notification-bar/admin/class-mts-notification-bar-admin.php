<?php

/**
 * The dashboard-specific functionality of the plugin.
 *
 * @link       http://mythemeshop.com
 * @since      1.0.0
 *
 * @package    MTSNB
 * @subpackage MTSNB/admin
 */

/**
 * The dashboard-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the dashboard-specific stylesheet and JavaScript.
 *
 * @package    MTSNB
 * @subpackage MTSNB/admin
 * @author     Your Name <email@example.com>
 */
class MTSNB_Admin {

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
	 * Slug of the plugin screen.
	 *
	 * @since 1.0.0
	 *
	 * @var   string
	 */
	private $plugin_screen_hook_suffix = null;

	private $supported_custom_post_types;
	private $supported_custom_taxonomies;

	private $post_tag_select_options;
	private $category_select_options;
	private $page_select_options;
	private $custom_taxonomies_select_options;

	private $force_bar_post_types;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 1.0.0
	 * @param string    $plugin_name       The name of this plugin.
	 * @param string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the Dashboard.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_styles() {

		$screen = get_current_screen();
		$screen_id = $screen->id;

		$force_bar_post_types = $this->force_bar_post_types;

		if ( 'mts_notification_bar' === $screen_id || in_array( $screen_id, $force_bar_post_types ) ) {

			if ( 'mts_notification_bar' === $screen_id ) {

				wp_enqueue_style( 'wp-color-picker' );
				wp_enqueue_style( 'mtsnb-date-time-picker', plugin_dir_url( __FILE__ ) . '/css/mts-datepicker.css', array(), $this->version, 'all' );
			}

			wp_enqueue_style( 'select2', plugin_dir_url( __FILE__ ) . 'css/select2.min.css', array(), $this->version, 'all' );
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/mts-notification-bar-admin.css', array(), $this->version, 'all' );
		}
	}

	/**
	 * Register the JavaScript for the dashboard.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {

		$screen = get_current_screen();
		$screen_id = $screen->id;

		$force_bar_post_types = $this->force_bar_post_types;

		if ( 'mts_notification_bar' === $screen_id || in_array( $screen_id, $force_bar_post_types ) ) {

			if ( 'mts_notification_bar' === $screen_id ) {

				wp_enqueue_script( 'wp-color-picker' );

				wp_enqueue_script( 'jquery-ui-core' );
				wp_enqueue_script( 'jquery-ui-sortable' );

				wp_enqueue_script( 'jquery-ui-slider' );

				wp_enqueue_script(
					'bootstrap-tagsinput',
					plugin_dir_url( __FILE__ ) . 'js/bootstrap-tagsinput.min.js',
					array(
						'jquery',
					),
					$this->version, false
				);

				wp_enqueue_script(
					'mtsnb-time-picker',
					plugin_dir_url( __FILE__ ) . '/js/jquery-ui-timepicker-addon.js',
					array(
						'jquery',
						'jquery-ui-core',
						'jquery-ui-datepicker',
						'jquery-ui-slider'
					),
					$this->version, false
				);

				wp_enqueue_script(
					'select2',
					plugin_dir_url( __FILE__ ) . 'js/select2.full.min.js',
					array(
						'jquery',
					),
					$this->version, false
				);

				wp_enqueue_script(
					$this->plugin_name,
					plugin_dir_url( __FILE__ ) . 'js/mts-notification-bar-admin.js',
					array(
						'jquery',
						'select2',
						'wp-color-picker',
						'bootstrap-tagsinput',
						'jquery-ui-sortable'
					),
					$this->version, false
				);
				wp_localize_script(
					$this->plugin_name,
					'adminVars',
					array(
						'social_icons' => $this->get_social_icons(),
						'remove' => __( 'Remove', $this->plugin_name ),
						'enable_test' => __( 'Enable Split Test', $this->plugin_name ),
						'disable_test' => __( 'Disable Split Test', $this->plugin_name ),
						'select_placeholder' => __( 'Enter Notification Bar Title', $this->plugin_name ),
					)
				);

			} else {// Override notification bar select

				wp_enqueue_script(
					'select2',
					plugin_dir_url( __FILE__ ) . 'js/select2.full.min.js',
					array(
						'jquery',
					),
					$this->version, false
				);

				wp_enqueue_script(
					$this->plugin_name.'select',
					plugin_dir_url( __FILE__ ) . 'js/mts-notification-bar-select.js',
					array(
						'jquery',
						'select2',
					),
					$this->version, false
				);
				wp_localize_script(
					$this->plugin_name.'select',
					'mtsnb_locale',
					array(
						'select_placeholder' => __( 'Enter Notification Bar Title', $this->plugin_name ),
					)
				);
			}
		}
	}


	//////////////////////
	////// Settings //////
	//////////////////////

	/**
	 * Register the administration menu, attached to 'admin_menu'
	 *
	 * @since 1.0.0
	 */
	public function plugin_admin_menu() {

		add_submenu_page(
			'edit.php?post_type=mts_notification_bar',
			__( 'WP Notification Bar Settings', $this->plugin_name ),
			__( 'Settings', $this->plugin_name ),
			'manage_options',
			$this->plugin_name,
			array( $this, 'display_plugin_admin_page' )
		);
	}

	/**
	 * Render the settings page.
	 *
	 * @since 1.0.0
	 */
	public function display_plugin_admin_page() {
	?>
		<div class="wrap">
			<?php screen_icon(); ?>
			<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
			<form method="post" action="options.php">
				<?php settings_fields( $this->plugin_name ); ?>
				<?php do_settings_sections( $this->plugin_name ); ?>
				<?php submit_button(); ?>
			</form>
		</div>
	<?php
	}

	/**
	 * Creates our settings sections with fields etc.
	 *
	 * @since    1.0.3
	 */
	public function settings_api_init() {

		$args = array(
			'public' => true,
			'publicly_queryable'=> true,
			'_builtin'=> false,
		);
		$custom_post_types = get_post_types( $args, 'objects' );

		foreach ( $custom_post_types as $post_type ) {

			$this->supported_custom_post_types[ $post_type->name ] = $post_type->labels->name;
		}

		// Add a new setting to the options table
		register_setting( $this->plugin_name, 'mtsnb_supported_custom_post_types' );
		// Add general section
		add_settings_section(
			'general_settings_section',
			__( 'Custom Post Types', $this->plugin_name ),
			array( $this, 'general_settings_section_callback' ),
			$this->plugin_name
		);

		// Add general section fields
	 	add_settings_field(
			'mtsnb_supported_custom_post_types',
			__( 'Supported Post Types:', $this->plugin_name ),
			array( $this, 'options_cpt_multiselect_callback' ),
			$this->plugin_name,
			'general_settings_section'
		);

		// Add a new setting to the options table
		register_setting( $this->plugin_name, 'notification_bar_cookies_expiry' );
		// Add the Cookie settings section to our plugins settings page
		add_settings_section(
			'cookies_settings_section',
			__( 'Cookies settings', $this->plugin_name ),
			array( $this, 'cookies_section_callback_function' ),
			$this->plugin_name
		);

		// Add the fields
	 	add_settings_field(
			'notification_bar_cookies_expiry',
			__('Cookies expiry time (days):', $this->plugin_name),
			array($this, 'cookies_callback_function'),
			$this->plugin_name,
			'cookies_settings_section'
		);
	}

	// callbacks
	public function general_settings_section_callback(){
		?>
		<p><?php _e('Add support for Custom Post Type single pages and its taxonomies archive pages.', $this->plugin_name ); ?></p>
		<?php
	}
	public function cookies_section_callback_function(){
		?>
		<p><?php printf( __( 'Cookies are used to make "Show N times" and "Show after N visits" display conditions possible.<br />If you want to reset cookies, you can do that on <a href="%s">Notification Bars</a> page using "Bulk Actions" select dropdown.', $this->plugin_name ), esc_url( admin_url( 'edit.php?post_type=mts_notification_bar' ) ) ); ?></p>
		<?php
	}
	public function cookies_callback_function(){
		$expiry = ( false !== get_option('notification_bar_cookies_expiry') ) ? get_option('notification_bar_cookies_expiry') : 365;
		?>
		<input type="number" step="1" min="1" name="notification_bar_cookies_expiry" id="notification_bar_cookies_expiry" value="<?php echo $expiry;?>" class="small-text"/>
		<?php
	}
	/**
	 * Multi select option callback function.
	 *
	 * @since 1.0.3
	 */
	public function options_cpt_multiselect_callback( $args ) {

		$opt_val = get_option( 'mtsnb_supported_custom_post_types', array() );
		$opt_val = !empty( $opt_val ) ? $opt_val : array()
		?>
		<select multiple name="mtsnb_supported_custom_post_types[]" id="mtsnb_supported_custom_post_types">
			<?php
			if ( !empty( $this->supported_custom_post_types ) ) {
				foreach ( $this->supported_custom_post_types as $id => $name ) {

					$selected =  in_array( $id, $opt_val ) ? ' selected="selected"' : '';
					?>
					<option value="<?php echo esc_attr( $id );?>"<?php echo $selected; ?>><?php echo esc_html( $name ); ?></option>
					<?php
				}
			}
			?>
		</select>
		<?php
	}

	//////////////////////
	//////// CPT /////////
	//////////////////////

	/**
	 * Register MTS Notification Bar Post Type, attached to 'init'
	 *
	 * @since    1.0.0
	 */
	public function mts_notification_cpt() {
		$labels = array(
			'name'               => _x( 'Notification Bars', 'post type general name', $this->plugin_name ),
			'singular_name'      => _x( 'Notification Bar', 'post type singular name', $this->plugin_name ),
			'menu_name'          => _x( 'Notification Bars', 'admin menu', $this->plugin_name ),
			'name_admin_bar'     => _x( 'Notification Bar', 'add new on admin bar', $this->plugin_name ),
			'add_new'            => _x( 'Add New', 'notification bar', $this->plugin_name ),
			'add_new_item'       => __( 'Add New Notification Bar', $this->plugin_name ),
			'new_item'           => __( 'New Notification Bar', $this->plugin_name ),
			'edit_item'          => __( 'Edit Notification Bar', $this->plugin_name ),
			'view_item'          => __( 'View Notification Bar', $this->plugin_name ),
			'all_items'          => __( 'All Notification Bars', $this->plugin_name ),
			'search_items'       => __( 'Search Notification Bars', $this->plugin_name ),
			'parent_item_colon'  => __( 'Parent Notification Bars:', $this->plugin_name ),
			'not_found'          => __( 'No notification bars found.', $this->plugin_name ),
			'not_found_in_trash' => __( 'No notification bars found in Trash.', $this->plugin_name )
		);

		$args = array(
			'labels' => $labels,
			'public' => false,
			'show_ui' => true,
			'capability_type' => 'post',
			'hierarchical' => false,
			'rewrite' => false,
			'publicly_queryable' => false,
			'menu_position' => 100,
			'menu_icon' => 'dashicons-info',
			'has_archive' => false,
			'supports' => array('title')
		);

		register_post_type( 'mts_notification_bar' , $args );

		
		$default_supported_post_types = array( 'post', 'page' );
		$cpt_supp = get_option( 'mtsnb_supported_custom_post_types', array() );
		$cpt_supp = !empty( $cpt_supp ) ? $cpt_supp : array();
		// Filter supported post types where user can override bar on single view ( if user want to disable it on posts or pages for example )
		$force_bar_supported_post_types = apply_filters( 'mtsnb_force_bar_post_types', array_merge( $cpt_supp, $default_supported_post_types ) );

		if ( ( $key = array_search( 'mts_notification_bar', $force_bar_supported_post_types ) ) !== false ) {

			unset( $force_bar_supported_post_types[ $key ] );
		}

		$this->force_bar_post_types = $force_bar_supported_post_types;
	}

	/**
	 * Add preview button to edit bar page.
	 *
	 * @since    1.0.0
	 */
	public function add_preview_button() {
		global $post;
		if ( 'mts_notification_bar' === $post->post_type ) {
			echo '<div class="misc-pub-section">';
				echo '<a href="#" class="button" id="preview-bar"><i class="dashicons dashicons-visibility"></i> '.__( 'Preview Bar', $this->plugin_name ).'</a>';
			echo '</div>';
		}
	}

	/**
	 * Add the Meta Box
	 *
	 * @since    1.0.0
	 */
	public function add_custom_meta_box() {
	    
        add_meta_box(
            'custom_meta_box',
            __( 'Settings', $this->plugin_name ),
            array( $this, 'show_custom_meta_box' ),
            'mts_notification_bar',
            'normal',
            'high'
        );
	}

	/**
	 * The Callback, Meta Box Content
	 *
	 * @since    1.0.0
	 */
	public function show_custom_meta_box( $post ) {

		$general_options = array(
			array(
				'type' => 'select',
				'name' => 'button',
				'label' => __( 'Hide/Close Button', $this->plugin_name ),
				'default'=> 'no_button',
				'options' => array(
					'no_button' => __( 'No Button', $this->plugin_name ),
					'toggle_button' => __( 'Toggle Button', $this->plugin_name ),
					'close_button' => __( 'Close Button', $this->plugin_name ),
				),
				'class' => 'mtsnb-has-child-opt'
			),
			array(
				'type' => 'select_icon',
				'name' => 'close_icon',
				'label' => __( 'Close icon', $this->plugin_name ),
				'default'=> '',
				'options' => $this->get_awesome_icons(),
				'parent_select' => 'button',
				'parent_value' => 'toggle_button,close_button'
			),
			array(
				'type' => 'select_icon',
				'name' => 'show_icon',
				'label' => __( 'Open icon', $this->plugin_name ),
				'default'=> '',
				'options' => $this->get_awesome_icons(),
				'parent_select' => 'button',
				'parent_value' => 'toggle_button'
			),
			array(
				'type' => 'select',
				'name' => 'initial_state',
				'label' => __( 'Initial State', $this->plugin_name ),
				'options' => array(
					'opened' => __( 'Opened', $this->plugin_name ),
					'closed' => __( 'Closed', $this->plugin_name ),
				),
				'default'=> 'opened',
				'parent_select' => 'button',
				'parent_value' => 'toggle_button'
			),
			array(
				'type' => 'checkbox',
				'name' => 'remember_state',
				'label' => __( 'Remember State', $this->plugin_name ),
				'default'=> '0',
				'parent_select' => 'button',
				'parent_value' => 'toggle_button,close_button'
			),
			array(
				'type' => 'number',
				'name' => 'content_width',
				'label' => __( 'Content Width (px)', $this->plugin_name ),
				'default'=> '960'
			),
			array(
				'type' => 'number',
				'name' => 'padding',
				'label' => __( 'Padding (px)', $this->plugin_name ),
				'default'=> '10'
			),
			array(
				'type' => 'select',
				'name' => 'css_position',
				'label' => __( 'Notification bar CSS position', $this->plugin_name ),
				'default'=> 'fixed',
				'options' => array(
					'fixed' => __( 'Fixed', $this->plugin_name ),
					'absolute' => __( 'Absolute', $this->plugin_name ),
				)
			),
			array(
				'type' => 'select',
				'name' => 'screen_position',
				'label' => __( 'Notification bar position', $this->plugin_name ),
				'default'=> 'top',
				'options' => array(
					'top' => __( 'Top', $this->plugin_name ),
					'bottom' => __( 'Bottom', $this->plugin_name ),
				)
			),
			array(
				'type' => 'select',
				'name' => 'animation',
				'label' => __( 'Notification bar animation', $this->plugin_name ),
				'default'=> '',
				'options' => $this->get_animations(),
			),
			array(
				'type' => 'select',
				'name' => 'content_animation',
				'label' => __( 'Notification bar content animation', $this->plugin_name ),
				'default'=> '',
				'options' => $this->get_animations(),
			),
		);

		$style_options = array(
			array( 'type' => 'color', 'name' => 'bg_color', 'label' => __( 'Background Color', $this->plugin_name ), 'default'=> '#3071A9' ),
			array( 'type' => 'color', 'name' => 'txt_color', 'label' => __( 'Text Color', $this->plugin_name ), 'default'=> '#ffffff' ),
			array( 'type' => 'color', 'name' => 'link_color', 'label' => __( 'Link Color', $this->plugin_name ), 'default'=> '#dedede' ),
			array( 'type' => 'color', 'name' => 'link_hover_color', 'label' => __( 'Link Hover Color', $this->plugin_name ), 'default'=> '#ffffff' ),
			array( 'type' => 'color', 'name' => 'button_color', 'label' => __( 'Close/Toggle Button Color', $this->plugin_name ), 'default'=> '#ffffff' ),
			array( 'type' => 'color', 'name' => 'button_bg_color', 'label' => __( 'Close/Toggle Button Background Color', $this->plugin_name ), 'default'=> '#3071A9' ),
			array(
				'type' => 'number',
				'name' => 'font_size',
				'label' => __( 'Font size (px)', $this->plugin_name ),
				'default'=> '15'
			),
			array(
				'type' => 'select',
				'name' => 'font_weight',
				'label' => __( 'Font Weight', $this->plugin_name ),
				'default'=> 'bold',
				'options' => array(
					'normal' => __( 'Normal', $this->plugin_name ),
					'bold' => __( 'Bold', $this->plugin_name ),
					'bolder' => __( 'Bolder', $this->plugin_name ),
					'lighter' => __( 'Lighter', $this->plugin_name ),
				)
			),
			array(
				'type' => 'text',
				'name' => 'line_height',
				'label' => __( 'Line Height', $this->plugin_name ),
				'default'=> '1.4'
			),
			array(
				'type' => 'checkbox',
				'name' => 'shadow',
				'label' => __( 'Drop Shadow', $this->plugin_name ),
				'default'=> '0',
			),
		);

		$button_content_type_options = array(
			array(
				'type' => 'select',
				'name' => 'basic_link_style',
				'label' => __( 'Link Style', $this->plugin_name ),
				'default'=> 'link',
				'options' => array(
					'link' => __( 'Link', $this->plugin_name ),
					'button' => __( 'Button', $this->plugin_name ),
				)
			),
			array(
				'type' => 'text',
				'name' => 'basic_text',
				'label' => __( 'Text', $this->plugin_name ),
				'default'=> ''
			),
			array(
				'type' => 'text',
				'name' => 'basic_link_text',
				'label' => __( 'Link/Button Text', $this->plugin_name ),
				'default'=> ''
			),
			array(
				'type' => 'text',
				'name' => 'basic_link_url',
				'label' => __( 'Link/Button Url', $this->plugin_name ),
				'default'=> ''
			),
		);

		$posts_content_type_options = array(
			array(
				'type' => 'text',
				'name' => 'posts_text',
				'label' => __( 'Text', $this->plugin_name ),
				'default'=> ''
			),
			array(
				'type' => 'number',
				'name' => 'posts_number',
				'label' => __( 'Number of posts', $this->plugin_name ),
				'default'=> '5'
			),
			array(
				'type' => 'checkbox',
				'name' => 'post_thumb',
				'label' => __( 'Display Post Thumbnail', $this->plugin_name ),
				'default'=> '0',
			),
			array(
				'type' => 'select',
				'name' => 'posts_to_show',
				'label' => __( 'Show', $this->plugin_name ),
				'default'=> 'recent',
				'options' => array(
					'recent' => __( 'Recent', $this->plugin_name ),
					'related' => __( 'Related', $this->plugin_name ),
					'custom_posts' => __( 'Custom Posts', $this->plugin_name ),
					'custom_categories' => __( 'Custom Categories', $this->plugin_name ),
					'custom_tags' => __( 'Custom Tags', $this->plugin_name ),
				),
				'class' => 'mtsnb-has-child-opt'
			),
			array(
				'type' => 'text',
				'name' => 'custom_posts',
				'label' => __( 'Enter comma separated list of post ids', $this->plugin_name ),
				'default'=> '',
				'parent_select' => 'posts_to_show',
				'parent_value' => 'custom_posts'
			),
			array(
				'type' => 'select_adv',
				'name' => 'custom_categories',
				'label' => __( 'Select Post Categories', $this->plugin_name ),
				'default'=> array(),
				'options' => $this->category_select_options,
				'parent_select' => 'posts_to_show',
				'parent_value' => 'custom_categories'
			),
			array(
				'type' => 'select_adv',
				'name' => 'custom_tags',
				'label' => __( 'Select Post Tags', $this->plugin_name ),
				'default'=> array(),
				'options' => $this->post_tag_select_options,
				'parent_select' => 'posts_to_show',
				'parent_value' => 'custom_tags'
			),
		);

		$newsletter_content_type_options = array(
			array(
				'type' => 'text',
				'name' => 'newsletter_text',
				'label' => __( 'Text', $this->plugin_name ),
				'default'=> ''
			),
			array(
				'type' => 'select',
				'name' => 'newsletter_type',
				'label' => __( 'Service', $this->plugin_name ),
				'default'=> 'fb',
				'options' => array(
					'fb' => __( 'Feedburner', $this->plugin_name ),
					'MailChimp' => __( 'MailChimp', $this->plugin_name ),
					'aweber' => __( 'Aweber', $this->plugin_name ),
					'getresponse' => __( 'Get Response', $this->plugin_name ),
					'campaignmonitor' => __( 'Campaign Monitor', $this->plugin_name ),
					'madmimi' => __( 'Mad Mimi', $this->plugin_name ),
				),
				'class' => 'mtsnb-has-child-opt'
			),
			array(
				'type' => 'checkbox',
				'name' => 'newsletter_name',
				'label' => __( 'Include Name Field', $this->plugin_name ),
				'default'=> '0',
				'parent_select' => 'newsletter_type',
				'parent_value' => array('MailChimp','aweber','getresponse','campaignmonitor','madmimi')
			),
			array(
				'type' => 'checkbox',
				'name' => 'newsletter_lastname',
				'label' => __( 'Include Last Name Field', $this->plugin_name ),
				'default'=> '0',
				'parent_select' => 'newsletter_type',
				'parent_value' => array('MailChimp','aweber','getresponse','campaignmonitor','madmimi')
			),
			//MailChimp
			array(
				'type' => 'text',
				'name' => 'MailChimp_api_key',
				'label' => __( 'MailChimp <a href="http://kb.mailchimp.com/accounts/management/about-api-keys" target="_blank">API Key</a>', $this->plugin_name ),
				'default'=> '',
				'parent_select' => 'newsletter_type',
				'parent_value' => 'MailChimp'
			),
			array(
				'type' => 'ajax_list',
				'name' => 'MailChimp_list',
				'label' => __( 'MailChimp List ID', $this->plugin_name ),
				'default'=> '',
				'parent_select' => 'newsletter_type',
				'parent_value' => 'MailChimp'
			),
			array(
				'type' => 'checkbox',
				'name' => 'MailChimp_single_optin',
				'label' => __( 'Single Optin', $this->plugin_name ),
				'default'=> '0',
				'parent_select' => 'newsletter_type',
				'parent_value' => 'MailChimp'
			),
			//Aweber
			array(
				'type' => 'aweber_actions',
				'name' => 'aweber_data',
				'label' => '',
				'default'=> array(),
				'parent_select' => 'newsletter_type',
				'parent_value' => 'aweber'
			),
			array(
				'type' => 'text',
				'name' => 'aweber_code',
				'label' => __( 'Paste Authorization Code', $this->plugin_name ),
				'default'=> '',
				'parent_select' => 'newsletter_type',
				'parent_value' => 'aweber'
			),
			array(
				'type' => 'ajax_list',
				'name' => 'aweber_list',
				'label' => __( 'Aweber List', $this->plugin_name ),
				'default'=> '',
				'parent_select' => 'newsletter_type',
				'parent_value' => 'aweber'
			),
			//Get Response
			array(
				'type' => 'text',
				'name' => 'getresponse_api_key',
				'label' => __( 'Get Response <a href="http://www.getresponse.com/learning-center/glossary/api-key.html" target="_blank">API Key</a>', $this->plugin_name ),
				'default'=> '',
				'parent_select' => 'newsletter_type',
				'parent_value' => 'getresponse'
			),
			array(
				'type' => 'ajax_list',
				'name' => 'getresponse_campaign',
				'label' => __( 'Get Response Campaign', $this->plugin_name ),
				'default'=> '',
				'parent_select' => 'newsletter_type',
				'parent_value' => 'getresponse'
			),
			// Campaign Monitor
			array(
				'type' => 'text',
				'name' => 'campaignmonitor_api_key',
				'label' => __( 'Campaign Monitor <a href="http://help.campaignmonitor.com/topic.aspx?t=206" target="_blank">API Key</a>', $this->plugin_name ),
				'default'=> '',
				'parent_select' => 'newsletter_type',
				'parent_value' => 'campaignmonitor'
			),
			array(
				'type' => 'ajax_client',
				'name' => 'campaignmonitor_client',
				'label' => __( 'Campaign Monitor Client', $this->plugin_name ),
				'default'=> '',
				'parent_select' => 'newsletter_type',
				'parent_value' => 'campaignmonitor'
			),
			array(
				'type' => 'ajax_list',
				'name' => 'campaignmonitor_list',
				'label' => __( 'Campaign Monitor List', $this->plugin_name ),
				'default'=> '',
				'parent_select' => 'newsletter_type',
				'parent_value' => 'campaignmonitor'
			),
			//Mad Mimi
			array(
				'type' => 'text',
				'name' => 'madmimi_username',
				'label' => __( 'Mad Mimi Username / Email', $this->plugin_name ),
				'default'=> '',
				'parent_select' => 'newsletter_type',
				'parent_value' => 'madmimi'
			),
			array(
				'type' => 'text',
				'name' => 'madmimi_api_key',
				'label' => __( 'Mad Mimi <a href="http://help.madmimi.com/where-can-i-find-my-api-key/" target="_blank">API Key</a>', $this->plugin_name ),
				'default'=> '',
				'parent_select' => 'newsletter_type',
				'parent_value' => 'madmimi'
			),
			array(
				'type' => 'ajax_list',
				'name' => 'madmimi_list',
				'label' => __( 'Mad Mimi List', $this->plugin_name ),
				'default'=> '',
				'parent_select' => 'newsletter_type',
				'parent_value' => 'madmimi'
			),
			// Feedburner
			array(
				'type' => 'text',
				'name' => 'newsletter_fb_id',
				'label' => __( 'Feedburner <a href="http://rosalindgardner.com/blog/how-to-find-your-feedburner-id/" target="_blank">ID</a>', $this->plugin_name ),
				'default'=> '',
				'parent_select' => 'newsletter_type',
				'parent_value' => 'fb'
			),

			array(
				'type' => 'text',
				'name' => 'newsletter_btn_text',
				'label' => __( 'Submit button text', $this->plugin_name ),
				'default'=> __( 'Subscribe', $this->plugin_name ),
				'parent_select' => 'newsletter_type',
				'parent_value' => array('MailChimp','aweber','getresponse','campaignmonitor','madmimi','fb')
			),
			array(
				'type' => 'text',
				'name' => 'newsletter_success_text',
				'label' => __( 'Success message', $this->plugin_name ),
				'default'=> __( 'We will be in touch soon!', $this->plugin_name ),
				'parent_select' => 'newsletter_type',
				'parent_value' => array('MailChimp','aweber','getresponse','campaignmonitor','madmimi')
			),
		);

		$social_content_type_options = array(
			array(
				'type' => 'text',
				'name' => 'social_text',
				'label' => __( 'Text', $this->plugin_name ),
				'default'=> ''
			),
		);

		$twitter_content_type_options = array(
			array(
				'type' => 'text',
				'name' => 'twitts_text',
				'label' => __( 'Text', $this->plugin_name ),
				'default'=> ''
			),
			array(
				'type' => 'number',
				'name' => 'twitts_number',
				'label' => __( 'Number of Tweets', $this->plugin_name ),
				'default'=> '5'
			),
			array(
				'type' => 'info',
				'default' => __( 'Find out how to setup your App <a href="http://iag.me/socialmedia/how-to-create-a-twitter-app-in-8-easy-steps/" target="_blank">here</a> and enter your App details below.', $this->plugin_name ),
			),
			array(
				'type' => 'text',
				'name' => 'twitter_api_key',
				'label' => __( 'API Key', $this->plugin_name ),
				'default'=> ''
			),
			array(
				'type' => 'text',
				'name' => 'twitter_api_secret',
				'label' => __( 'API Secret', $this->plugin_name ),
				'default'=> ''
			),
			array(
				'type' => 'text',
				'name' => 'twitter_token',
				'label' => __( 'Token', $this->plugin_name ),
				'default'=> ''
			),
			array(
				'type' => 'text',
				'name' => 'twitter_token_secret',
				'label' => __( 'Token Secret', $this->plugin_name ),
				'default'=> ''
			),
		);

		$fbfeed_content_type_options = array(
			array(
				'type' => 'text',
				'name' => 'fb_text',
				'label' => __( 'Text', $this->plugin_name ),
				'default'=> ''
			),
			array(
				'type' => 'text',
				'name' => 'fb_page_id',
				'label' => __( 'Facebook <a href="http://findmyfacebookid.com/" target="_blank">Page ID</a>', $this->plugin_name ),
				'default'=> ''
			),
			array(
				'type' => 'number',
				'name' => 'fb_posts_number',
				'label' => __( 'Number of posts to display', $this->plugin_name ),
				'default'=> '5'
			),
			array(
				'type' => 'info',
				'default' => __( 'Find out how to setup your App <a href="https://getsharepress.com/setup" target="_blank">here</a> and enter your <a href="https://developers.facebook.com/apps/" target="_blank">App ID and Secret</a> below.', $this->plugin_name ),
			),
			array(
				'type' => 'text',
				'name' => 'fb_app_id',
				'label' => __( 'Facebook App ID', $this->plugin_name ),
				'default'=> ''
			),
			array(
				'type' => 'text',
				'name' => 'fb_app_secret',
				'label' => __( 'Facebook App Secret', $this->plugin_name ),
				'default'=> ''
			),
		);

		$counter_content_type_options = array(
			array(
				'type' => 'text',
				'name' => 'counter_text',
				'label' => __( 'Text', $this->plugin_name ),
				'default'=> ''
			),
			array(
				'type' => 'date',
				'name' => 'counter_date',
				'label' => __( 'Count Until Date', $this->plugin_name ),
				'default'=> '',
			),
			array(
				'type' => 'time',
				'name' => 'counter_time',
				'label' => __( 'Count Until Time', $this->plugin_name ),
				'default'=> '00:00',
			),
			array(
				'type' => 'text',
				'name' => 'counter_btn_text',
				'label' => __( 'Call To Action Button Text', $this->plugin_name ),
				'default'=> ''
			),
			array(
				'type' => 'text',
				'name' => 'counter_btn_url',
				'label' => __( 'Call To Action Button Url', $this->plugin_name ),
				'default'=> ''
			),
		);

		$popup_content_type_options = array(
			array(
				'type' => 'text',
				'name' => 'popup_text',
				'label' => __( 'Text', $this->plugin_name ),
				'default'=> ''
			),
			array(
				'type' => 'select',
				'name' => 'popup_video_type',
				'label' => __( 'Video Service', $this->plugin_name ),
				'default'=> 'youtube',
				'options' => array(
					'youtube' => __( 'YouTube', $this->plugin_name ),
					'vimeo'   => __( 'Vimeo', $this->plugin_name ),
				),
				'class' => 'mtsnb-has-child-opt'
			),
			array(
				'type' => 'text',
				'name' => 'popup_youtube_link',
				'label' => __( 'Link to YouTube video', $this->plugin_name ),
				'default'=> '',
				'parent_select' => 'popup_video_type',
				'parent_value' => 'youtube'
			),
			array(
				'type' => 'text',
				'name' => 'popup_vimeo_link',
				'label' => __( 'Link to Vimeo video', $this->plugin_name ),
				'default'=> '',
				'parent_select' => 'popup_video_type',
				'parent_value' => 'vimeo'
			),
		);

		$search_content_type_options = array(
			array(
				'type' => 'text',
				'name' => 'search_text',
				'label' => __( 'Text', $this->plugin_name ),
				'default'=> __( 'Looking for something?', $this->plugin_name ),
			),
		);

		$custom_content_type_options = array(
			array(
				'type' => 'textarea',
				'name' => 'custom_content',
				'label' => __( 'Add custom content, shortcodes allowed', $this->plugin_name ),
				'default'=> ''
			),
		);

		// Add an nonce field so we can check for it later.
		wp_nonce_field('mtsnb_meta_box', 'mtsnb_meta_box_nonce');
		// Use get_post_meta to retrieve an existing value from the database.
		$value = get_post_meta( $post->ID, '_mtsnb_data', true );//var_dump($value);
		?>
		<div class="mtsnb-tabs clearfix">
			<div class="mtsnb-tabs-inner clearfix">
				<?php $active_tab = ( isset( $value['active_tab'] ) && !empty( $value['active_tab'] ) ) ? $value['active_tab'] : 'general'; ?>
				<input type="hidden" class="mtsnb-tab-option" name="mtsnb_fields[active_tab]" id="mtsnb_fields_active_tab" value="<?php echo $active_tab; ?>" />
				<ul class="mtsnb-tabs-nav" id="main-tabs-nav">
					<li>
						<a href="#tab-general" <?php if ( $active_tab === 'general' ) echo 'class="active"'; ?>>
							<span class="mtsnb-tab-title"><i class="dashicons dashicons-admin-generic"></i><?php _e( 'General', $this->plugin_name ); ?></span>
						</a>
					</li>
					<li>
						<a href="#tab-type" <?php if ( $active_tab === 'type' ) echo 'class="active"'; ?>>
							<span class="mtsnb-tab-title"><i class="dashicons dashicons-edit"></i><?php _e( 'Content', $this->plugin_name ); ?></span>
						</a>
					</li>
					<li>
						<a href="#tab-style" <?php if ( $active_tab === 'style' ) echo 'class="active"'; ?>>
							<span class="mtsnb-tab-title"><i class="dashicons dashicons-admin-appearance"></i><?php _e( 'Style', $this->plugin_name ); ?></span>
						</a>
					</li>
					<li>
						<a href="#tab-conditions" <?php if ( $active_tab === 'conditions' ) echo 'class="active"'; ?>>
							<span class="mtsnb-tab-title"><i class="dashicons dashicons-admin-settings"></i><?php _e( 'Conditions', $this->plugin_name ); ?></span>
						</a>
					</li>
				</ul>
				<div class="mtsnb-tabs-wrap" id="main-tabs-wrap">
					<div id="tab-general" class="mtsnb-tabs-content <?php if ( $active_tab === 'general' ) echo 'active'; ?>">
						<div class="mtsnb-tab-desc"><?php _e( 'Select basic settings like close button type and CSS position of the bar.', $this->plugin_name ); ?></div>
						<div class="mtsnb-tab-options clearfix">
							<?php
							foreach ( $general_options as $option_args ) {
								$this->custom_meta_field( $option_args, $value );
							}
							?>
						</div>
					</div>
					<div id="tab-type" class="mtsnb-tabs-content <?php if ( $active_tab === 'type' ) echo 'active'; ?>">
						<div class="mtsnb-tab-desc"><?php _e( 'Set up notification bar content. Select content type and fill in the fields.', $this->plugin_name ); ?></div>
						<div class="mtsnb-tab-options clearfix">
							<?php $content_type = ( isset( $value['content_type'] ) && !empty( $value['content_type'] ) ) ? $value['content_type'] : 'button'; ?>
							<input type="hidden" class="mtsnb-tab-option" name="mtsnb_fields[content_type]" id="mtsnb_fields_content_type" value="<?php echo $content_type; ?>" />
							<ul class="mtsnb-tabs-nav" id="sub-tabs-nav">
								<li><a href="#tab-button" <?php if ( $content_type === 'button' ) echo 'class="active"'; ?>><?php _e( 'Text and Link/Button', $this->plugin_name ); ?></a></li>
								<li><a href="#tab-posts" <?php if ( $content_type === 'posts' ) echo 'class="active"'; ?>><?php _e( 'Posts', $this->plugin_name ); ?></a></li>
								<li><a href="#tab-newsletter" <?php if ( $content_type === 'newsletter' ) echo 'class="active"'; ?>><?php _e( 'Newsletter', $this->plugin_name ); ?></a></li>
								<li><a href="#tab-social" <?php if ( $content_type === 'social' ) echo 'class="active"'; ?>><?php _e( 'Social Media', $this->plugin_name ); ?></a></li>
								<li><a href="#tab-facebook" <?php if ( $content_type === 'facebook' ) echo 'class="active"'; ?>><?php _e( 'Facebook Feeds', $this->plugin_name ); ?></a></li>
								<li><a href="#tab-twitter" <?php if ( $content_type === 'twitter' ) echo 'class="active"'; ?>><?php _e( 'Twitter Feeds', $this->plugin_name ); ?></a></li>
								<li><a href="#tab-countdown" <?php if ( $content_type === 'countdown' ) echo 'class="active"'; ?>><?php _e( 'Countdown Timer', $this->plugin_name ); ?></a></li>
								<li><a href="#tab-popup" <?php if ( $content_type === 'popup' ) echo 'class="active"'; ?>><?php _e( 'Popup Video', $this->plugin_name ); ?></a></li>
								<li><a href="#tab-search" <?php if ( $content_type === 'search' ) echo 'class="active"'; ?>><?php _e( 'Search Form', $this->plugin_name ); ?></a></li>
								<li><a href="#tab-custom" <?php if ( $content_type === 'custom' ) echo 'class="active"'; ?>><?php _e( 'Custom', $this->plugin_name ); ?></a></li>
							</ul>
							<div class="meta-tabs-wrap" id="sub-tabs-wrap">
								<div id="tab-button" class="mtsnb-tabs-content <?php if ( $content_type === 'button' ) echo 'active'; ?>">
									<?php
									foreach ( $button_content_type_options as $option_args ) {
										$this->custom_meta_field( $option_args, $value );
									}
									?>
								</div>
								<div id="tab-posts" class="mtsnb-tabs-content <?php if ( $content_type === 'posts' ) echo 'active'; ?>">
									<?php
									foreach ( $posts_content_type_options as $option_args ) {
										$this->custom_meta_field( $option_args, $value );
									}
									?>
								</div>
								<div id="tab-newsletter" class="mtsnb-tabs-content <?php if ( $content_type === 'newsletter' ) echo 'active'; ?>">
									<?php
									foreach ( $newsletter_content_type_options as $option_args ) {
										$this->custom_meta_field( $option_args, $value );
									}
									?>
								</div>
								<div id="tab-social" class="mtsnb-tabs-content <?php if ( $content_type === 'social' ) echo 'active'; ?>">
									<?php
									foreach ( $social_content_type_options as $option_args ) {
										$this->custom_meta_field( $option_args, $value );
									}
									?>
									<?php $social_icons = $this->get_social_icons(); ?>

									<button type="button" role="button" class="mtsnb-social-add-platform mtsnb-social-div button" id="mtsnb-social-add-platform">
										<i class="fa fa-plus"></i> <?php _e('Add Social Media Platform', $this->plugin_name); ?>
									</button>
									<br/>
									<br/>

									<table id="mtsnb-social-div-table" class="table table-striped mtsnb-social-div">
										
										<tbody id="mtsnb-social-div-tbody">
										<?php
										$value_icons = isset($value['social'])? $value['social'] : array();
										$value_icons = array_values($value_icons);
										foreach ( $value_icons as $key1 => $icon ) { ?>
											<tr class="text-center">
												<td>
													<i class="mtsnb-move fa fa-arrows"></i>
												</td>
												<td>
													<select id="mtsnb_fields_social_type" name="mtsnb_fields[social][<?php echo $key1; ?>][type]">
													<?php foreach ($social_icons as $key => $name) { ?>
														<option value="<?php echo $key; ?>" <?php selected($value_icons[$key1]['type'], $key, true); ?>><?php echo $name; ?></option>
													<?php } ?>
													</select>
												</td>
												<td>
													<input class="form-control" id="mtsnb_fields_social_social_url" name="mtsnb_fields[social][<?php echo $key1; ?>][url]" type="text" value="<?php echo (isset($value_icons[$key1]['url']) ? $value_icons[$key1]['url'] :''); ?>" placeholder="http://example.com"/>
												</td>
												<td>
													<button class="mtsnb-social-remove-platform button"><i class="fa fa-close"></i> <?php _e('Remove', $this->plugin_name); ?></button>
												</td>
											</tr>
										<?php } ?>
										</tbody>
									</table>
								</div>
								<div id="tab-facebook" class="mtsnb-tabs-content <?php if ( $content_type === 'facebook' ) echo 'active'; ?>">
									<?php
									foreach ( $fbfeed_content_type_options as $option_args ) {
										$this->custom_meta_field( $option_args, $value );
									}
									?>
								</div>
								<div id="tab-twitter" class="mtsnb-tabs-content <?php if ( $content_type === 'twitter' ) echo 'active'; ?>">
									<?php
									if ( version_compare( PHP_VERSION, '5.4.0' ) >= 0 ) {
										foreach ( $twitter_content_type_options as $option_args ) {
											$this->custom_meta_field( $option_args, $value );
										}
									} else {
										echo '<div class="form-row">';
										_e( 'The Twitter Feed content requires at least PHP version 5.4.0', $this->plugin_name );
										echo '</div>';
									}
									?>
								</div>
								<div id="tab-countdown" class="mtsnb-tabs-content <?php if ( $content_type === 'countdown' ) echo 'active'; ?>">
									<?php
									foreach ( $counter_content_type_options as $option_args ) {
										$this->custom_meta_field( $option_args, $value );
									}
									?>
								</div>
								<div id="tab-popup" class="mtsnb-tabs-content <?php if ( $content_type === 'popup' ) echo 'active'; ?>">
									<?php
									foreach ( $popup_content_type_options as $option_args ) {
										$this->custom_meta_field( $option_args, $value );
									}
									?>
								</div>
								<div id="tab-search" class="mtsnb-tabs-content <?php if ( $content_type === 'search' ) echo 'active'; ?>">
									<?php
									foreach ( $search_content_type_options as $option_args ) {
										$this->custom_meta_field( $option_args, $value );
									}
									?>
								</div>
								<div id="tab-custom" class="mtsnb-tabs-content <?php if ( $content_type === 'custom' ) echo 'active'; ?>">
									<?php
									foreach ( $custom_content_type_options as $option_args ) {
										$this->custom_meta_field( $option_args, $value );
									}
									?>
								</div>
							</div>
							<?php //////////////////// A/B testing ///////////////////////// ?>
							<div class="clearfix"></div>
							<br />
							<br />
							<?php $b_enabled = ( isset( $value['b_enabled'] ) && !empty( $value['b_enabled'] ) ) ? $value['b_enabled'] : ''; ?>
							<button type="button" role="button" class="button button-primary mtsnb-enable-split-test<?php if ( $b_enabled === '1' ) echo ' active'; ?>">
								<?php if ( $b_enabled === '1' ) { _e( 'Disable Split Test', $this->plugin_name ); } else { _e( 'Enable Split Test', $this->plugin_name ); } ?>
							</button>
							<input type="hidden" class="mtsnb-b-option" name="mtsnb_fields[b_enabled]" id="mtsnb_fields_b_enabled" value="<?php echo $b_enabled; ?>" />
							<button type="button" role="button" class="button mtsnb-reset-split-test<?php if ( $b_enabled === '1' ) echo ' show'; ?>" data-bar-id="<?php echo $post->ID; ?>"><?php _e( 'Restart Split Test', $this->plugin_name ); ?></button>
							<div id="mtsnb-test-stats-a" class="mtsnb-test-stats<?php if ( $b_enabled === '1' ) echo ' active'; ?>">
								<?php $this->ab_test_stats( 'a', $post->ID ); ?>
							</div>
							<br />
							<br />
							<div id="b-sub-tabs-wrap" <?php if ( $b_enabled === '1' ) echo 'class="active"'; ?>>
								<div class="mtsnb-tab-desc clearfix">
									<?php _e( 'Allocate traffic and set up second variation content below.', $this->plugin_name ); ?>
									<div id="mtsnb-ab-sliders">
										<?php $a_traffic = ( isset( $value['a_traffic'] ) ) ? $value['a_traffic'] : '50'; ?>
										<input type="hidden" class="mtsnb-ab-slider-a-option" name="mtsnb_fields[a_traffic]" id="mtsnb_fields_a_traffic" value="<?php echo $a_traffic; ?>" />
										<div class="mtsnb-ab-slider-label"><?php printf( __( 'Traffic on first variation: %s', $this->plugin_name ), '<span class="mtsnb-a-slider-num">'.$a_traffic.'</span> %' )?></div>
										<div class="mtsnb-ab-slider mtsnb-ab-slider-a"></div>
										<?php $b_traffic = ( isset( $value['b_traffic'] ) ) ? $value['b_traffic'] : '50'; ?>
										<input type="hidden" class="mtsnb-ab-slider-b-option" name="mtsnb_fields[b_traffic]" id="mtsnb_fields_a_traffic" value="<?php echo $b_traffic; ?>" />
										<div class="mtsnb-ab-slider-label"><?php printf( __( 'Traffic on second variation: %s', $this->plugin_name ), '<span class="mtsnb-b-slider-num">'.$b_traffic.'</span> %' )?></div>
										<div class="mtsnb-ab-slider mtsnb-ab-slider-b"></div>
									</div>
								</div>
								<?php $b_content_type = ( isset( $value['b_content_type'] ) && !empty( $value['b_content_type'] ) ) ? $value['b_content_type'] : 'button'; ?>
								<input type="hidden" class="mtsnb-tab-option" name="mtsnb_fields[b_content_type]" id="mtsnb_fields_b_content_type" value="<?php echo $b_content_type; ?>" />
								<ul class="mtsnb-tabs-nav" id="sub-tabs-nav">
									<li><a href="#tab-button-b" <?php if ( $b_content_type === 'button-b' ) echo 'class="active"'; ?>><?php _e( 'Text and Link/Button', $this->plugin_name ); ?></a></li>
									<li><a href="#tab-posts-b" <?php if ( $b_content_type === 'posts-b' ) echo 'class="active"'; ?>><?php _e( 'Posts', $this->plugin_name ); ?></a></li>
									<li><a href="#tab-newsletter-b" <?php if ( $b_content_type === 'newsletter-b' ) echo 'class="active"'; ?>><?php _e( 'Newsletter', $this->plugin_name ); ?></a></li>
									<li><a href="#tab-social-b" <?php if ( $b_content_type === 'social-b' ) echo 'class="active"'; ?>><?php _e( 'Social Media', $this->plugin_name ); ?></a></li>
									<li><a href="#tab-facebook-b" <?php if ( $b_content_type === 'facebook-b' ) echo 'class="active"'; ?>><?php _e( 'Facebook Feeds', $this->plugin_name ); ?></a></li>
									<li><a href="#tab-twitter-b" <?php if ( $b_content_type === 'twitter-b' ) echo 'class="active"'; ?>><?php _e( 'Twitter Feeds', $this->plugin_name ); ?></a></li>
									<li><a href="#tab-countdown-b" <?php if ( $b_content_type === 'countdown-b' ) echo 'class="active"'; ?>><?php _e( 'Countdown Timer', $this->plugin_name ); ?></a></li>
									<li><a href="#tab-popup-b" <?php if ( $b_content_type === 'popup-b' ) echo 'class="active"'; ?>><?php _e( 'Popup Video', $this->plugin_name ); ?></a></li>
									<li><a href="#tab-search-b" <?php if ( $b_content_type === 'search-b' ) echo 'class="active"'; ?>><?php _e( 'Search Form', $this->plugin_name ); ?></a></li>
									<li><a href="#tab-custom-b" <?php if ( $b_content_type === 'custom-b' ) echo 'class="active"'; ?>><?php _e( 'Custom', $this->plugin_name ); ?></a></li>
								</ul>
								<div class="meta-tabs-wrap" id="sub-tabs-wrap">
									<div id="tab-button-b" class="mtsnb-tabs-content <?php if ( $b_content_type === 'button-b' ) echo 'active'; ?>">
										<?php
										foreach ( $button_content_type_options as $option_args ) {
											$this->custom_meta_field( $option_args, $value, true );
										}
										?>
									</div>
									<div id="tab-posts-b" class="mtsnb-tabs-content <?php if ( $b_content_type === 'posts-b' ) echo 'active'; ?>">
										<?php
										foreach ( $posts_content_type_options as $option_args ) {
											$this->custom_meta_field( $option_args, $value, true );
										}
										?>
									</div>
									<div id="tab-newsletter-b" class="mtsnb-tabs-content <?php if ( $b_content_type === 'newsletter-b' ) echo 'active'; ?>">
										<?php
										foreach ( $newsletter_content_type_options as $option_args ) {
											$this->custom_meta_field( $option_args, $value, true );
										}
										?>
									</div>
									<div id="tab-social-b" class="mtsnb-tabs-content <?php if ( $b_content_type === 'social-b' ) echo 'active'; ?>">
										<?php
										foreach ( $social_content_type_options as $option_args ) {
											$this->custom_meta_field( $option_args, $value, true );
										}
										?>
										<?php $b_social_icons = $this->get_social_icons(); ?>

										<button type="button" role="button" class="mtsnb-social-add-platform mtsnb-social-div button" id="mtsnb-b-social-add-platform">
											<i class="fa fa-plus"></i> <?php _e('Add Social Media Platform', $this->plugin_name); ?>
										</button>
										<br/>
										<br/>

										<table id="mtsnb-b-social-div-table" class="table table-striped mtsnb-social-div">
											
											<tbody id="mtsnb-b-social-div-tbody">
											<?php
											$b_value_icons = isset($value['b_social'])? $value['b_social'] : array();
											$b_value_icons = array_values($b_value_icons);
											foreach ( $b_value_icons as $key1 => $icon ) { ?>
												<tr class="text-center">
													<td>
														<i class="mtsnb-move fa fa-arrows"></i>
													</td>
													<td>
														<select id="mtsnb_fields_b_social_type" name="mtsnb_fields[b_social][<?php echo $key1; ?>][type]">
														<?php foreach ($b_social_icons as $key => $name) { ?>
															<option value="<?php echo $key; ?>" <?php selected($b_value_icons[$key1]['type'], $key, true); ?>><?php echo $name; ?></option>
														<?php } ?>
														</select>
													</td>
													<td>
														<input class="form-control" id="mtsnb_fields_b_social_url" name="mtsnb_fields[b_social][<?php echo $key1; ?>][url]" type="text" value="<?php echo (isset($b_value_icons[$key1]['url']) ? $b_value_icons[$key1]['url'] :''); ?>" placeholder="http://example.com"/>
													</td>
													<td>
														<button class="mtsnb-social-remove-platform button"><i class="fa fa-close"></i> <?php _e('Remove', $this->plugin_name); ?></button>
													</td>
												</tr>
											<?php } ?>
											</tbody>
										</table>
									</div>
									<div id="tab-facebook-b" class="mtsnb-tabs-content <?php if ( $b_content_type === 'facebook-b' ) echo 'active'; ?>">
										<?php
										foreach ( $fbfeed_content_type_options as $option_args ) {
											$this->custom_meta_field( $option_args, $value, true );
										}
										?>
									</div>
									<div id="tab-twitter-b" class="mtsnb-tabs-content <?php if ( $b_content_type === 'twitter-b' ) echo 'active'; ?>">
										<?php
										if ( version_compare( PHP_VERSION, '5.4.0' ) >= 0 ) {
											foreach ( $twitter_content_type_options as $option_args ) {
												$this->custom_meta_field( $option_args, $value, true );
											}
										} else {
											echo '<div class="form-row">';
											_e( 'The Twitter Feed content requires at least PHP version 5.4.0', $this->plugin_name );
											echo '</div>';
										}
									?>
									</div>
									<div id="tab-countdown-b" class="mtsnb-tabs-content <?php if ( $b_content_type === 'countdown-b' ) echo 'active'; ?>">
										<?php
										foreach ( $counter_content_type_options as $option_args ) {
											$this->custom_meta_field( $option_args, $value, true );
										}
										?>
									</div>
									<div id="tab-popup-b" class="mtsnb-tabs-content <?php if ( $b_content_type === 'popup-b' ) echo 'active'; ?>">
										<?php
										foreach ( $popup_content_type_options as $option_args ) {
											$this->custom_meta_field( $option_args, $value, true );
										}
										?>
									</div>
									<div id="tab-search-b" class="mtsnb-tabs-content <?php if ( $b_content_type === 'search-b' ) echo 'active'; ?>">
										<?php
										foreach ( $search_content_type_options as $option_args ) {
											$this->custom_meta_field( $option_args, $value, true );
										}
										?>
									</div>
									<div id="tab-custom-b" class="mtsnb-tabs-content <?php if ( $b_content_type === 'custom-b' ) echo 'active'; ?>">
										<?php
										foreach ( $custom_content_type_options as $option_args ) {
											$this->custom_meta_field( $option_args, $value, true );
										}
										?>
									</div>
								</div>
								<div id="clearfix"></div>
								<div id="mtsnb-test-stats-b" class="mtsnb-test-stats">
									<?php $this->ab_test_stats( 'b', $post->ID ); ?>
								</div>
							</div>
							<?php //////////////////// A/B testing ///////////////////////// ?>
						</div>
					</div>
					<div id="tab-style" class="mtsnb-tabs-content <?php if ( $active_tab === 'style' ) echo 'active'; ?>">
						<div class="mtsnb-tab-desc"><?php _e( 'Change the appearance of the notification bar. You can choose from the predefined color sets, or select custom colors.', $this->plugin_name ); ?></div>
						<div class="mtsnb-tab-options clearfix">
							<div class="form-row">
								<?php $this->color_palettes_select(); ?>
							</div>
							<?php
							foreach ( $style_options as $option_args ) {
								$this->custom_meta_field( $option_args, $value );
							}
							?>
						</div>
					</div>
					<div id="tab-conditions" class="mtsnb-tabs-content <?php if ( $active_tab === 'conditions' ) echo 'active'; ?>">
						<div class="mtsnb-tab-desc"><?php _e( 'Choose when and where to display the notification bar.', $this->plugin_name ); ?></div>
						<div id="conditions-selector-wrap" class="clearfix">
							<div id="conditions-selector">
								<ul>
									<?php $condition_location_state       = isset( $value['conditions'] ) && isset( $value['conditions']['location'] ) && ( isset( $value['conditions']['location']['state'] ) && !empty( $value['conditions']['location']['state'] ) ) ? $value['conditions']['location']['state'] : ''; ?>
									<?php $condition_notlocation_state    = isset( $value['conditions'] ) && isset( $value['conditions']['notlocation'] ) && ( isset( $value['conditions']['notlocation']['state'] ) && !empty( $value['conditions']['notlocation']['state'] ) ) ? $value['conditions']['notlocation']['state'] : ''; ?>
									<?php $condition_location_disabled    = empty( $condition_notlocation_state ) ? '' : ' disabled'; ?>
									<?php $condition_notlocation_disabled = empty( $condition_location_state ) ? '' : ' disabled'; ?>
									<li id="condition-location" data-disable="notlocation" class="condition-checkbox <?php echo $condition_location_state.$condition_location_disabled; ?>">
										<?php _e( 'On specific locations', $this->plugin_name ); ?>
										<div class="mtsnb-check"></div>
										<input type="hidden" class="mtsnb-condition-checkbox-input" id="mtsnb_fields_conditions_location_state" name="mtsnb_fields[conditions][location][state]" value="<?php echo $condition_location_state; ?>">
									</li>
									<li id="condition-notlocation" data-disable="location" class="condition-checkbox <?php echo $condition_notlocation_state.$condition_notlocation_disabled; ?>">
										<?php _e( 'Not on specific locations', $this->plugin_name ); ?>
										<div class="mtsnb-check"></div>
										<input type="hidden" class="mtsnb-condition-checkbox-input" id="mtsnb_fields_conditions_notlocation_state" name="mtsnb_fields[conditions][notlocation][state]" value="<?php echo $condition_notlocation_state; ?>">
									</li>
									<?php $condition_logged_state       = isset( $value['conditions'] ) && isset( $value['conditions']['logged'] ) && ( isset( $value['conditions']['logged']['state'] ) && !empty( $value['conditions']['logged']['state'] ) ) ? $value['conditions']['logged']['state'] : ''; ?>
									<?php $condition_notlogged_state    = isset( $value['conditions'] ) && isset( $value['conditions']['notlogged'] ) && ( isset( $value['conditions']['notlogged']['state'] ) && !empty( $value['conditions']['notlogged']['state'] ) ) ? $value['conditions']['notlogged']['state'] : ''; ?>
									<?php $condition_logged_disabled    = empty( $condition_notlogged_state ) ? '' : ' disabled'; ?>
									<?php $condition_notlogged_disabled = empty( $condition_logged_state ) ? '' : ' disabled'; ?>
									<li id="condition-logged" data-disable="notlogged" class="condition-checkbox <?php echo $condition_logged_state.$condition_logged_disabled; ?>">
										<?php _e( 'Visitor is logged in', $this->plugin_name ); ?>
										<div class="mtsnb-check"></div>
										<input type="hidden" class="mtsnb-condition-checkbox-input" id="mtsnb_fields_conditions_logged_state" name="mtsnb_fields[conditions][logged][state]" value="<?php echo $condition_logged_state; ?>">
									</li>
									<li id="condition-notlogged" data-disable="logged" class="condition-checkbox <?php echo $condition_notlogged_state.$condition_notlogged_disabled; ?>">
										<?php _e( 'Visitor is not logged in', $this->plugin_name ); ?>
										<div class="mtsnb-check"></div>
										<input type="hidden" class="mtsnb-condition-checkbox-input" id="mtsnb_fields_conditions_notlogged_state" name="mtsnb_fields[conditions][notlogged][state]" value="<?php echo $condition_notlogged_state; ?>">
									</li>
									<?php $condition_less_state = isset( $value['conditions'] ) && isset( $value['conditions']['less'] ) && ( isset( $value['conditions']['less']['state'] ) && !empty( $value['conditions']['less']['state'] ) ) ? $value['conditions']['less']['state'] : ''; ?>
									<li id="condition-less" class="condition-checkbox <?php echo $condition_less_state; ?>">
										<?php _e( 'Show N times', $this->plugin_name ); ?>
										<div class="mtsnb-check"></div>
										<input type="hidden" class="mtsnb-condition-checkbox-input" id="mtsnb_fields_conditions_less_state" name="mtsnb_fields[conditions][less][state]" value="<?php echo $condition_less_state; ?>">
									</li>
									<?php $condition_after_state = isset( $value['conditions'] ) && isset( $value['conditions']['after'] ) && ( isset( $value['conditions']['after']['state'] ) && !empty( $value['conditions']['after']['state'] ) ) ? $value['conditions']['after']['state'] : ''; ?>
									<li id="condition-after" class="condition-checkbox <?php echo $condition_after_state; ?>">
										<?php _e( 'Show after N visits', $this->plugin_name ); ?>
										<div class="mtsnb-check"></div>
										<input type="hidden" class="mtsnb-condition-checkbox-input" id="mtsnb_fields_conditions_after_state" name="mtsnb_fields[conditions][after][state]" value="<?php echo $condition_after_state; ?>">
									</li>
									<?php $condition_onmobile_state       = isset( $value['conditions'] ) && isset( $value['conditions']['onmobile'] ) && ( isset( $value['conditions']['onmobile']['state'] ) && !empty( $value['conditions']['onmobile']['state'] ) ) ? $value['conditions']['onmobile']['state'] : ''; ?>
									<?php $condition_notonmobile_state    = isset( $value['conditions'] ) && isset( $value['conditions']['notonmobile'] ) && ( isset( $value['conditions']['notonmobile']['state'] ) && !empty( $value['conditions']['notonmobile']['state'] ) ) ? $value['conditions']['notonmobile']['state'] : ''; ?>
									<?php $condition_onmobile_disabled    = empty( $condition_notonmobile_state ) ? '' : ' disabled'; ?>
									<?php $condition_notonmobile_disabled = empty( $condition_onmobile_state ) ? '' : ' disabled'; ?>
									<li id="condition-onmobile" data-disable="notonmobile" class="condition-checkbox <?php echo $condition_onmobile_state.$condition_onmobile_disabled; ?>">
										<?php _e( 'Only on mobile devices', $this->plugin_name ); ?>
										<div class="mtsnb-check"></div>
										<input type="hidden" class="mtsnb-condition-checkbox-input" id="mtsnb_fields_conditions_onmobile_state" name="mtsnb_fields[conditions][onmobile][state]" value="<?php echo $condition_onmobile_state; ?>">
									</li>
									<li id="condition-notonmobile" data-disable="onmobile" class="condition-checkbox <?php echo $condition_notonmobile_state.$condition_notonmobile_disabled; ?>">
										<?php _e( 'Not on mobile devices', $this->plugin_name ); ?>
										<div class="mtsnb-check"></div>
										<input type="hidden" class="mtsnb-condition-checkbox-input" id="mtsnb_fields_conditions_notonmobile_state" name="mtsnb_fields[conditions][notonmobile][state]" value="<?php echo $condition_notonmobile_state; ?>">
									</li>
									<?php $condition_referrer_state       = isset( $value['conditions'] ) && isset( $value['conditions']['referrer'] ) && ( isset( $value['conditions']['referrer']['state'] ) && !empty( $value['conditions']['referrer']['state'] ) ) ? $value['conditions']['referrer']['state'] : ''; ?>
									<?php $condition_notreferrer_state    = isset( $value['conditions'] ) && isset( $value['conditions']['notreferrer'] ) && ( isset( $value['conditions']['notreferrer']['state'] ) && !empty( $value['conditions']['notreferrer']['state'] ) ) ? $value['conditions']['notreferrer']['state'] : ''; ?>
									<?php $condition_referrer_disabled    = empty( $condition_notreferrer_state ) ? '' : ' disabled'; ?>
									<?php $condition_notreferrer_disabled = empty( $condition_referrer_state ) ? '' : ' disabled'; ?>
									<li id="condition-referrer" data-disable="notreferrer" class="condition-checkbox <?php echo $condition_referrer_state.$condition_referrer_disabled; ?>">
										<?php _e( 'From a specific referrer', $this->plugin_name ); ?>
										<div class="mtsnb-check"></div>
										<input type="hidden" class="mtsnb-condition-checkbox-input" id="mtsnb_fields_conditions_referrer_state" name="mtsnb_fields[conditions][referrer][state]" value="<?php echo $condition_referrer_state; ?>">
									</li>
									<li id="condition-notreferrer" data-disable="referrer" class="condition-checkbox <?php echo $condition_notreferrer_state.$condition_notreferrer_disabled; ?>">
										<?php _e( 'Not from a specific referrer', $this->plugin_name ); ?>
										<div class="mtsnb-check"></div>
										<input type="hidden" class="mtsnb-condition-checkbox-input" id="mtsnb_fields_conditions_notreferrer_state" name="mtsnb_fields[conditions][notreferrer][state]" value="<?php echo $condition_notreferrer_state; ?>">
									</li>
									<?php $condition_utm_state       = isset( $value['conditions'] ) && isset( $value['conditions']['utm'] ) && ( isset( $value['conditions']['utm']['state'] ) && !empty( $value['conditions']['utm']['state'] ) ) ? $value['conditions']['utm']['state'] : ''; ?>
									<?php $condition_notutm_state    = isset( $value['conditions'] ) && isset( $value['conditions']['notutm'] ) && ( isset( $value['conditions']['notutm']['state'] ) && !empty( $value['conditions']['notutm']['state'] ) ) ? $value['conditions']['notutm']['state'] : ''; ?>
									<?php $condition_utm_disabled    = empty( $condition_notutm_state ) ? '' : ' disabled'; ?>
									<?php $condition_notutm_disabled = empty( $condition_utm_state ) ? '' : ' disabled'; ?>
									<li id="condition-utm" data-disable="notutm" class="condition-checkbox <?php echo $condition_utm_state.$condition_utm_disabled; ?>">
										<?php _e( 'With specific UTM tags', $this->plugin_name ); ?>
										<div class="mtsnb-check"></div>
										<input type="hidden" class="mtsnb-condition-checkbox-input" id="mtsnb_fields_conditions_utm_state" name="mtsnb_fields[conditions][utm][state]" value="<?php echo $condition_utm_state; ?>">
									</li>
									<li id="condition-notutm" data-disable="utm" class="condition-checkbox <?php echo $condition_notutm_state.$condition_notutm_disabled; ?>">
										<?php _e( 'Without specific UTM tags', $this->plugin_name ); ?>
										<div class="mtsnb-check"></div>
										<input type="hidden" class="mtsnb-condition-checkbox-input" id="mtsnb_fields_conditions_notutm_state" name="mtsnb_fields[conditions][notutm][state]" value="<?php echo $condition_notutm_state; ?>">
									</li>
									<?php $condition_time_state       = isset( $value['conditions'] ) && isset( $value['conditions']['time'] ) && ( isset( $value['conditions']['time']['state'] ) && !empty( $value['conditions']['time']['state'] ) ) ? $value['conditions']['time']['state'] : ''; ?>
									<?php $condition_nottime_state    = isset( $value['conditions'] ) && isset( $value['conditions']['nottime'] ) && ( isset( $value['conditions']['nottime']['state'] ) && !empty( $value['conditions']['nottime']['state'] ) ) ? $value['conditions']['nottime']['state'] : ''; ?>
									<?php $condition_time_disabled    = empty( $condition_nottime_state ) ? '' : ' disabled'; ?>
									<?php $condition_nottime_disabled = empty( $condition_time_state ) ? '' : ' disabled'; ?>
									<li id="condition-time" data-disable="nottime" class="condition-checkbox <?php echo $condition_time_state.$condition_time_disabled; ?>">
										<?php _e( 'Show from/to time', $this->plugin_name ); ?>
										<div class="mtsnb-check"></div>
										<input type="hidden" class="mtsnb-condition-checkbox-input" id="mtsnb_fields_conditions_time_state" name="mtsnb_fields[conditions][time][state]" value="<?php echo $condition_time_state; ?>">
									</li>
									<li id="condition-nottime" data-disable="time" class="condition-checkbox <?php echo $condition_nottime_state.$condition_nottime_disabled; ?>">
										<?php _e( 'Hide from/to time', $this->plugin_name ); ?>
										<div class="mtsnb-check"></div>
										<input type="hidden" class="mtsnb-condition-checkbox-input" id="mtsnb_fields_conditions_nottime_state" name="mtsnb_fields[conditions][nottime][state]" value="<?php echo $condition_nottime_state; ?>">
									</li>
								</ul>
							</div>
							<div id="conditions-panels">
								<div id="condition-location-panel" class="mtsnb-conditions-panel <?php echo $condition_location_state; ?>">
									<div class="mtsnb-conditions-panel-title"><?php _e( 'On specific locations', $this->plugin_name ); ?></div>
									<div class="mtsnb-conditions-panel-content">
										<div class="mtsnb-conditions-panel-desc"><?php _e( 'Show Notification Bar on the following locations', $this->plugin_name ); ?></div>
										<div class="mtsnb-conditions-panel-opt">
											<?php $location_home       = isset( $value['conditions'] ) && isset( $value['conditions']['location'] ) && ( isset( $value['conditions']['location']['home'] ) && !empty( $value['conditions']['location']['home'] ) ) ? $value['conditions']['location']['home'] : '0'; ?>
											<?php $location_blog_home  = isset( $value['conditions'] ) && isset( $value['conditions']['location'] ) && ( isset( $value['conditions']['location']['blog_home'] ) && !empty( $value['conditions']['location']['blog_home'] ) ) ? $value['conditions']['location']['blog_home'] : '0'; ?>
											<?php $location_pages      = isset( $value['conditions'] ) && isset( $value['conditions']['location'] ) && ( isset( $value['conditions']['location']['pages'] ) && !empty( $value['conditions']['location']['pages'] ) ) ? $value['conditions']['location']['pages'] : '0'; ?>
											<?php $location_posts      = isset( $value['conditions'] ) && isset( $value['conditions']['location'] ) && ( isset( $value['conditions']['location']['posts'] ) && !empty( $value['conditions']['location']['posts'] ) ) ? $value['conditions']['location']['posts'] : '0'; ?>
											<?php $location_categories = isset( $value['conditions'] ) && isset( $value['conditions']['location'] ) && ( isset( $value['conditions']['location']['categories'] ) && !empty( $value['conditions']['location']['categories'] ) ) ? $value['conditions']['location']['categories'] : '0'; ?>
											<?php $location_tags       = isset( $value['conditions'] ) && isset( $value['conditions']['location'] ) && ( isset( $value['conditions']['location']['tags'] ) && !empty( $value['conditions']['location']['tags'] ) ) ? $value['conditions']['location']['tags'] : '0'; ?>
											<?php $location_date       = isset( $value['conditions'] ) && isset( $value['conditions']['location'] ) && ( isset( $value['conditions']['location']['date'] ) && !empty( $value['conditions']['location']['date'] ) ) ? $value['conditions']['location']['date'] : '0'; ?>
											<?php $location_author     = isset( $value['conditions'] ) && isset( $value['conditions']['location'] ) && ( isset( $value['conditions']['location']['author'] ) && !empty( $value['conditions']['location']['author'] ) ) ? $value['conditions']['location']['author'] : '0'; ?>
											<?php $location_404        = isset( $value['conditions'] ) && isset( $value['conditions']['location'] ) && ( isset( $value['conditions']['location']['404'] ) && !empty( $value['conditions']['location']['404'] ) ) ? $value['conditions']['location']['404'] : '0'; ?>
											<?php $location_search     = isset( $value['conditions'] ) && isset( $value['conditions']['location'] ) && ( isset( $value['conditions']['location']['search'] ) && !empty( $value['conditions']['location']['search'] ) ) ? $value['conditions']['location']['search'] : '0'; ?>
											<?php $location_custom     = isset( $value['conditions'] ) && isset( $value['conditions']['location'] ) && ( isset( $value['conditions']['location']['custom'] ) && !empty( $value['conditions']['location']['custom'] ) ) ? $value['conditions']['location']['custom'] : '0'; ?>
											<p>
												<label>
													<input type="checkbox" class="mtsnb-checkbox" name="mtsnb_fields[conditions][location][home]" id="mtsnb_fields_conditions_location_home" value="1" <?php checked( $location_home, '1', true ); ?> />
													<?php _e( 'Homepage.', $this->plugin_name ); ?>
												</label>
											</p>
											<?php if ( 'page' === get_option('show_on_front') && '0' !== get_option('page_for_posts') && '0' !== get_option('page_on_front') ) { ?>
												<p>
													<label>
														<input type="checkbox" class="mtsnb-checkbox" name="mtsnb_fields[conditions][location][blog_home]" id="mtsnb_fields_conditions_location_blog_home" value="1" <?php checked( $location_blog_home, '1', true ); ?> />
														<?php _e( 'Blog Homepage.', $this->plugin_name ); ?>
													</label>
												</p>
											<?php } ?>
											<p>
												<label>
													<input type="checkbox" class="mtsnb-checkbox mtsnb-checkbox-toggle" name="mtsnb_fields[conditions][location][pages]" id="mtsnb_fields_conditions_location_pages" value="1" <?php checked( $location_pages, '1', true ); ?> />
													<?php _e( 'Pages.', $this->plugin_name ); ?>
												</label>
											</p>
											<div class="mtsnb-conditions-panel-opt <?php if ( '1' === $location_pages ) echo 'active'; ?>" data-checkbox="mtsnb_fields_conditions_location_pages">
												<?php $location_pages_all =  $this->page_select_options; ?>
												<?php $location_pages_entries = isset( $value['conditions'] ) && isset( $value['conditions']['location'] ) && ( isset( $value['conditions']['location']['custom_pages'] ) && !empty( $value['conditions']['location']['custom_pages'] ) ) ? $value['conditions']['location']['custom_pages'] : array(); ?>
												<p><?php _e( 'Select pages. Leave empty to show Notification Bar on all pages', $this->plugin_name ); ?></p>
												<select multiple class="mtsnb-multi-select" name="mtsnb_fields[conditions][location][custom_pages][]" id="mtsnb_fields_conditions_location_custom_pages">
													<?php
													if ( !empty( $location_pages_all ) ) {
														foreach ( $location_pages_all as $id => $name ) {

															$selected =  in_array( $id, $location_pages_entries ) ? ' selected="selected"' : '';
															?>
															<option value="<?php echo esc_attr( $id );?>"<?php echo $selected; ?>><?php echo esc_html( $name ); ?></option>
															<?php
														}
													}
													?>
												</select>
											</div>
											<p>
												<label>
													<input type="checkbox" class="mtsnb-checkbox mtsnb-checkbox-toggle" name="mtsnb_fields[conditions][location][posts]" id="mtsnb_fields_conditions_location_posts" value="1" <?php checked( $location_posts, '1', true ); ?> />
													<?php _e( 'Posts.', $this->plugin_name ); ?>
												</label>
											</p>
											<div class="mtsnb-conditions-panel-opt <?php if ( '1' === $location_posts ) echo 'active'; ?>" data-checkbox="mtsnb_fields_conditions_location_posts">
												<?php
												$location_posts_entries = isset( $value['conditions'] ) && isset( $value['conditions']['location'] ) && ( isset( $value['conditions']['location']['custom_posts'] ) && !empty( $value['conditions']['location']['custom_posts'] ) ) ? $value['conditions']['location']['custom_posts'] : '';
												if ( is_array( $location_posts_entries ) ) {
													$location_posts_entries = implode (', ', $location_posts_entries );
												}
												?>
												<p><?php _e( 'Enter comma separated list of post ids. Leave empty to show Notification Bar on all posts', $this->plugin_name ); ?></p>
												<input type="text" name="mtsnb_fields[conditions][location][custom_posts]" id="mtsnb_fields_conditions_location_custom_posts" value="<?php echo esc_attr( $location_posts_entries );?>" />
											</div>
											<p>
												<label>
													<input type="checkbox" class="mtsnb-checkbox mtsnb-checkbox-toggle" name="mtsnb_fields[conditions][location][categories]" id="mtsnb_fields_conditions_location_categories" value="1" <?php checked( $location_categories, '1', true ); ?> />
													<?php _e( 'Post category archives.', $this->plugin_name ); ?>
												</label>
											</p>
											<div class="mtsnb-conditions-panel-opt <?php if ( '1' === $location_categories ) echo 'active'; ?>" data-checkbox="mtsnb_fields_conditions_location_categories">
												<?php $location_cats_all =  $this->category_select_options; ?>
												<?php $location_cats_entries = isset( $value['conditions'] ) && isset( $value['conditions']['location'] ) && ( isset( $value['conditions']['location']['custom_categories'] ) && !empty( $value['conditions']['location']['custom_categories'] ) ) ? $value['conditions']['location']['custom_categories'] : array(); ?>
												<p><?php _e( 'Select categories. Leave empty to hide Notification Bar on all categories', $this->plugin_name ); ?></p>
												<select multiple class="mtsnb-multi-select" name="mtsnb_fields[conditions][location][custom_categories][]" id="mtsnb_fields_conditions_location_custom_categories">
													<?php
													if ( !empty( $location_cats_all ) ) {
														foreach ( $location_cats_all as $id => $name ) {

															$selected =  in_array( $id, $location_cats_entries ) ? ' selected="selected"' : '';
															?>
															<option value="<?php echo esc_attr( $id );?>"<?php echo $selected; ?>><?php echo esc_html( $name ); ?></option>
															<?php
														}
													}
													?>
												</select>
											</div>
											<p>
												<label>
													<input type="checkbox" class="mtsnb-checkbox mtsnb-checkbox-toggle" name="mtsnb_fields[conditions][location][tags]" id="mtsnb_fields_conditions_location_tags" value="1" <?php checked( $location_tags, '1', true ); ?> />
													<?php _e( 'Post tag archives.', $this->plugin_name ); ?>
												</label>
											</p>
											<div class="mtsnb-conditions-panel-opt <?php if ( '1' === $location_tags ) echo 'active'; ?>" data-checkbox="mtsnb_fields_conditions_location_tags">
												<?php $location_tags_all =  $this->post_tag_select_options; ?>
												<?php $location_tags_entries = isset( $value['conditions'] ) && isset( $value['conditions']['location'] ) && ( isset( $value['conditions']['location']['custom_tags'] ) && !empty( $value['conditions']['location']['custom_tags'] ) ) ? $value['conditions']['location']['custom_tags'] : array(); ?>
												<p><?php _e( 'Select tags. Leave empty to show Notification Bar on all tags', $this->plugin_name ); ?></p>
												<select multiple class="mtsnb-multi-select" name="mtsnb_fields[conditions][location][custom_tags][]" id="mtsnb_fields_conditions_location_custom_tags">
													<?php
													if ( !empty( $location_tags_all ) ) {
														foreach ( $location_tags_all as $id => $name ) {

															$selected =  in_array( $id, $location_tags_entries ) ? ' selected="selected"' : '';
															?>
															<option value="<?php echo esc_attr( $id );?>"<?php echo $selected; ?>><?php echo esc_html( $name ); ?></option>
															<?php
														}
													}
													?>
												</select>
											</div>
											<p>
												<label>
													<input type="checkbox" class="mtsnb-checkbox" name="mtsnb_fields[conditions][location][date]" id="mtsnb_fields_conditions_location_date" value="1" <?php checked( $location_date, '1', true ); ?> />
													<?php _e( 'Date archives.', $this->plugin_name ); ?>
												</label>
											</p>
											<p>
												<label>
													<input type="checkbox" class="mtsnb-checkbox" name="mtsnb_fields[conditions][location][author]" id="mtsnb_fields_conditions_location_author" value="1" <?php checked( $location_author, '1', true ); ?> />
													<?php _e( 'Author archives.', $this->plugin_name ); ?>
												</label>
											</p>
											<p>
												<label>
													<input type="checkbox" class="mtsnb-checkbox" name="mtsnb_fields[conditions][location][404]" id="mtsnb_fields_conditions_location_404" value="1" <?php checked( $location_404, '1', true ); ?> />
													<?php _e( 'Error 404 page.', $this->plugin_name ); ?>
												</label>
											</p>
											<p>
												<label>
													<input type="checkbox" class="mtsnb-checkbox" name="mtsnb_fields[conditions][location][search]" id="mtsnb_fields_conditions_location_search" value="1" <?php checked( $location_search, '1', true ); ?> />
													<?php _e( 'Search results page.', $this->plugin_name ); ?>
												</label>
											</p>
											<p>
												<label>
													<input type="checkbox" class="mtsnb-checkbox mtsnb-checkbox-toggle" name="mtsnb_fields[conditions][location][custom]" id="mtsnb_fields_conditions_location_custom" value="1" <?php checked( $location_custom, '1', true ); ?> />
													<?php _e( 'Custom.', $this->plugin_name ); ?>
												</label>
											</p>
											<div class="mtsnb-conditions-panel-opt <?php if ( '1' === $location_custom ) echo 'active'; ?>" data-checkbox="mtsnb_fields_conditions_location_custom">
												<?php $location_entries = isset( $value['conditions'] ) && isset( $value['conditions']['location'] ) && ( isset( $value['conditions']['location']['data'] ) && !empty( $value['conditions']['location']['data'] ) ) ? $value['conditions']['location']['data'] : array(); ?>
												<p><?php _e( 'Enter custom URLs, type full URL and hit enter/space to separate entries.', $this->plugin_name ); ?></p>
												<select multiple class="mtsnb-multi-text" name="mtsnb_fields[conditions][location][data][]" id="mtsnb_fields_conditions_location_data">
													<?php
													if ( !empty( $location_entries ) ) {
														foreach ( $location_entries as $entry ) {
															?>
															<option value="<?php echo esc_attr( $entry );?>" selected="selected"><?php echo esc_html( $entry ); ?></option>
															<?php
														}
													}
													?>
												</select>
											</div>
											<?php
											//////////////////// CPT & Tax Support /////////////////////////
											$cpt_supp = get_option( 'mtsnb_supported_custom_post_types', array() );
											if ( !empty( $cpt_supp ) ) {
												foreach ( $cpt_supp as $cpt ) {
													$location_cpt  = isset( $value['conditions'] ) && isset( $value['conditions']['location'] ) && ( isset( $value['conditions']['location'][ $cpt ] ) && !empty( $value['conditions']['location'][ $cpt ] ) ) ? $value['conditions']['location'][ $cpt ] : '0';
													if ( isset( $this->supported_custom_post_types[ $cpt ] ) ) {
													?>
														<p>
															<label>
																<input type="checkbox" class="mtsnb-checkbox mtsnb-checkbox-toggle" name="mtsnb_fields[conditions][location][<?php echo $cpt; ?>]" id="mtsnb_fields_conditions_location_<?php echo $cpt; ?>" value="1" <?php checked( $location_cpt, '1', true ); ?> />
																<?php echo $this->supported_custom_post_types[ $cpt ]; ?>
															</label>
														</p>
														<div class="mtsnb-conditions-panel-opt <?php if ( '1' === $location_cpt ) echo 'active'; ?>" data-checkbox="mtsnb_fields_conditions_location_<?php echo $cpt; ?>">
															<?php
															$location_cpt_entries = isset( $value['conditions'] ) && isset( $value['conditions']['location'] ) && ( isset( $value['conditions']['location'][ 'custom_'.$cpt ] ) && !empty( $value['conditions']['location'][ 'custom_'.$cpt ] ) ) ? $value['conditions']['location'][ 'custom_'.$cpt ] : '';
															if ( is_array( $location_cpt_entries ) ) {
																$location_cpt_entries = implode (', ', $location_cpt_entries );
															}
															?>
															<p><?php printf( __( 'Enter comma separated list of ids. Leave empty to show Notification Bar on all %s', $this->plugin_name ), $this->supported_custom_post_types[ $cpt ] ); ?></p>
															<input type="text" name="mtsnb_fields[conditions][location][custom_<?php echo $cpt; ?>]" id="mtsnb_fields_conditions_location_custom_<?php echo $cpt; ?>" value="<?php echo esc_attr( $location_cpt_entries );?>" />
														</div>
														<?php
														if ( isset( $this->supported_custom_taxonomies[ $cpt ] ) ) {

															foreach ( $this->supported_custom_taxonomies[ $cpt ] as $name => $object ) {

																$location_ct  = isset( $value['conditions'] ) && isset( $value['conditions']['location'] ) && ( isset( $value['conditions']['location'][ $name ] ) && !empty( $value['conditions']['location'][ $name ] ) ) ? $value['conditions']['location'][ $name ] : '0';
																?>
																<p>
																	<label>
																		<input type="checkbox" class="mtsnb-checkbox mtsnb-checkbox-toggle" name="mtsnb_fields[conditions][location][<?php echo $name; ?>]" id="mtsnb_fields_conditions_location_<?php echo $name; ?>" value="1" <?php checked( $location_ct, '1', true ); ?> />
																		<?php printf( __( '%s archives.', $this->plugin_name ), $object->labels->singular_name ); ?>
																	</label>
																</p>
																<div class="mtsnb-conditions-panel-opt <?php if ( '1' === $location_ct ) echo 'active'; ?>" data-checkbox="mtsnb_fields_conditions_location_<?php echo $name; ?>">
																	<?php $location_ct_all =  $this->custom_taxonomies_select_options[ $cpt ][ $name ]; ?>
																	<?php $location_ct_entries = isset( $value['conditions'] ) && isset( $value['conditions']['location'] ) && ( isset( $value['conditions']['location']['custom_'.$name] ) && !empty( $value['conditions']['location']['custom_'.$name] ) ) ? $value['conditions']['location']['custom_'.$name] : array(); ?>
																	<p><?php printf( __( 'Select %s. Leave empty to show Notification Bar on all %s', $this->plugin_name ), $object->labels->name, $object->labels->name ); ?></p>
																	<select multiple class="mtsnb-multi-select" name="mtsnb_fields[conditions][location][custom_<?php echo $name; ?>][]" id="mtsnb_fields_conditions_location_custom_<?php echo $name; ?>">
																		<?php
																		if ( !empty( $location_ct_all ) ) {
																			foreach ( $location_ct_all as $id => $name2 ) {

																				$selected =  in_array( $id, $location_ct_entries ) ? ' selected="selected"' : '';
																				?>
																				<option value="<?php echo esc_attr( $id );?>"<?php echo $selected; ?>><?php echo esc_html( $name2 ); ?></option>
																				<?php
																			}
																		}
																		?>
																	</select>
																</div>
																<?php
															}
														}
													}
												}
											}
											//////////////////// CPT & Tax Support /////////////////////////
											?>
										</div>
									</div>
								</div>
								<div id="condition-notlocation-panel" class="mtsnb-conditions-panel <?php echo $condition_notlocation_state; ?>">
									<div class="mtsnb-conditions-panel-title"><?php _e( 'Not on specific locations', $this->plugin_name ); ?></div>
									<div class="mtsnb-conditions-panel-content">
										<div class="mtsnb-conditions-panel-desc"><?php _e( 'Hide Notification Bar on the following locations', $this->plugin_name ); ?></div>
										<div class="mtsnb-conditions-panel-opt">
											<?php $notlocation_home       = isset( $value['conditions'] ) && isset( $value['conditions']['notlocation'] ) && ( isset( $value['conditions']['notlocation']['home'] ) && !empty( $value['conditions']['notlocation']['home'] ) ) ? $value['conditions']['notlocation']['home'] : '0'; ?>
											<?php $notlocation_blog_home  = isset( $value['conditions'] ) && isset( $value['conditions']['notlocation'] ) && ( isset( $value['conditions']['notlocation']['blog_home'] ) && !empty( $value['conditions']['notlocation']['blog_home'] ) ) ? $value['conditions']['notlocation']['blog_home'] : '0'; ?>
											<?php $notlocation_pages      = isset( $value['conditions'] ) && isset( $value['conditions']['notlocation'] ) && ( isset( $value['conditions']['notlocation']['pages'] ) && !empty( $value['conditions']['notlocation']['pages'] ) ) ? $value['conditions']['notlocation']['pages'] : '0'; ?>
											<?php $notlocation_posts      = isset( $value['conditions'] ) && isset( $value['conditions']['notlocation'] ) && ( isset( $value['conditions']['notlocation']['posts'] ) && !empty( $value['conditions']['notlocation']['posts'] ) ) ? $value['conditions']['notlocation']['posts'] : '0'; ?>
											<?php $notlocation_categories = isset( $value['conditions'] ) && isset( $value['conditions']['notlocation'] ) && ( isset( $value['conditions']['notlocation']['categories'] ) && !empty( $value['conditions']['notlocation']['categories'] ) ) ? $value['conditions']['notlocation']['categories'] : '0'; ?>
											<?php $notlocation_tags       = isset( $value['conditions'] ) && isset( $value['conditions']['notlocation'] ) && ( isset( $value['conditions']['notlocation']['tags'] ) && !empty( $value['conditions']['notlocation']['tags'] ) ) ? $value['conditions']['notlocation']['tags'] : '0'; ?>
											<?php $notlocation_date       = isset( $value['conditions'] ) && isset( $value['conditions']['notlocation'] ) && ( isset( $value['conditions']['notlocation']['date'] ) && !empty( $value['conditions']['notlocation']['date'] ) ) ? $value['conditions']['notlocation']['date'] : '0'; ?>
											<?php $notlocation_author     = isset( $value['conditions'] ) && isset( $value['conditions']['notlocation'] ) && ( isset( $value['conditions']['notlocation']['author'] ) && !empty( $value['conditions']['notlocation']['author'] ) ) ? $value['conditions']['notlocation']['author'] : '0'; ?>
											<?php $notlocation_404        = isset( $value['conditions'] ) && isset( $value['conditions']['notlocation'] ) && ( isset( $value['conditions']['notlocation']['404'] ) && !empty( $value['conditions']['notlocation']['404'] ) ) ? $value['conditions']['notlocation']['404'] : '0'; ?>
											<?php $notlocation_search     = isset( $value['conditions'] ) && isset( $value['conditions']['notlocation'] ) && ( isset( $value['conditions']['notlocation']['search'] ) && !empty( $value['conditions']['notlocation']['search'] ) ) ? $value['conditions']['notlocation']['search'] : '0'; ?>
											<?php $notlocation_custom     = isset( $value['conditions'] ) && isset( $value['conditions']['notlocation'] ) && ( isset( $value['conditions']['notlocation']['custom'] ) && !empty( $value['conditions']['notlocation']['custom'] ) ) ? $value['conditions']['notlocation']['custom'] : '0'; ?>
											<p>
												<label>
													<input type="checkbox" class="mtsnb-checkbox" name="mtsnb_fields[conditions][notlocation][home]" id="mtsnb_fields_conditions_notlocation_home" value="1" <?php checked( $notlocation_home, '1', true ); ?> />
													<?php _e( 'Homepage.', $this->plugin_name ); ?>
												</label>
											</p>
											<?php if ( 'page' === get_option('show_on_front') && '0' !== get_option('page_for_posts') && '0' !== get_option('page_on_front') ) { ?>
												<p>
													<label>
														<input type="checkbox" class="mtsnb-checkbox" name="mtsnb_fields[conditions][notlocation][blog_home]" id="mtsnb_fields_conditions_notlocation_blog_home" value="1" <?php checked( $notlocation_blog_home, '1', true ); ?> />
														<?php _e( 'Blog Homepage.', $this->plugin_name ); ?>
													</label>
												</p>
											<?php } ?>
											<p>
												<label>
													<input type="checkbox" class="mtsnb-checkbox mtsnb-checkbox-toggle" name="mtsnb_fields[conditions][notlocation][pages]" id="mtsnb_fields_conditions_notlocation_pages" value="1" <?php checked( $notlocation_pages, '1', true ); ?> />
													<?php _e( 'Pages.', $this->plugin_name ); ?>
												</label>
											</p>
											<div class="mtsnb-conditions-panel-opt <?php if ( '1' === $notlocation_pages ) echo 'active'; ?>" data-checkbox="mtsnb_fields_conditions_notlocation_pages">
												<?php $notlocation_pages_all =  $this->page_select_options; ?>
												<?php $notlocation_pages_entries = isset( $value['conditions'] ) && isset( $value['conditions']['notlocation'] ) && ( isset( $value['conditions']['notlocation']['custom_pages'] ) && !empty( $value['conditions']['notlocation']['custom_pages'] ) ) ? $value['conditions']['notlocation']['custom_pages'] : array(); ?>
												<p><?php _e( 'Select pages. Leave empty to hide Notification Bar on all pages', $this->plugin_name ); ?></p>
												<select multiple class="mtsnb-multi-select" name="mtsnb_fields[conditions][notlocation][custom_pages][]" id="mtsnb_fields_conditions_notlocation_custom_pages">
													<?php
													if ( !empty( $notlocation_pages_all ) ) {
														foreach ( $notlocation_pages_all as $id => $name ) {

															$selected =  in_array( $id, $notlocation_pages_entries ) ? ' selected="selected"' : '';
															?>
															<option value="<?php echo esc_attr( $id );?>"<?php echo $selected; ?>><?php echo esc_html( $name ); ?></option>
															<?php
														}
													}
													?>
												</select>
											</div>
											<p>
												<label>
													<input type="checkbox" class="mtsnb-checkbox mtsnb-checkbox-toggle" name="mtsnb_fields[conditions][notlocation][posts]" id="mtsnb_fields_conditions_notlocation_posts" value="1" <?php checked( $notlocation_posts, '1', true ); ?> />
													<?php _e( 'Posts.', $this->plugin_name ); ?>
												</label>
											</p>
											<div class="mtsnb-conditions-panel-opt <?php if ( '1' === $notlocation_posts ) echo 'active'; ?>" data-checkbox="mtsnb_fields_conditions_notlocation_posts">
												<?php
												$notlocation_posts_entries = isset( $value['conditions'] ) && isset( $value['conditions']['notlocation'] ) && ( isset( $value['conditions']['notlocation']['custom_posts'] ) && !empty( $value['conditions']['notlocation']['custom_posts'] ) ) ? $value['conditions']['notlocation']['custom_posts'] : '';
												if ( is_array( $notlocation_posts_entries ) ) {
													$notlocation_posts_entries = implode (', ', $notlocation_posts_entries );
												}
												?>
												<p><?php _e( 'Enter comma separated list of post ids. Leave empty to hide Notification Bar on all posts', $this->plugin_name ); ?></p>
												<input type="text" name="mtsnb_fields[conditions][notlocation][custom_posts]" id="mtsnb_fields_conditions_notlocation_custom_posts" value="<?php echo esc_attr( $notlocation_posts_entries );?>" />
											</div>
											<p>
												<label>
													<input type="checkbox" class="mtsnb-checkbox mtsnb-checkbox-toggle" name="mtsnb_fields[conditions][notlocation][categories]" id="mtsnb_fields_conditions_notlocation_categories" value="1" <?php checked( $notlocation_categories, '1', true ); ?> />
													<?php _e( 'Post category archives.', $this->plugin_name ); ?>
												</label>
											</p>
											<div class="mtsnb-conditions-panel-opt <?php if ( '1' === $notlocation_categories ) echo 'active'; ?>" data-checkbox="mtsnb_fields_conditions_notlocation_categories">
												<?php $notlocation_cats_all =  $this->category_select_options; ?>
												<?php $notlocation_cats_entries = isset( $value['conditions'] ) && isset( $value['conditions']['notlocation'] ) && ( isset( $value['conditions']['notlocation']['custom_categories'] ) && !empty( $value['conditions']['notlocation']['custom_categories'] ) ) ? $value['conditions']['notlocation']['custom_categories'] : array(); ?>
												<p><?php _e( 'Select categories. Leave empty to hide Notification Bar on all categories', $this->plugin_name ); ?></p>
												<select multiple class="mtsnb-multi-select" name="mtsnb_fields[conditions][notlocation][custom_categories][]" id="mtsnb_fields_conditions_notlocation_custom_categories">
													<?php
													if ( !empty( $notlocation_cats_all ) ) {
														foreach ( $notlocation_cats_all as $id => $name ) {

															$selected =  in_array( $id, $notlocation_cats_entries ) ? ' selected="selected"' : '';
															?>
															<option value="<?php echo esc_attr( $id );?>"<?php echo $selected; ?>><?php echo esc_html( $name ); ?></option>
															<?php
														}
													}
													?>
												</select>
											</div>
											<p>
												<label>
													<input type="checkbox" class="mtsnb-checkbox mtsnb-checkbox-toggle" name="mtsnb_fields[conditions][notlocation][tags]" id="mtsnb_fields_conditions_notlocation_tags" value="1" <?php checked( $notlocation_tags, '1', true ); ?> />
													<?php _e( 'Post tag archives.', $this->plugin_name ); ?>
												</label>
											</p>
											<div class="mtsnb-conditions-panel-opt <?php if ( '1' === $notlocation_tags ) echo 'active'; ?>" data-checkbox="mtsnb_fields_conditions_notlocation_tags">
												<?php $notlocation_tags_all =  $this->post_tag_select_options; ?>
												<?php $notlocation_tags_entries = isset( $value['conditions'] ) && isset( $value['conditions']['notlocation'] ) && ( isset( $value['conditions']['notlocation']['custom_tags'] ) && !empty( $value['conditions']['notlocation']['custom_tags'] ) ) ? $value['conditions']['notlocation']['custom_tags'] : array(); ?>
												<p><?php _e( 'Select tags. Leave empty to hide Notification Bar on all tags', $this->plugin_name ); ?></p>
												<select multiple class="mtsnb-multi-select" name="mtsnb_fields[conditions][notlocation][custom_tags][]" id="mtsnb_fields_conditions_notlocation_custom_tags">
													<?php
													if ( !empty( $notlocation_tags_all ) ) {
														foreach ( $notlocation_tags_all as $id => $name ) {

															$selected =  in_array( $id, $notlocation_tags_entries ) ? ' selected="selected"' : '';
															?>
															<option value="<?php echo esc_attr( $id );?>"<?php echo $selected; ?>><?php echo esc_html( $name ); ?></option>
															<?php
														}
													}
													?>
												</select>
											</div>
											<p>
												<label>
													<input type="checkbox" class="mtsnb-checkbox" name="mtsnb_fields[conditions][notlocation][date]" id="mtsnb_fields_conditions_notlocation_date" value="1" <?php checked( $notlocation_date, '1', true ); ?> />
													<?php _e( 'Date archives.', $this->plugin_name ); ?>
												</label>
											</p>
											<p>
												<label>
													<input type="checkbox" class="mtsnb-checkbox" name="mtsnb_fields[conditions][notlocation][author]" id="mtsnb_fields_conditions_notlocation_author" value="1" <?php checked( $notlocation_author, '1', true ); ?> />
													<?php _e( 'Author archives.', $this->plugin_name ); ?>
												</label>
											</p>
											<p>
												<label>
													<input type="checkbox" class="mtsnb-checkbox" name="mtsnb_fields[conditions][notlocation][404]" id="mtsnb_fields_conditions_notlocation_404" value="1" <?php checked( $notlocation_404, '1', true ); ?> />
													<?php _e( 'Error 404 page.', $this->plugin_name ); ?>
												</label>
											</p>
											<p>
												<label>
													<input type="checkbox" class="mtsnb-checkbox" name="mtsnb_fields[conditions][notlocation][search]" id="mtsnb_fields_conditions_notlocation_search" value="1" <?php checked( $notlocation_search, '1', true ); ?> />
													<?php _e( 'Search results page.', $this->plugin_name ); ?>
												</label>
											</p>
											<p>
												<label>
													<input type="checkbox" class="mtsnb-checkbox mtsnb-checkbox-toggle" name="mtsnb_fields[conditions][notlocation][custom]" id="mtsnb_fields_conditions_notlocation_custom" value="1" <?php checked( $notlocation_custom, '1', true ); ?> />
													<?php _e( 'Custom.', $this->plugin_name ); ?>
												</label>
											</p>
											<div class="mtsnb-conditions-panel-opt <?php if ( '1' === $notlocation_custom ) echo 'active'; ?>" data-checkbox="mtsnb_fields_conditions_notlocation_custom">
												<?php $notlocation_entries = isset( $value['conditions'] ) && isset( $value['conditions']['notlocation'] ) && ( isset( $value['conditions']['notlocation']['data'] ) && !empty( $value['conditions']['notlocation']['data'] ) ) ? $value['conditions']['notlocation']['data'] : array(); ?>
												<p><?php _e( 'Enter custom URLs, type full URL and hit enter/space to separate entries.', $this->plugin_name ); ?></p>
												<select multiple class="mtsnb-multi-text" name="mtsnb_fields[conditions][notlocation][data][]" id="mtsnb_fields_conditions_notlocation_data">
													<?php
													if ( !empty( $notlocation_entries ) ) {
														foreach ( $notlocation_entries as $entry ) {
															?>
															<option value="<?php echo esc_attr( $entry );?>" selected="selected"><?php echo esc_html( $entry ); ?></option>
															<?php
														}
													}
													?>
												</select>
											</div>
											<?php
											//////////////////// CPT & Tax Support /////////////////////////
											if ( !empty( $cpt_supp ) ) {
												foreach ( $cpt_supp as $cpt ) {
													$notlocation_cpt  = isset( $value['conditions'] ) && isset( $value['conditions']['notlocation'] ) && ( isset( $value['conditions']['notlocation'][ $cpt ] ) && !empty( $value['conditions']['notlocation'][ $cpt ] ) ) ? $value['conditions']['notlocation'][ $cpt ] : '0';
													if ( isset( $this->supported_custom_post_types[ $cpt ] ) ) {
													?>
														<p>
															<label>
																<input type="checkbox" class="mtsnb-checkbox mtsnb-checkbox-toggle" name="mtsnb_fields[conditions][notlocation][<?php echo $cpt; ?>]" id="mtsnb_fields_conditions_notlocation_<?php echo $cpt; ?>" value="1" <?php checked( $notlocation_cpt, '1', true ); ?> />
																<?php echo $this->supported_custom_post_types[ $cpt ]; ?>
															</label>
														</p>
														<div class="mtsnb-conditions-panel-opt <?php if ( '1' === $notlocation_cpt ) echo 'active'; ?>" data-checkbox="mtsnb_fields_conditions_notlocation_<?php echo $cpt; ?>">
															<?php
															$notlocation_cpt_entries = isset( $value['conditions'] ) && isset( $value['conditions']['notlocation'] ) && ( isset( $value['conditions']['notlocation'][ 'custom_'.$cpt ] ) && !empty( $value['conditions']['notlocation'][ 'custom_'.$cpt ] ) ) ? $value['conditions']['notlocation'][ 'custom_'.$cpt ] : '';
															if ( is_array( $notlocation_cpt_entries ) ) {
																$notlocation_cpt_entries = implode (', ', $notlocation_cpt_entries );
															}
															?>
															<p><?php printf( __( 'Enter comma separated list of ids. Leave empty to hide Notification Bar on all %s', $this->plugin_name ), $this->supported_custom_post_types[ $cpt ] ); ?></p>
															<input type="text" name="mtsnb_fields[conditions][notlocation][custom_<?php echo $cpt; ?>]" id="mtsnb_fields_conditions_notlocation_custom_<?php echo $cpt; ?>" value="<?php echo esc_attr( $notlocation_cpt_entries );?>" />
														</div>
														<?php
														if ( isset( $this->supported_custom_taxonomies[ $cpt ] ) ) {

															foreach ( $this->supported_custom_taxonomies[ $cpt ] as $name => $object ) {

																$notlocation_ct  = isset( $value['conditions'] ) && isset( $value['conditions']['notlocation'] ) && ( isset( $value['conditions']['notlocation'][ $name ] ) && !empty( $value['conditions']['notlocation'][ $name ] ) ) ? $value['conditions']['notlocation'][ $name ] : '0';
																?>
																<p>
																	<label>
																		<input type="checkbox" class="mtsnb-checkbox mtsnb-checkbox-toggle" name="mtsnb_fields[conditions][notlocation][<?php echo $name; ?>]" id="mtsnb_fields_conditions_notlocation_<?php echo $name; ?>" value="1" <?php checked( $notlocation_ct, '1', true ); ?> />
																		<?php printf( __( '%s archives.', $this->plugin_name ), $object->labels->singular_name ); ?>
																	</label>
																</p>
																<div class="mtsnb-conditions-panel-opt <?php if ( '1' === $notlocation_ct ) echo 'active'; ?>" data-checkbox="mtsnb_fields_conditions_notlocation_<?php echo $name; ?>">
																	<?php $notlocation_ct_all =  $this->custom_taxonomies_select_options[ $cpt ][ $name ]; ?>
																	<?php $notlocation_ct_entries = isset( $value['conditions'] ) && isset( $value['conditions']['notlocation'] ) && ( isset( $value['conditions']['notlocation']['custom_'.$name] ) && !empty( $value['conditions']['notlocation']['custom_'.$name] ) ) ? $value['conditions']['notlocation']['custom_'.$name] : array(); ?>
																	<p><?php printf( __( 'Select %s. Leave empty to hide Notification Bar on all %s', $this->plugin_name ), $object->labels->name, $object->labels->name ); ?></p>
																	<select multiple class="mtsnb-multi-select" name="mtsnb_fields[conditions][notlocation][custom_<?php echo $name; ?>][]" id="mtsnb_fields_conditions_notlocation_custom_<?php echo $name; ?>">
																		<?php
																		if ( !empty( $notlocation_ct_all ) ) {
																			foreach ( $notlocation_ct_all as $id => $name2 ) {

																				$selected =  in_array( $id, $notlocation_ct_entries ) ? ' selected="selected"' : '';
																				?>
																				<option value="<?php echo esc_attr( $id );?>"<?php echo $selected; ?>><?php echo esc_html( $name2 ); ?></option>
																				<?php
																			}
																		}
																		?>
																	</select>
																</div>
																<?php
															}
														}
													}
												}
											}
											//////////////////// CPT & Tax Support /////////////////////////
											?>
										</div>
									</div>
								</div>
								<div id="condition-logged-panel" class="mtsnb-conditions-panel <?php echo $condition_logged_state; ?>">
									<div class="mtsnb-conditions-panel-title"><?php _e( 'Visitor is logged in', $this->plugin_name ); ?></div>
									<div class="mtsnb-conditions-panel-content">
										<div class="mtsnb-conditions-panel-desc"><?php _e( 'Show Notification Bar only for logged in visitors', $this->plugin_name ); ?></div>
									</div>
								</div>
								<div id="condition-notlogged-panel" class="mtsnb-conditions-panel <?php echo $condition_notlogged_state; ?>">
									<div class="mtsnb-conditions-panel-title"><?php _e( 'Visitor is not logged in', $this->plugin_name ); ?></div>
									<div class="mtsnb-conditions-panel-content">
										<div class="mtsnb-conditions-panel-desc"><?php _e( 'Show Notification Bar only for not logged in visitors', $this->plugin_name ); ?></div>
									</div>
								</div>
								<div id="condition-less-panel" class="mtsnb-conditions-panel <?php echo $condition_less_state; ?>">
									<div class="mtsnb-conditions-panel-title"><?php _e( 'Show N times', $this->plugin_name ); ?></div>
									<div class="mtsnb-conditions-panel-content">
										<div class="mtsnb-conditions-panel-desc">
											<?php $less_times = isset( $value['conditions'] ) && isset( $value['conditions']['less'] ) && ( isset( $value['conditions']['less']['data'] ) && !empty( $value['conditions']['less']['data'] ) ) ? $value['conditions']['less']['data'] : '1'; ?>
											<?php _e( 'Show Notification Bar', $this->plugin_name) ?>
											<input type="number" step="1" min="1" name="mtsnb_fields[conditions][less][data]" id="mtsnb_fields_conditions_less_data" value="<?php echo $less_times;?>" class="small-text"/> 
											<?php _e( 'times for each visitor.', $this->plugin_name ); ?>
										</div>
									</div>
								</div>
								<div id="condition-after-panel" class="mtsnb-conditions-panel <?php echo $condition_after_state; ?>">
									<div class="mtsnb-conditions-panel-title"><?php _e( 'Show after N visits', $this->plugin_name ); ?></div>
									<div class="mtsnb-conditions-panel-content">
										<div class="mtsnb-conditions-panel-desc">
											<?php $after_times = isset( $value['conditions'] ) && isset( $value['conditions']['after'] ) && ( isset( $value['conditions']['after']['data'] ) && !empty( $value['conditions']['after']['data'] ) ) ? $value['conditions']['after']['data'] : '1'; ?>
											<?php _e( 'Show Notification Bar after', $this->plugin_name) ?>
											<input type="number" step="1" min="1" name="mtsnb_fields[conditions][after][data]" id="mtsnb_fields_conditions_after_data" value="<?php echo $after_times;?>" class="small-text"/> 
											<?php _e( 'visits.', $this->plugin_name ); ?>
										</div>
									</div>
								</div>
								<div id="condition-onmobile-panel" class="mtsnb-conditions-panel <?php echo $condition_onmobile_state; ?>">
									<div class="mtsnb-conditions-panel-title"><?php _e( 'Only on mobile devices', $this->plugin_name ); ?></div>
									<div class="mtsnb-conditions-panel-content">
										<div class="mtsnb-conditions-panel-desc"><?php _e( 'Show Notification Bar to visitors that are using a mobile device.', $this->plugin_name ); ?></div>
									</div>
								</div>
								<div id="condition-notonmobile-panel" class="mtsnb-conditions-panel <?php echo $condition_notonmobile_state; ?>">
									<div class="mtsnb-conditions-panel-title"><?php _e( 'Not on mobile devices', $this->plugin_name ); ?></div>
									<div class="mtsnb-conditions-panel-content">
										<div class="mtsnb-conditions-panel-desc"><?php _e( 'Show Notification Bar to visitors that are using a normal computer or laptop', $this->plugin_name ); ?></div>
									</div>
								</div>
								<div id="condition-referrer-panel" class="mtsnb-conditions-panel <?php echo $condition_referrer_state; ?>">
									<div class="mtsnb-conditions-panel-title"><?php _e( 'From a specific referrer', $this->plugin_name ); ?></div>
									<div class="mtsnb-conditions-panel-content">
										<div class="mtsnb-conditions-panel-desc">
											<?php $referrer_search  = isset( $value['conditions'] ) && isset( $value['conditions']['referrer'] ) && ( isset( $value['conditions']['referrer']['search']) && !empty( $value['conditions']['referrer']['search'] ) ) ? $value['conditions']['referrer']['search'] : '0'; ?>
											<?php $referrer_custom  = isset( $value['conditions'] ) && isset( $value['conditions']['referrer'] ) && ( isset( $value['conditions']['referrer']['custom']) && !empty( $value['conditions']['referrer']['custom'] ) ) ? $value['conditions']['referrer']['custom'] : '0'; ?>
											<p>
												<label>
													<input type="checkbox" class="mtsnb-checkbox" name="mtsnb_fields[conditions][referrer][search]" id="mtsnb_fields_conditions_referrer_search" value="1" <?php checked( $referrer_search, '1', true ); ?> />
													<?php _e( 'Show Notification Bar if visitor arrived via a search engine.', $this->plugin_name ); ?>
												</label>
											</p>
											<p>
												<label>
													<input type="checkbox" class="mtsnb-checkbox mtsnb-checkbox-toggle" name="mtsnb_fields[conditions][referrer][custom]" id="mtsnb_fields_conditions_referrer_custom" value="1" <?php checked( $referrer_custom, '1', true ); ?> />
													<?php _e( 'Show Notification Bar if visitor arrived from a specific referrer.', $this->plugin_name ); ?>
												</label>
											</p>
										</div>
										<div class="mtsnb-conditions-panel-opt <?php if ( '1' === $referrer_custom ) echo 'active'; ?>" data-checkbox="mtsnb_fields_conditions_referrer_custom">
											<?php $referrer_entries = isset( $value['conditions'] ) && isset( $value['conditions']['referrer'] ) && ( isset( $value['conditions']['referrer']['data'] ) && !empty( $value['conditions']['referrer']['data'] ) ) ? $value['conditions']['referrer']['data'] : array(); ?>
											<p><?php _e( 'Enter custom refferrers. Can be full URL or a pattern like ".example.com", type and hit enter/space to separate entries.', $this->plugin_name ); ?></p>
											<select multiple class="mtsnb-multi-text" name="mtsnb_fields[conditions][referrer][data][]" id="mtsnb_fields_conditions_referrer_data">
												<?php
												if ( !empty( $referrer_entries ) ) {
													foreach ( $referrer_entries as $entry ) {
														?>
														<option value="<?php echo esc_attr( $entry );?>" selected="selected"><?php echo esc_html( $entry ); ?></option>
														<?php
													}
												}
												?>
											</select>
										</div>
									</div>
								</div>
								<div id="condition-notreferrer-panel" class="mtsnb-conditions-panel <?php echo $condition_notreferrer_state; ?>">
									<div class="mtsnb-conditions-panel-title"><?php _e( 'Not from a specific referrer', $this->plugin_name ); ?></div>
									<div class="mtsnb-conditions-panel-content">
										<div class="mtsnb-conditions-panel-desc">
											<?php $notreferrer_search  = isset( $value['conditions'] ) && isset( $value['conditions']['notreferrer'] ) && ( isset( $value['conditions']['notreferrer']['search']) && !empty( $value['conditions']['notreferrer']['search'] ) ) ? $value['conditions']['notreferrer']['search'] : '0'; ?>
											<?php $notreferrer_custom  = isset( $value['conditions'] ) && isset( $value['conditions']['notreferrer'] ) && ( isset( $value['conditions']['notreferrer']['custom']) && !empty( $value['conditions']['notreferrer']['custom'] ) ) ? $value['conditions']['notreferrer']['custom'] : '0'; ?>
											<p>
												<label>
													<input type="checkbox" class="mtsnb-checkbox" name="mtsnb_fields[conditions][notreferrer][search]" id="mtsnb_fields_conditions_notreferrer_search" value="1" <?php checked( $notreferrer_search, '1', true ); ?> />
													<?php _e( 'Hide Notification Bar if visitor arrived via a search engine.', $this->plugin_name ); ?>
												</label>
											</p>
											<p>
												<label>
													<input type="checkbox" class="mtsnb-checkbox mtsnb-checkbox-toggle" name="mtsnb_fields[conditions][notreferrer][custom]" id="mtsnb_fields_conditions_notreferrer_custom" value="1" <?php checked( $notreferrer_custom, '1', true ); ?> />
													<?php _e( 'Hide Notification Bar if visitor arrived from a specific referrer.', $this->plugin_name ); ?>
												</label>
											</p>
										</div>
										<div class="mtsnb-conditions-panel-opt <?php if ( '1' === $notreferrer_custom ) echo 'active'; ?>" data-checkbox="mtsnb_fields_conditions_notreferrer_custom">
											<?php $notreferrer_entries = isset( $value['conditions'] ) && isset( $value['conditions']['notreferrer'] ) && ( isset( $value['conditions']['notreferrer']['data'] ) && !empty( $value['conditions']['notreferrer']['data'] ) ) ? $value['conditions']['notreferrer']['data'] : array(); ?>
											<p><?php _e( 'Enter custom refferrers. Can be full URL or a pattern like ".example.com", type and hit enter/space to separate entries.', $this->plugin_name ); ?></p>
											<select multiple class="mtsnb-multi-text" name="mtsnb_fields[conditions][notreferrer][data][]" id="mtsnb_fields_conditions_notreferrer_data">
												<?php
												if ( !empty( $notreferrer_entries ) ) {
													foreach ( $notreferrer_entries as $entry ) {
														?>
														<option value="<?php echo esc_attr( $entry );?>" selected="selected"><?php echo esc_html( $entry ); ?></option>
														<?php
													}
												}
												?>
											</select>
										</div>
									</div>
								</div>
								<div id="condition-utm-panel" class="mtsnb-conditions-panel <?php echo $condition_utm_state; ?>">
									<div class="mtsnb-conditions-panel-title"><?php _e( 'With specific UTM tags', $this->plugin_name ); ?></div>
									<div class="mtsnb-conditions-panel-content">
										<div class="mtsnb-conditions-panel-desc">
											<?php $utm_operator = isset( $value['conditions'] ) && isset( $value['conditions']['utm'] ) && ( isset( $value['conditions']['utm']['operator'] ) && !empty( $value['conditions']['utm']['operator'] ) ) ? $value['conditions']['utm']['operator'] : 'or'; ?>
											<?php _e( 'Show Notification Bar if URL contains', $this->plugin_name); ?>
												<select name="mtsnb_fields[conditions][utm][operator]" id="mtsnb_fields_conditions_utm_operator" value="<?php echo $utm_operator;?>" class="mtsnb-small-select">
													<option value="or" <?php selected( $utm_operator, 'or', true); ?>><?php _e( 'Any', $this->plugin_name ); ?></option>
													<option value="and" <?php selected( $utm_operator, 'and', true); ?>><?php _e( 'Every', $this->plugin_name ); ?></option>
												</select>
											<?php _e( 'value entered below.', $this->plugin_name ); ?>
										</div>
										<div class="mtsnb-conditions-panel-opt">
											<?php
											$utm_default_tags = $this->get_default_utm_tags();
											$utm_default_entries = array();
											foreach ( $utm_default_tags as $key => $tag ) {
												$utm_default_entries[ $key ]['name'] = $tag;
												$utm_default_entries[ $key ]['value'] = '';
											}
											$utm_entries = isset( $value['conditions'] ) && isset( $value['conditions']['utm'] ) && ( isset( $value['conditions']['utm']['tags'] ) && !empty( $value['conditions']['utm']['tags'] ) ) ? $value['conditions']['utm']['tags'] : $utm_default_entries;
											$utm_count = 0;
											?>
											<p class="mtsnb-utm-tags">
												<?php
												foreach ( $utm_entries as $key => $entry ) {
												?>
													<label class="mtsnb-utm-label">
														<span class="utm-text"><?php echo $entry['name'];?> = </span>
														<input type="hidden" name="mtsnb_fields[conditions][utm][tags][<?php echo $utm_count;?>][name]" id="mtsnb_fields_conditions_utm_tags_<?php echo $utm_count;?>_name" value="<?php echo $entry['name'];?>" />
														<input type="text" name="mtsnb_fields[conditions][utm][tags][<?php echo $utm_count;?>][value]" id="mtsnb_fields_conditions_utm_tags_<?php echo $utm_count;?>_value" value="<?php echo $entry['value'];?>" />
														<?php if( !in_array( $entry['name'], $utm_default_tags ) ) { ?><span class="mtsnb-remove-utm-tag"><i class="fa fa-close"></i></span><?php } ?>
													</label>
												<?php
												$utm_count++;
												}
												?>
											</p>
											<p>
												<label><span class="utm-text"><?php _e( 'Add Custom:', $this->plugin_name ); ?></span>
													<input type="text" class="mtsnb-add-utm-tag-input" value="" placeholder=""/>
												</label>
												<button type="button" role="button" class="mtsnb-add-utm-tag button" disabled>
													<i class="fa fa-plus"></i> <?php _e('Add', $this->plugin_name ); ?>
												</button>
											</p>
										</div>
									</div>
								</div>
								<div id="condition-notutm-panel" class="mtsnb-conditions-panel <?php echo $condition_notutm_state; ?>">
									<div class="mtsnb-conditions-panel-title"><?php _e( 'Without specific UTM tags', $this->plugin_name ); ?></div>
									<div class="mtsnb-conditions-panel-content">
										<div class="mtsnb-conditions-panel-desc">
											<?php $notutm_operator = isset( $value['conditions'] ) && isset( $value['conditions']['notutm'] ) && ( isset( $value['conditions']['notutm']['operator'] ) && !empty( $value['conditions']['notutm']['operator'] ) ) ? $value['conditions']['notutm']['operator'] : 'or'; ?>
											<?php _e( 'Hide Notification Bar if URL contains', $this->plugin_name); ?>
												<select name="mtsnb_fields[conditions][notutm][operator]" id="mtsnb_fields_conditions_notutm_operator" value="<?php echo $notutm_operator;?>" class="mtsnb-small-select">
													<option value="or" <?php selected( $notutm_operator, 'or', true); ?>><?php _e( 'Any', $this->plugin_name ); ?></option>
													<option value="and" <?php selected( $notutm_operator, 'and', true); ?>><?php _e( 'Every', $this->plugin_name ); ?></option>
												</select>
											<?php _e( 'value entered below.', $this->plugin_name ); ?>
										</div>
										<div class="mtsnb-conditions-panel-opt">
											<?php
											$notutm_default_tags = $this->get_default_utm_tags();
											$notutm_default_entries = array();
											foreach ( $notutm_default_tags as $key => $tag ) {
												$notutm_default_entries[ $key ]['name'] = $tag;
												$notutm_default_entries[ $key ]['value'] = '';
											}
											$notutm_entries = isset( $value['conditions'] ) && isset( $value['conditions']['notutm'] ) && ( isset( $value['conditions']['notutm']['tags'] ) && !empty( $value['conditions']['notutm']['tags'] ) ) ? $value['conditions']['notutm']['tags'] : $notutm_default_entries;
											$notutm_count = 0;
											?>
											<p class="mtsnb-utm-tags">
												<?php
												foreach ( $notutm_entries as $key => $entry ) {
												?>
													<label class="mtsnb-utm-label">
														<span class="utm-text"><?php echo $entry['name'];?> = </span>
														<input type="hidden" name="mtsnb_fields[conditions][notutm][tags][<?php echo $notutm_count;?>][name]" id="mtsnb_fields_conditions_notutm_tags_<?php echo $notutm_count;?>_name" value="<?php echo $entry['name'];?>" />
														<input type="text" name="mtsnb_fields[conditions][notutm][tags][<?php echo $notutm_count;?>][value]" id="mtsnb_fields_conditions_notutm_tags_<?php echo $notutm_count;?>_value" value="<?php echo $entry['value'];?>" />
														<?php if( !in_array( $entry['name'], $notutm_default_tags ) ) { ?><span class="mtsnb-remove-utm-tag"><i class="fa fa-close"></i></span><?php } ?>
													</label>
												<?php
												$notutm_count++;
												}
												?>
											</p>
											<p>
												<label><span class="utm-text"><?php _e( 'Add Custom:', $this->plugin_name ); ?></span>
													<input type="text" class="mtsnb-add-utm-tag-input" value="" placeholder=""/>
												</label>
												<button type="button" role="button" class="mtsnb-add-utm-tag button notutm-button" disabled>
													<i class="fa fa-plus"></i> <?php _e('Add', $this->plugin_name ); ?>
												</button>
											</p>
										</div>
									</div>
								</div>
								<div id="condition-time-panel" class="mtsnb-conditions-panel <?php echo $condition_time_state; ?>">
									<div class="mtsnb-conditions-panel-title"><?php _e( 'Show from/to time', $this->plugin_name ); ?></div>
									<div class="mtsnb-conditions-panel-content">
										<div class="mtsnb-conditions-panel-desc">
											<?php $show_from_time_enabled  = isset( $value['conditions'] ) && isset( $value['conditions']['show_from_time'] ) && ( isset( $value['conditions']['show_from_time']['enabled']) && !empty( $value['conditions']['show_from_time']['enabled'] ) ) ? $value['conditions']['show_from_time']['enabled'] : '0'; ?>
											<p>
												<label>
													<input type="checkbox" class="mtsnb-checkbox mtsnb-checkbox-toggle" name="mtsnb_fields[conditions][show_from_time][enabled]" id="mtsnb_fields_conditions_show_from_time_enabled" value="1" <?php checked( $show_from_time_enabled, '1', true ); ?> />
													<?php _e( 'Show Notification Bar starting from selected date and time.', $this->plugin_name ); ?>
												</label>
											</p>
										</div>
										<div class="mtsnb-conditions-panel-opt <?php if ( '1' === $show_from_time_enabled ) echo 'active'; ?>" data-checkbox="mtsnb_fields_conditions_show_from_time_enabled">
											<?php $show_from_time_date = isset( $value['conditions'] ) && isset( $value['conditions']['show_from_time'] ) && ( isset( $value['conditions']['show_from_time']['date'] ) && !empty( $value['conditions']['show_from_time']['date'] ) ) ? $value['conditions']['show_from_time']['date'] : ''; ?>
											<?php $show_from_time_time = isset( $value['conditions'] ) && isset( $value['conditions']['show_from_time'] ) && ( isset( $value['conditions']['show_from_time']['time'] ) && !empty( $value['conditions']['show_from_time']['time'] ) ) ? $value['conditions']['show_from_time']['time'] : ''; ?>
											<p><label for="mtsnb_fields_conditions_show_from_time_date"><?php _e( 'Date (required):', $this->plugin_name ); ?></label></p>
											<input class="mtsnb-condition-datepicker" type="text" name="mtsnb_fields[conditions][show_from_time][date]" id="mtsnb_fields_conditions_show_from_time_date" value="<?php echo $show_from_time_date;?>" size="30" />
											<p><label for="mtsnb_fields_conditions_show_from_time_time"><?php _e( 'Time (optional):', $this->plugin_name ); ?></label></p>
											<input class="mtsnb-timepicker" type="text" name="mtsnb_fields[conditions][show_from_time][time]" id="mtsnb_fields_conditions_show_from_time_time" value="<?php echo $show_from_time_time;?>" size="30" />
										</div>
										<div class="mtsnb-conditions-panel-desc">
											<?php $show_to_time_enabled  = isset( $value['conditions'] ) && isset( $value['conditions']['show_to_time'] ) && ( isset( $value['conditions']['show_to_time']['enabled']) && !empty( $value['conditions']['show_to_time']['enabled'] ) ) ? $value['conditions']['show_to_time']['enabled'] : '0'; ?>
											<p>
												<label>
													<input type="checkbox" class="mtsnb-checkbox mtsnb-checkbox-toggle" name="mtsnb_fields[conditions][show_to_time][enabled]" id="mtsnb_fields_conditions_show_to_time_enabled" value="1" <?php checked( $show_to_time_enabled, '1', true ); ?> />
													<?php _e( 'Show Notification Bar until selected date and time.', $this->plugin_name ); ?>
												</label>
											</p>
										</div>
										<div class="mtsnb-conditions-panel-opt <?php if ( '1' === $show_to_time_enabled ) echo 'active'; ?>" data-checkbox="mtsnb_fields_conditions_show_to_time_enabled">
											<?php $show_to_time_date = isset( $value['conditions'] ) && isset( $value['conditions']['show_to_time'] ) && ( isset( $value['conditions']['show_to_time']['date'] ) && !empty( $value['conditions']['show_to_time']['date'] ) ) ? $value['conditions']['show_to_time']['date'] : ''; ?>
											<?php $show_to_time_time = isset( $value['conditions'] ) && isset( $value['conditions']['show_to_time'] ) && ( isset( $value['conditions']['show_to_time']['time'] ) && !empty( $value['conditions']['show_to_time']['time'] ) ) ? $value['conditions']['show_to_time']['time'] : ''; ?>
											<p><label for="mtsnb_fields_conditions_show_to_time_date"><?php _e( 'Date (required):', $this->plugin_name ); ?></label></p>
											<input class="mtsnb-condition-datepicker" type="text" name="mtsnb_fields[conditions][show_to_time][date]" id="mtsnb_fields_conditions_show_to_time_date" value="<?php echo $show_to_time_date;?>" size="30" />
											<p><label for="mtsnb_fields_conditions_show_to_time_time"><?php _e( 'Time (optional):', $this->plugin_name ); ?></label></p>
											<input class="mtsnb-timepicker" type="text" name="mtsnb_fields[conditions][show_to_time][time]" id="mtsnb_fields_conditions_show_to_time_time" value="<?php echo $show_to_time_time;?>" size="30" />
										</div>
									</div>
								</div>
								<div id="condition-nottime-panel" class="mtsnb-conditions-panel <?php echo $condition_nottime_state; ?>">
									<div class="mtsnb-conditions-panel-title"><?php _e( 'Hide from/to time', $this->plugin_name ); ?></div>
									<div class="mtsnb-conditions-panel-content">
										<div class="mtsnb-conditions-panel-desc">
											<?php $hide_from_time_enabled  = isset( $value['conditions'] ) && isset( $value['conditions']['hide_from_time'] ) && ( isset( $value['conditions']['hide_from_time']['enabled']) && !empty( $value['conditions']['hide_from_time']['enabled'] ) ) ? $value['conditions']['hide_from_time']['enabled'] : '0'; ?>
											<p>
												<label>
													<input type="checkbox" class="mtsnb-checkbox mtsnb-checkbox-toggle" name="mtsnb_fields[conditions][hide_from_time][enabled]" id="mtsnb_fields_conditions_hide_from_time_enabled" value="1" <?php checked( $hide_from_time_enabled, '1', true ); ?> />
													<?php _e( 'Hide Notification Bar starting from selected date and time.', $this->plugin_name ); ?>
												</label>
											</p>
										</div>
										<div class="mtsnb-conditions-panel-opt <?php if ( '1' === $hide_from_time_enabled ) echo 'active'; ?>" data-checkbox="mtsnb_fields_conditions_hide_from_time_enabled">
											<?php $hide_from_time_date = isset( $value['conditions'] ) && isset( $value['conditions']['hide_from_time'] ) && ( isset( $value['conditions']['hide_from_time']['date'] ) && !empty( $value['conditions']['hide_from_time']['date'] ) ) ? $value['conditions']['hide_from_time']['date'] : ''; ?>
											<?php $hide_from_time_time = isset( $value['conditions'] ) && isset( $value['conditions']['hide_from_time'] ) && ( isset( $value['conditions']['hide_from_time']['time'] ) && !empty( $value['conditions']['hide_from_time']['time'] ) ) ? $value['conditions']['hide_from_time']['time'] : ''; ?>
											<p><label for="mtsnb_fields_conditions_hide_from_time_date"><?php _e( 'Date (required):', $this->plugin_name ); ?></label></p>
											<input class="mtsnb-condition-datepicker" type="text" name="mtsnb_fields[conditions][hide_from_time][date]" id="mtsnb_fields_conditions_hide_from_time_date" value="<?php echo $hide_from_time_date;?>" size="30" />
											<p><label for="mtsnb_fields_conditions_hide_from_time_time"><?php _e( 'Time (optional):', $this->plugin_name ); ?></label></p>
											<input class="mtsnb-timepicker" type="text" name="mtsnb_fields[conditions][hide_from_time][time]" id="mtsnb_fields_conditions_hide_from_time_time" value="<?php echo $hide_from_time_time;?>" size="30" />
										</div>
										<div class="mtsnb-conditions-panel-desc">
											<?php $hide_to_time_enabled  = isset( $value['conditions'] ) && isset( $value['conditions']['hide_to_time'] ) && ( isset( $value['conditions']['hide_to_time']['enabled']) && !empty( $value['conditions']['hide_to_time']['enabled'] ) ) ? $value['conditions']['hide_to_time']['enabled'] : '0'; ?>
											<p>
												<label>
													<input type="checkbox" class="mtsnb-checkbox mtsnb-checkbox-toggle" name="mtsnb_fields[conditions][hide_to_time][enabled]" id="mtsnb_fields_conditions_hide_to_time_enabled" value="1" <?php checked( $hide_to_time_enabled, '1', true ); ?> />
													<?php _e( 'Hide Notification Bar until selected date and time.', $this->plugin_name ); ?>
												</label>
											</p>
										</div>
										<div class="mtsnb-conditions-panel-opt <?php if ( '1' === $hide_to_time_enabled ) echo 'active'; ?>" data-checkbox="mtsnb_fields_conditions_hide_to_time_enabled">
											<?php $hide_to_time_date = isset( $value['conditions'] ) && isset( $value['conditions']['hide_to_time'] ) && ( isset( $value['conditions']['hide_to_time']['date'] ) && !empty( $value['conditions']['hide_to_time']['date'] ) ) ? $value['conditions']['hide_to_time']['date'] : ''; ?>
											<?php $hide_to_time_time = isset( $value['conditions'] ) && isset( $value['conditions']['hide_to_time'] ) && ( isset( $value['conditions']['hide_to_time']['time'] ) && !empty( $value['conditions']['hide_to_time']['time'] ) ) ? $value['conditions']['hide_to_time']['time'] : ''; ?>
											<p><label for="mtsnb_fields_conditions_hide_to_time_date"><?php _e( 'Date (required):', $this->plugin_name ); ?></label></p>
											<input class="mtsnb-condition-datepicker" type="text" name="mtsnb_fields[conditions][hide_to_time][date]" id="mtsnb_fields_conditions_hide_to_time_date" value="<?php echo $hide_to_time_date;?>" size="30" />
											<p><label for="mtsnb_fields_conditions_hide_to_time_time"><?php _e( 'Time (optional):', $this->plugin_name ); ?></label></p>
											<input class="mtsnb-timepicker" type="text" name="mtsnb_fields[conditions][hide_to_time][time]" id="mtsnb_fields_conditions_hide_to_time_time" value="<?php echo $hide_to_time_time;?>" size="30" />
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Helper function for common fields
	 *
	 * @since    1.0.0
	 */
	public function custom_meta_field( $args, $value, $b = false ) {

		$type = isset( $args['type'] ) ? $args['type'] : '';
		$name = isset( $args['name'] ) ? $args['name'] : '';
		$name = $b ? 'b_'.$name : $name;
		$label = isset( $args['label'] ) ? $args['label'] : '';
		$options = isset( $args['options'] ) ? $args['options'] : array();
		$default = isset( $args['default'] ) ? $args['default'] : '';
		$min = isset( $args['min'] ) ? $args['min'] : '0';

		$class = isset( $args['class'] ) ? $args['class'] : '';

		// For show/hide options based on select value
		if ( $b ) {
			$data_parent_select = isset( $args['parent_select'] ) ? ' data-parent-select-id="mtsnb_fields_b_'.$args['parent_select'].'"' : '';
		} else {
			$data_parent_select = isset( $args['parent_select'] ) ? ' data-parent-select-id="mtsnb_fields_'.$args['parent_select'].'"' : '';
		}
		
		$data_parent_value = '';
		if ( isset( $args['parent_value'] ) ) {
			$parent_values = '';
			if ( is_array( $args['parent_value'] ) ) {
				$parent_values='';
				foreach ( $args['parent_value'] as $val ) {

					$parent_values .= $val.',';
				}
			} else {
				$parent_values = $args['parent_value'];
			}

			$data_parent_value = ' data-parent-select-value="'.rtrim( $parent_values, ',' ).'"';
		}
		$parent_data = $data_parent_select . $data_parent_value;

		// Option value
		$opt_val = isset( $value[ $name ] ) ? $value[ $name ] : $default;

		?>
		<div id="mtsnb_fields_<?php echo $name;?>_row" class="form-row"<?php echo $parent_data; ?>>
			<label class="form-label" for="mtsnb_fields_<?php echo $name;?>"><?php echo $label; ?></label>
			<div class="form-option <?php echo $class; ?>">
			<?php
			switch ( $type ) {

				case 'text':
				?>
					<input type="text" name="mtsnb_fields[<?php echo $name;?>]" id="mtsnb_fields_<?php echo $name;?>" value="<?php echo esc_attr( $opt_val );?>" />
				<?php
				break;
				case 'select':
				?>
					<select name="mtsnb_fields[<?php echo $name;?>]" id="mtsnb_fields_<?php echo $name;?>">
					<?php foreach ( $options as $val => $label ) { ?>
						<option value="<?php echo $val; ?>" <?php selected( $opt_val, $val, true); ?>><?php echo $label ?></option>
					<?php } ?>
					</select>
				<?php
				break;
				case 'image':
				?>
					<div class="clearfix" id="mtsnb_fields_<?php echo $name.'_preview';?>">
					<?php
					if ( isset($opt_val['url']) && $opt_val['url'] != '' ) {
						echo '<img class="custom_media_image" src="' . $opt_val['url'] . '" style="margin:0 0 10px;padding:0;max-width:100%;height:auto;float:left;display:inline-block" />';
					}
					?>
					</div>
					<input type="hidden" id="mtsnb_fields_<?php echo $name.'_id';?>" name="mtsnb_fields[<?php echo $name;?>][id]" value="<?php if (isset($opt_val['id'])) echo $opt_val['id']; ?>" />
					<input type="hidden" id="mtsnb_fields_<?php echo $name.'_url';?>" name="mtsnb_fields[<?php echo $name;?>][url]" value="<?php if (isset($opt_val['url'])) echo $opt_val['url']; ?>" />
					<button class="button" name="mtsnb_fields_<?php echo $name.'_upload';?>" id="mtsnb_fields_<?php echo $name.'_upload';?>" data-id="<?php echo 'mtsnb_fields_'.$name; ?>" onclick="mtsImageField.uploader( '<?php echo 'mtsnb_fields_'.$name; ?>' ); return false;"><?php _e( 'Select Image', $this->plugin_name ); ?></button>
					<?php
					if ( isset( $opt_val['url'] ) && $opt_val['url'] != '' ) {
						echo '<a href="#" class="clear-image">' . __( 'Remove Image', $this->plugin_name ) . '</a>';
					}

				break;
				case 'number':
				?>
					<input type="number" step="1" min="<?php echo $min;?>" name="mtsnb_fields[<?php echo $name;?>]" id="mtsnb_fields_<?php echo $name;?>" value="<?php echo $opt_val;?>" class="small-text"/>
				<?php
				break;
				case 'color':
				?>
					<input type="text" name="mtsnb_fields[<?php echo $name;?>]" id="mtsnb_fields_<?php echo $name;?>" value="<?php echo $opt_val;?>" class="mtsnb-color-picker" />
				<?php
				break;
				case 'textarea':
				?>
					<textarea name="mtsnb_fields[<?php echo $name;?>]" id="mtsnb_fields_<?php echo $name;?>" class="mtsnb-textarea"><?php echo esc_textarea( $opt_val );?></textarea>
				<?php
				break;
				case 'checkbox':
				?>
					<input type="checkbox" name="mtsnb_fields[<?php echo $name;?>]" id="mtsnb_fields_<?php echo $name;?>" value="1" <?php checked( $opt_val, '1', true ); ?> />
				<?php
				break;
				case 'select_adv':
				?>
					<select multiple class="mtsnb-multi-select" name="mtsnb_fields[<?php echo $name;?>][]" id="mtsnb_fields_<?php echo $name;?>">
						<?php
						if ( !empty( $options ) ) {
							foreach ( $options as $id => $name ) {

								$selected =  in_array( $id, $opt_val ) ? ' selected="selected"' : '';
								?>
								<option value="<?php echo esc_attr( $id );?>"<?php echo $selected; ?>><?php echo esc_html( $name ); ?></option>
								<?php
							}
						}
						?>
					</select>
				<?php
				break;
				case 'select_icon':
				?>
					<select class="mtsnb-icon-select" name="mtsnb_fields[<?php echo $name;?>]" id="mtsnb_fields_<?php echo $name;?>">
						<option value=""<?php selected( $opt_val, '', true ); ?>><?php _e('No Icon', $this->plugin_name ); ?></option>
						<?php
						if ( !empty( $options ) ) {
							foreach ( $options as $icon_category => $icons ) {
						        echo '<optgroup label="'.$icon_category.'">';
						        foreach ($icons as $icon) {
						            echo '<option value="'.$icon.'"'.selected( $opt_val, $icon, false).'>'.ucwords(str_replace('-', ' ', $icon)).'</option>';
						        }
						        echo '</optgroup>';
							}
						}
						?>
					</select>
				<?php
				break;
				case 'ajax_list':
				?>
					<select class="mtsnb-ajax-select" name="mtsnb_fields[<?php echo $name;?>]" id="mtsnb_fields_<?php echo $name;?>" data-list="<?php echo $opt_val;?>"></select>
				<?php
				break;
				case 'ajax_client':
				?>
					<select class="mtsnb-ajax-select" name="mtsnb_fields[<?php echo $name;?>]" id="mtsnb_fields_<?php echo $name;?>" data-client="<?php echo $opt_val;?>"></select>
				<?php
				break;
				case 'aweber_actions':
				$aweber_name = $b ? 'b_aweber' : 'aweber';
				?>
					<a href="https://auth.aweber.com/1.0/oauth/authorize_app/e59c401b" target="_blank" class="button mtsnb-aweber-connect"><?php isset($value[$aweber_name]['access_key']) && $value[$aweber_name]['access_key'] == '' ? _e( 'Get Authorization Code', $this->plugin_name ) : _e( 'Reconnect Account', $this->plugin_name ); ?></a>
					<input type="hidden" id="mtsnb_fields_<?php echo $aweber_name;?>_consumer_key" name="mtsnb_fields[<?php echo $aweber_name;?>][consumer_key]" value="<?php echo (isset($value[$aweber_name]['consumer_key']) ? $value[$aweber_name]['consumer_key'] :''); ?>" />
					<input type="hidden" id="mtsnb_fields_<?php echo $aweber_name;?>_consumer_secret" name="mtsnb_fields[<?php echo $aweber_name;?>][consumer_secret]" value="<?php echo (isset($value[$aweber_name]['consumer_secret']) ? $value[$aweber_name]['consumer_secret'] :''); ?>" />
					<input type="hidden" id="mtsnb_fields_<?php echo $aweber_name;?>_access_key" name="mtsnb_fields[<?php echo $aweber_name;?>][access_key]" value="<?php echo (isset($value[$aweber_name]['access_key']) ? $value[$aweber_name]['access_key'] :''); ?>" />
					<input type="hidden" id="mtsnb_fields_<?php echo $aweber_name;?>_access_secret" name="mtsnb_fields[<?php echo $aweber_name;?>][access_secret]" value="<?php echo (isset($value[$aweber_name]['access_secret']) ? $value[$aweber_name]['access_secret'] :''); ?>" />
				<?php
				break;
				case 'date':
				?>
					<input class="mtsnb-datepicker" type="text" name="mtsnb_fields[<?php echo $name;?>]" id="mtsnb_fields_<?php echo $name;?>" value="<?php echo $opt_val;?>" size="30" />
				<?php
				break;
				case 'time':
				?>
					<input class="mtsnb-timepicker" type="text" name="mtsnb_fields[<?php echo $name;?>]" id="mtsnb_fields_<?php echo $name;?>" value="<?php echo $opt_val;?>" size="30" />
				<?php
				break;
				case 'info':
				?>
					<small class="mtsnb-option-info">
						<?php echo $default; ?>
					</small>
				<?php
				break;
			}
			?>
			</div>
		</div>
		<?php
	}

	/**
	 * Save the Data
	 *
	 * @since    1.0.0
	 */
	public function save_custom_meta( $post_id ) {
		// Check if our nonce is set.
		if ( ! isset( $_POST['mtsnb_meta_box_nonce'] ) ) {
			return;
		}
		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $_POST['mtsnb_meta_box_nonce'], 'mtsnb_meta_box' ) ) {
			return;
		}
		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		// Check the user's permissions.
		if ( isset( $_POST['post_type'] ) && 'mts_notification_bar' == $_POST['post_type'] ) {

			if ( ! current_user_can( 'edit_page', $post_id ) ) {
				return;
			}

		} else {

			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}
		}

		/* OK, it's safe for us to save the data now. */
		if ( ! isset( $_POST['mtsnb_fields'] ) ) {
			return;
		}

		$my_data = $_POST['mtsnb_fields'];

		$value = get_post_meta( $post_id, '_mtsnb_data', true );

		// reset "remember state" cookie
		if ( isset( $_COOKIE['mtsnb_state_'.$post_id] ) ) {
			unset( $_COOKIE['mtsnb_state_'.$post_id] );
			setcookie( 'mtsnb_state_'.$post_id, '', time() - 3600, '/' ); // empty value and old timestamp
		}

		// Update the meta field in the database.
		update_post_meta( $post_id, '_mtsnb_data', $my_data );

		// update option which holds the data about bars that have "Show after N times" condition ( since 1.0.3 )
		if ( get_option( 'mtsnb_show_after_data' ) !== false ) {

			$existing_arr = get_option( 'mtsnb_show_after_data' );

			if ( isset( $my_data['conditions']['after']['state'] ) && !empty( $my_data['conditions']['after']['state'] ) && isset( $my_data['conditions']['after']['data'] ) &&  0 !== (int) $my_data['conditions']['after']['data'] ) {

				// Add/update current bar data to option
				$existing_arr[ $post_id ] = (int) $my_data['conditions']['after']['data'];
				
			} else {

				// Remove current bar data from option if it is there
				if ( isset( $existing_arr[ $post_id ] ) ) {

					unset( $existing_arr[ $post_id ] );
				}
			}

			update_option( 'mtsnb_show_after_data', $existing_arr );

		} else {

			add_option( 'mtsnb_show_after_data', array() );
		}
	}

	/**
	 * Generate options for multi select options ( posts, pages, categories, tags )
	 *
	 * @since    1.0.0
	 */
	public function get_select_data( $name = 'post', $type = 'post_type' ) {

		$data = array();

		if ( 'page' === $name ) {

			$pages = get_pages();
			foreach ( $pages as $page ) {

				$data[ $page->ID ] = $page->post_title;
			}

		} else {

			if ( 'post_type' === $type ) {

				$args = array(
					'post_type' => $name,
					'posts_per_page' => -1,
					'post_status' => 'publish'
				);

				$posts_array = get_posts( $args );
				foreach ( $posts_array as $post ) {
					
					$data[ $post->ID ] = $post->post_title;
				}

			} else { //taxonomy terms

				$args = array(
					'get' => 'all',
				);

				$terms = get_terms( $name, $args );
				if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {

					foreach ( $terms as $term ) {

						$data[ $term->term_id ] = $term->name;
					}
				}
			}
		}

		return $data;
	}

	/**
	 * Generate options for multi select options once on admin_init
	 *
	 * @since    1.0.1
	 */
	public function set_select_options() {

		$this->post_tag_select_options = $this->get_select_data('post_tag', 'taxonomy');
		$this->category_select_options = $this->get_select_data('category','taxonomy');

		$this->page_select_options = $this->get_select_data('page');

		$cpt_supp = get_option( 'mtsnb_supported_custom_post_types', array() );
		if ( !empty( $cpt_supp ) ) {
			foreach ( $cpt_supp as $cpt ) {

				$taxonomy_objects = get_object_taxonomies( $cpt, 'objects' );

				foreach ( $taxonomy_objects as $taxonomy_name => $taxonomy_object ) {

					if ( $taxonomy_object->public && $taxonomy_object->show_ui ) {

						$this->supported_custom_taxonomies[ $cpt ][ $taxonomy_name ] = $taxonomy_object;
						$this->custom_taxonomies_select_options[ $cpt ][ $taxonomy_name ] = $this->get_select_data( $taxonomy_name, 'taxonomy' );
					}
				}
			}
		}
	}

	/**
	 * Get the animations array
	 *
	 * @since 1.0.3
	 */
	public function get_animations() {

		$animations = array(
			''                 => 'none',
			'mtsnb-bounce'     => 'bounce',
			'mtsnb-flash'      => 'flash',
			'mtsnb-pulse'      => 'pulse',
			'mtsnb-rubberBand' => 'rubberBand',
			'mtsnb-shake'      => 'shake',
			'mtsnb-swing'      => 'swing',
			'mtsnb-tada'       => 'tada',
			'mtsnb-wobble'     => 'wobble',
			'mtsnb-jello'      => 'jello',
			'mtsnb-bounceIn'      => 'bounceIn',
			'mtsnb-bounceInDown'  => 'bounceInDown',
			'mtsnb-bounceInLeft'  => 'bounceInLeft',
			'mtsnb-bounceInRight' => 'bounceInRight',
			'mtsnb-bounceInUp'    => 'bounceInUp',
			'mtsnb-fadeIn'        => 'fadeIn',
			'mtsnb-fadeInDown'    => 'fadeInDown',
			'mtsnb-fadeInDownBig' => 'fadeInDownBig',
			'mtsnb-fadeInLeft'    => 'fadeInLeft',
			'mtsnb-fadeInLeftBig' => 'fadeInLeftBig',
			'mtsnb-fadeInRight'   => 'fadeInRight',
			'mtsnb-fadeInRightBig' => 'fadeInRightBig',
			'mtsnb-fadeInUp'      => 'fadeInUp',
			'mtsnb-fadeInUpBig'   => 'fadeInUpBig',
			'mtsnb-flip'    => 'flip',
			'mtsnb-flipInX' => 'flipInX',
			'mtsnb-flipInY' => 'flipInY',
			'mtsnb-lightSpeedIn' => 'lightSpeedIn',
			'mtsnb-rotateIn'          => 'rotateIn',
			'mtsnb-rotateInDownLeft'  => 'rotateInDownLeft',
			'mtsnb-rotateInDownRight' => 'rotateInDownRight',
			'mtsnb-rotateInUpLeft'    => 'rotateInUpLeft',
			'mtsnb-rotateInUpRight'   => 'rotateInUpRight',
			'mtsnb-slideInUp'    => 'slideInUp',
			'mtsnb-slideInDown'  => 'slideInDown',
			'mtsnb-slideInLeft'  => 'slideInLeft',
			'mtsnb-slideInRight' => 'slideInRight',
			'mtsnb-zoomIn'      => 'zoomIn',
			'mtsnb-zoomInDown'  => 'zoomInDown',
			'mtsnb-zoomInLeft'  => 'zoomInLeft',
			'mtsnb-zoomInRight' => 'zoomInRight',
			'mtsnb-zoomInUp'    => 'zoomInUp',
			'mtsnb-rollIn' => 'rollIn',
		);

		return apply_filters( 'mtsnb_animations', $animations );
	}

	/**
	 * Get the array of icons
	 *
	 * @since 1.0.0
	 */
	public function get_awesome_icons() {

		$awesome_icons = array(
			'Web Application Icons' => array(
				'adjust', 'anchor', 'archive', 'area-chart', 'arrows', 'arrows-h', 'arrows-v', 'asterisk', 'at', 'balance-scale', 'ban', 'bar-chart', 'barcode', 'bars', 'battery-empty', 'battery-full', 'battery-half', 'battery-quarter', 'battery-three-quarters', 'bed', 'beer', 'bell', 'bell-o', 'bell-slash', 'bell-slash-o', 'bicycle', 'binoculars', 'birthday-cake', 'bolt', 'bomb', 'book', 'bookmark', 'bookmark-o', 'briefcase', 'bug', 'building', 'building-o', 'bullhorn', 'bullseye', 'bus', 'calculator', 'calendar', 'calendar-check-o', 'calendar-minus-o', 'calendar-o', 'calendar-plus-o', 'calendar-times-o', 'camera', 'camera-retro', 'car', 'caret-square-o-down', 'caret-square-o-left', 'caret-square-o-right', 'caret-square-o-up', 'cart-arrow-down', 'cart-plus', 'cc', 'certificate', 'check', 'check-circle', 'check-circle-o', 'check-square', 'check-square-o', 'child', 'circle', 'circle-o', 'circle-o-notch', 'circle-thin', 'clock-o', 'clone', 'cloud', 'cloud-download', 'cloud-upload', 'code', 'code-fork', 'coffee', 'cog', 'cogs', 'comment', 'comment-o', 'commenting', 'commenting-o', 'comments', 'comments-o', 'compass', 'copyright', 'creative-commons', 'credit-card', 'crop', 'crosshairs', 'cube', 'cubes', 'cutlery', 'database', 'desktop', 'diamond', 'dot-circle-o', 'download', 'ellipsis-h', 'ellipsis-v', 'envelope', 'envelope-o', 'envelope-square', 'eraser', 'exchange', 'exclamation', 'exclamation-circle', 'exclamation-triangle', 'external-link', 'external-link-square', 'eye', 'eye-slash', 'eyedropper', 'fax', 'female', 'fighter-jet', 'file-archive-o', 'file-audio-o', 'file-code-o', 'file-excel-o', 'file-image-o', 'file-pdf-o', 'file-powerpoint-o', 'file-video-o', 'file-word-o', 'film', 'filter', 'fire', 'fire-extinguisher', 'flag', 'flag-checkered', 'flag-o', 'flask', 'folder', 'folder-o', 'folder-open', 'folder-open-o', 'frown-o', 'futbol-o', 'gamepad', 'gavel', 'gift', 'glass', 'globe', 'graduation-cap', 'hand-lizard-o', 'hand-paper-o', 'hand-peace-o', 'hand-pointer-o', 'hand-rock-o', 'hand-scissors-o', 'hand-spock-o', 'hdd-o', 'headphones', 'heart', 'heart-o', 'heartbeat', 'history', 'home', 'hourglass', 'hourglass-end', 'hourglass-half', 'hourglass-o', 'hourglass-start', 'i-cursor', 'inbox', 'industry', 'info', 'info-circle', 'key', 'keyboard-o', 'language', 'laptop', 'leaf', 'lemon-o', 'level-down', 'level-up', 'life-ring', 'lightbulb-o', 'line-chart', 'location-arrow', 'lock', 'magic', 'magnet', 'male', 'map', 'map-marker', 'map-o', 'map-pin', 'map-signs', 'meh-o', 'microphone', 'microphone-slash', 'minus', 'minus-circle', 'minus-square', 'minus-square-o', 'mobile', 'money', 'moon-o', 'motorcycle', 'mouse-pointer', 'music', 'newspaper-o', 'object-group', 'object-ungroup', 'paint-brush', 'paper-plane', 'paper-plane-o', 'paw', 'pencil', 'pencil-square', 'pencil-square-o', 'phone', 'phone-square', 'picture-o', 'pie-chart', 'plane', 'plug', 'plus', 'plus-circle', 'plus-square', 'plus-square-o', 'power-off', 'print', 'puzzle-piece', 'qrcode', 'question', 'question-circle', 'quote-left', 'quote-right', 'random', 'recycle', 'refresh', 'registered', 'reply', 'reply-all', 'retweet', 'road', 'rocket', 'rss', 'rss-square', 'search', 'search-minus', 'search-plus', 'server', 'share', 'share-alt', 'share-alt-square', 'share-square', 'share-square-o', 'shield', 'ship', 'shopping-cart', 'sign-in', 'sign-out', 'signal', 'sitemap', 'sliders', 'smile-o', 'sort', 'sort-alpha-asc', 'sort-alpha-desc', 'sort-amount-asc', 'sort-amount-desc', 'sort-asc', 'sort-desc', 'sort-numeric-asc', 'sort-numeric-desc', 'space-shuttle', 'spinner', 'spoon', 'square', 'square-o', 'star', 'star-half', 'star-half-o', 'star-o', 'sticky-note', 'sticky-note-o', 'street-view', 'suitcase', 'sun-o', 'tablet', 'tachometer', 'tag', 'tags', 'tasks', 'taxi', 'television', 'terminal', 'thumb-tack', 'thumbs-down', 'thumbs-o-down', 'thumbs-o-up', 'thumbs-up', 'ticket', 'times', 'times-circle', 'times-circle-o', 'tint', 'toggle-off', 'toggle-on', 'trademark', 'trash', 'trash-o', 'tree', 'trophy', 'truck', 'tty', 'umbrella', 'university', 'unlock', 'unlock-alt', 'upload', 'user', 'user-plus', 'user-secret', 'user-times', 'users', 'video-camera', 'volume-down', 'volume-off', 'volume-up', 'wheelchair', 'wifi', 'wrench'
			),
			'Hand Icons' => array(
				'hand-lizard-o', 'hand-o-down', 'hand-o-left', 'hand-o-right', 'hand-o-up', 'hand-paper-o', 'hand-peace-o', 'hand-pointer-o', 'hand-rock-o', 'hand-scissors-o', 'hand-spock-o', 'thumbs-down', 'thumbs-o-down', 'thumbs-o-up', 'thumbs-up'
			),
			'Transportation Icons' => array(
				'ambulance', 'bicycle', 'bus', 'car', 'fighter-jet', 'motorcycle', 'plane', 'rocket', 'ship', 'space-shuttle', 'subway', 'taxi', 'train', 'truck', 'wheelchair'
			),
			'Gender Icons' => array(
				'genderless', 'mars', 'mars-double', 'mars-stroke', 'mars-stroke-h', 'mars-stroke-v', 'mercury', 'neuter', 'transgender', 'transgender-alt', 'venus', 'venus-double', 'venus-mars'
			),
			'File Type Icons' => array(
				'file', 'file-archive-o', 'file-audio-o', 'file-code-o', 'file-excel-o', 'file-image-o', 'file-o', 'file-pdf-o', 'file-powerpoint-o', 'file-text', 'file-text-o', 'file-video-o', 'file-word-o'
			),
			'Spinner Icons' => array(
				'circle-o-notch', 'cog', 'refresh', 'spinner'
			),
			'Form Control Icons' => array(
				'check-square', 'check-square-o', 'circle', 'circle-o', 'dot-circle-o', 'minus-square', 'minus-square-o', 'plus-square', 'plus-square-o', 'square', 'square-o'
			),
			'Payment Icons' => array(
				'cc-amex', 'cc-diners-club', 'cc-discover', 'cc-jcb', 'cc-mastercard', 'cc-paypal', 'cc-stripe', 'cc-visa', 'credit-card', 'google-wallet', 'paypal'
			),
			'Chart Icons' => array(
				'area-chart', 'bar-chart', 'line-chart', 'pie-chart'
			),
			'Currency Icons' => array(
				'btc', 'eur', 'gbp', 'gg', 'gg-circle', 'ils', 'inr', 'jpy', 'krw', 'money', 'rub', 'try', 'usd'
			),
			'Text Editor Icons' => array(
				'align-center', 'align-justify', 'align-left', 'align-right', 'bold', 'chain-broken', 'clipboard', 'columns', 'eraser', 'file', 'file-o', 'file-text', 'file-text-o', 'files-o', 'floppy-o', 'font', 'header', 'indent', 'italic', 'link', 'list', 'list-alt', 'list-ol', 'list-ul', 'outdent', 'paperclip', 'paragraph', 'repeat', 'scissors', 'strikethrough', 'subscript', 'superscript', 'table', 'text-height', 'text-width', 'th', 'th-large', 'th-list', 'underline', 'undo'
			),
			'Directional Icons' => array(
				'angle-double-down', 'angle-double-left', 'angle-double-right', 'angle-double-up', 'angle-down', 'angle-left', 'angle-right', 'angle-up', 'arrow-circle-down', 'arrow-circle-left', 'arrow-circle-o-down', 'arrow-circle-o-left', 'arrow-circle-o-right', 'arrow-circle-o-up', 'arrow-circle-right', 'arrow-circle-up', 'arrow-down', 'arrow-left', 'arrow-right', 'arrow-up', 'arrows', 'arrows-alt', 'arrows-h', 'arrows-v', 'caret-down', 'caret-left', 'caret-right', 'caret-square-o-down', 'caret-square-o-left', 'caret-square-o-right', 'caret-square-o-up', 'caret-up', 'chevron-circle-down', 'chevron-circle-left', 'chevron-circle-right', 'chevron-circle-up', 'chevron-down', 'chevron-left', 'chevron-right', 'chevron-up', 'exchange', 'hand-o-down', 'hand-o-left', 'hand-o-right', 'hand-o-up', 'long-arrow-down', 'long-arrow-left', 'long-arrow-right', 'long-arrow-up'
			),
			'Video Player Icons' => array(
				'arrows-alt', 'backward', 'compress', 'eject', 'expand', 'fast-backward', 'fast-forward', 'forward', 'pause', 'play', 'play-circle', 'play-circle-o', 'random', 'step-backward', 'step-forward', 'stop', 'youtube-play'
			),
			'Brand Icons' => array(
				'500px', 'adn', 'amazon', 'android', 'angellist', 'apple', 'behance', 'behance-square', 'bitbucket', 'bitbucket-square', 'black-tie', 'btc', 'buysellads', 'cc-amex', 'cc-diners-club', 'cc-discover', 'cc-jcb', 'cc-mastercard', 'cc-paypal', 'cc-stripe', 'cc-visa', 'chrome', 'codepen', 'connectdevelop', 'contao', 'css3', 'dashcube', 'delicious', 'deviantart', 'digg', 'dribbble', 'dropbox', 'drupal', 'empire', 'expeditedssl', 'facebook', 'facebook-official', 'facebook-square', 'firefox', 'flickr', 'fonticons', 'forumbee', 'foursquare', 'get-pocket', 'gg', 'gg-circle', 'git', 'git-square', 'github', 'github-alt', 'github-square', 'google', 'google-plus', 'google-plus-square', 'google-wallet', 'gratipay', 'hacker-news', 'houzz', 'html5', 'instagram', 'internet-explorer', 'ioxhost', 'joomla', 'jsfiddle', 'lastfm', 'lastfm-square', 'leanpub', 'linkedin', 'linkedin-square', 'linux', 'maxcdn', 'meanpath', 'medium', 'odnoklassniki', 'odnoklassniki-square', 'opencart', 'openid', 'opera', 'optin-monster', 'pagelines', 'paypal', 'pied-piper', 'pied-piper-alt', 'pinterest', 'pinterest-p', 'pinterest-square', 'qq', 'rebel', 'reddit', 'reddit-square', 'renren', 'safari', 'sellsy', 'share-alt', 'share-alt-square', 'shirtsinbulk', 'simplybuilt', 'skyatlas', 'skype', 'slack', 'slideshare', 'soundcloud', 'spotify', 'stack-exchange', 'stack-overflow', 'steam', 'steam-square', 'stumbleupon', 'stumbleupon-circle', 'tencent-weibo', 'trello', 'tripadvisor', 'tumblr', 'tumblr-square', 'twitch', 'twitter', 'twitter-square', 'viacoin', 'vimeo', 'vimeo-square', 'vine', 'vk', 'weibo', 'weixin', 'whatsapp', 'wikipedia-w', 'windows', 'wordpress', 'xing', 'xing-square', 'y-combinator', 'yahoo', 'yelp', 'youtube', 'youtube-play', 'youtube-square'
			),
			'Medical Icons' => array(
				'ambulance', 'h-square', 'heart', 'heart-o', 'heartbeat', 'hospital-o', 'medkit', 'plus-square', 'stethoscope', 'user-md', 'wheelchair'
			)
		);

		return apply_filters( 'mtsnb_awesome_icons', $awesome_icons );
	}

	/**
	 * Get the array of social icons
	 *
	 * @since 1.0.0
	 */
	public function get_social_icons() {

		$social_icons = array(
			'fa-delicious' 		=> 'Delicious',
			'fa-digg' 			=> 'Digg',
			'fa-dribbble' 		=> 'Dribbble',
			'fa-facebook' 		=> 'Facebook',
			'fa-flickr' 		=> 'Flickr',
			'fa-foursquare' 	=> 'Four Square',
			'fa-github' 		=> 'Github',
			'fa-google-plus'	=> 'Google+',
			'fa-linkedin' 		=> 'LinkedIn',
			'fa-instagram'		=> 'Instagram',
			'fa-pinterest' 		=> 'Pinterest',
			'fa-reddit' 		=> 'Reddit',
			'fa-tumblr' 		=> 'Tumblr',
			'fa-twitch' 		=> 'Twitch',
			'fa-twitter' 		=> 'Twitter',
			'fa-vine' 			=> 'Vine',
			'fa-yelp' 			=> 'Yelp',
			'fa-youtube' 		=> 'YouTube',
		);

		return apply_filters( 'mtsnb_social_icons', $social_icons );
	}

	/**
	 * Get the array of UTM Tags to always show in options
	 *
	 * @since 1.0.0
	 */
	public function get_default_utm_tags() {

		return apply_filters( 'mtsnb_utm_tags', array( 'utm_source', 'utm_medium', 'utm_term', 'utm_content', 'utm_campaign') );
	}

	/**
	 * Get all MailChimp Lists and display in settings
	 *
	 */
	public function get_mailchimp_lists() {

		$options = '';

		if (isset($_POST['api_key']) && $_POST['api_key'] != '') {
			require_once( MTSNB_PLUGIN_DIR . 'includes/mailchimp/MailChimp.php');

			$MailChimp = new WPS_MailChimp($_POST['api_key']);
			$lists = $MailChimp->call('lists/list');

			if (isset($lists) && is_array($lists)) {

				foreach ($lists['data'] as $list) {
					$options .= '<option value="' . $list['id'] . '">' .  $list['name'] . '</option>';
				}

				if (isset($_POST['list']) && $_POST['list'] != '') {
					$options = '';
					foreach ($lists['data'] as $list) {

						if ($_POST['list'] == $list['id']) {
							$options .= '<option value="' . $list['id'] . '" selected="selected">' .  $list['name']. '</option>';
						} else {
							$options .= '<option value="' . $list['id'] . '">' .  $list['name'] . '</option>';
						}
					}
				}
			}
		}

		echo $options;

		die(); // this is required to terminate immediately and return a proper response
	}

	/**
	 * Get the Aweber lists for an account
	 *
	 */
	public function get_aweber_lists() {

		$options = '';
		$consumerKey = '';
		$consumerSecret = '';
		$accessKey = '';
		$accessSecret = '';

		if (isset($_POST['code']) && $_POST['code'] != '') {

			require_once( MTSNB_PLUGIN_DIR . 'includes/aweber/aweber_api.php');

			try {
				$credentials = AWeberAPI::getDataFromAweberID($_POST['code']);
				list($consumerKey, $consumerSecret, $accessKey, $accessSecret) = $credentials;


				$consumerKey = isset($consumerKey) && !empty($consumerKey) ? $consumerKey : '';
				$consumerSecret = isset($consumerSecret) && !empty($consumerSecret) ? $consumerSecret : '';
				$accessKey = isset($accessKey) && !empty($accessKey) ? $accessKey : '';
				$accessSecret = isset($accessSecret) && !empty($accessSecret) ? $accessSecret : '';
			} catch (AWeberAPIException $exc) {
				error_log($exc);
			}

			try {

				$aweber = new AWeberAPI($consumerKey, $consumerSecret);
				$account = $aweber->getAccount($accessKey, $accessSecret);
				$lists = $account->loadFromUrl('/accounts/' . $account->id . '/lists/');

				foreach ($lists as $list) {
					$options .= '<option value="' . $list->id . '">' .  $list->name . '</option>';
				}

			} catch (AWeberAPIException $exc) { error_log($exc); }
		}

		if (isset($_POST['list']) && $_POST['list'] != '') {

			$consumerKey     = $_POST['consumer_key'];
			$consumerSecret  = $_POST['consumer_secret'];
			$accessKey       = $_POST['access_key'];
			$accessSecret    = $_POST['access_secret'];

			require_once( MTSNB_PLUGIN_DIR . 'includes/aweber/aweber_api.php');

			try {

				$aweber = new AWeberAPI($consumerKey, $consumerSecret);
				$account = $aweber->getAccount($accessKey, $accessSecret);
				$lists = $account->loadFromUrl('/accounts/' . $account->id . '/lists/');

				$options = '';
				foreach ($lists as $list) {
					if ($_POST['list'] == $list->id) {
						$options .= '<option value="' . $list->id . '" selected="selected">' .  $list->name . '</option>';
					} else {
						$options .= '<option value="' . $list->id . '">' .  $list->name . '</option>';
					}
				}

			} catch (AWeberAPIException $exc) { error_log($exc); }
		}

		echo json_encode(array(
			'html'               => $options,
			'consumer_key'       => $consumerKey,
			'consumer_secret'    => $consumerSecret,
			'access_key'         => $accessKey,
			'access_secret'      => $accessSecret,
		));

		die(); // this is required to terminate immediately and return a proper response
	}

	/**
	 * Get all Get Repsonse Lists and display in settings
	 *
	 */
	public function get_getresponse_lists() {

		$options = '';

		if (isset($_POST['api_key']) && $_POST['api_key'] != '') {

			require_once( MTSNB_PLUGIN_DIR . 'includes/getresponse/jsonRPCClient.php');
			$api = new jsonRPCClient('http://api2.getresponse.com');

			try {
				$result = $api->get_campaigns($_POST['api_key']);
				foreach ((array) $result as $k => $v) {
					$campaigns[] = array('id' => $k, 'name' => $v['name']);
				}
			}

			catch (Exception $e) {}

			if (isset($campaigns) && is_array($campaigns)) {

				foreach ($campaigns as $campaign) {
					$options .= '<option value="' . $campaign['id'] . '">' .  $campaign['name'] . '</option>';
				}

				if (isset($_POST['campaign']) && $_POST['campaign'] != '') {
					$options = '';
					foreach ($campaigns as $campaign) {

						if ($_POST['campaign'] == $campaign['id']) {
							$options .= '<option value="' . $campaign['id'] . '" selected="selected">' .  $campaign['name'] . '</option>';
						} else {
							$options .= '<option value="' . $campaign['id'] . '">' .  $campaign['name'] . '</option>';
						}
					}
				}
			}
		}

		echo $options;

		die(); // this is required to terminate immediately and return a proper response
	}

	/**
	 * Get all Campaign Monitor Lists and display in settings
	 *
	 */
	public function get_campaignmonitor_lists() {

		$lists = '';
		$clients = '';

		if (isset($_POST['api_key']) && $_POST['api_key'] != '') {

			require_once( MTSNB_PLUGIN_DIR . 'includes/campaignmonitor/csrest_general.php');
			require_once( MTSNB_PLUGIN_DIR . 'includes/campaignmonitor/csrest_clients.php');
			$auth = array('api_key' => $_POST['api_key']);
			$wrap = new CS_REST_General($auth);
			$result = $wrap->get_clients();


			if ($result->was_successful()) {

				foreach ($result->response as $client) {
					$clients .= '<option value="' . $client->ClientID . '">' .  $client->Name . '</option>';
				}

				if (isset($_POST['client']) && $_POST['client'] != '') {
					$clients = '';
					foreach ($result->response as $client) {
						if ($_POST['client'] == $client->ClientID) {
							$clients .= '<option value="' . $client->ClientID . '" selected="selected">' .  $client->Name . '</option>';
						} else {
							$clients .= '<option value="' . $client->ClientID . '">' .  $client->Name . '</option>';
						}
					}
				}

				if (isset($_POST['client']) && $_POST['client'] != '') {
					$client_id = $_POST['client'];
				} else {
					$client_id = $result->response[0]->ClientID;
				}

				$wrap = new CS_REST_Clients($client_id, $_POST['api_key']);
				$result = $wrap->get_lists();

				if ($result->was_successful()) {
					foreach ($result->response as $list) {
						$lists .= '<option value="' . $list->ListID . '">' .  $list->Name . '</option>';
					}

					if (isset($_POST['list']) && $_POST['list'] != '') {
						$lists = '';
						foreach ($result->response as $list) {
							if ($_POST['list'] == $list->ListID) {
								$lists .= '<option value="' . $list->ListID . '" selected="selected">' .  $list->Name . '</option>';
							} else {
								$lists .= '<option value="' . $list->ListID . '">' .  $list->Name . '</option>';
							}
						}
					}
				}
			}
		}

		echo json_encode(array('clients' => $clients, 'lists' => $lists));

		die(); // this is required to terminate immediately and return a proper response
	}

	/**
	 * Update all Campaign Monitor Lists and display in settings
	 *
	 */
	public function update_campaignmonitor_lists() {

		$lists = '';

		if (isset($_POST['api_key']) && $_POST['api_key'] != '' &&
			isset($_POST['client_id']) && $_POST['client_id'] != '') {

			require_once( MTSNB_PLUGIN_DIR . 'includes/campaignmonitor/csrest_general.php');
			require_once( MTSNB_PLUGIN_DIR . 'includes/campaignmonitor/csrest_clients.php');


			$wrap = new CS_REST_Clients($_POST['client_id'], $_POST['api_key']);
			$result = $wrap->get_lists();


			if ($result->was_successful()) {
				foreach ($result->response as $list) {
					$lists .= '<option value="' . $list->ListID . '">' .  $list->Name . '</option>';
				}
			}
		}

		echo $lists;

		die(); // this is required to terminate immediately and return a proper response
	}

	/**
	 * Get all Mad Mimi Lists and display in settings
	 *
	 */
	public function get_madmimi_lists() {

		$options = '';

		if (isset($_POST['api_key']) && $_POST['api_key'] != '' &&
			isset($_POST['username']) && $_POST['username'] != '') {

			require_once( MTSNB_PLUGIN_DIR . 'includes/madmimi/MadMimi.class.php');

			$mailer = new MadMimi($_POST['username'], $_POST['api_key']);

			if (isset($mailer)) {
				try {
					$lists = $mailer->Lists();
					$lists  = new SimpleXMLElement($lists);

				    if ($lists->list) {
						foreach ($lists->list as $l) {
							$options .= '<option value="' . $l->attributes()->{'name'}->{0} . '">' .  $l->attributes()->{'name'}->{0} . '</option>';
						}
				    }

				    if (isset($_POST['list']) && $_POST['list'] != '') {
					    $options = '';
						foreach ($lists->list as $l) {

							if ($_POST['list'] == $l->attributes()->{'name'}->{0}) {
								$options .= '<option value="' . $l->attributes()->{'name'}->{0} . '" selected="selected">' .  $l->attributes()->{'name'}->{0} . '</option>';
							} else {
								$options .= '<option value="' . $l->attributes()->{'name'}->{0} . '">' .  $l->attributes()->{'name'}->{0} . '</option>';
							}
						}
					}
				} catch (Exception $exc) {}
			}
		}

		echo $options;

		die(); // this is required to terminate immediately and return a proper response
	}

	function color_palettes_select($target = 'mtsnb_fields') {
		$palettes = $this->get_default_color_palettes();
		if (!empty($palettes)) {
			?>
			<div class="mtsnb-colors-loader">
				<select class="mtsnb-colors-select">
					<option></option>
					<?php foreach ($palettes as $i => $palette) { ?>
					<option value="<?php echo $i; ?>" data-target="<?php echo esc_attr( $target ); ?>" data-colors="<?php echo esc_attr( json_encode( $palette['colors'] ) ); ?>"><?php echo $palette['name']; ?></option>
					<?php } ?>
				</select>
				<!-- <a href="#" class="mtsnb-toggle-palettes"><?php _e('Load a predefined color set', 'wp-subscribe'); ?></a>
				<div class="mtsnb-palettes">
					<?php foreach ($palettes as $i => $palette) { ?>
					<div class="single-palette">
					<table class="color-palette">
						<tbody>
							<tr>
								<?php foreach ($palette['colors'] as $color) { ?>
								<td style="background-color: <?php echo $color; ?>">&nbsp;</td>
								<?php } ?>
							</tr>
						</tbody>
					</table>
					<?php foreach ($palette['colors'] as $field => $color) { ?>
					<input type="hidden" class="mtsnb-palette-color" name="<?php echo $target.'_'.$field.'_color'; ?>" value="<?php echo $color; ?>" />
					<?php } ?>
					<a href="#" class="button button-secondary mtsnb-load-palette"><?php _e('Load colors', 'wp-subscribe'); ?></a>
					</div> 
					<?php } ?>
				</div> -->
			</div>
			<?php 
		}
	}

	function get_default_color_palettes() {
		$default_palettes = array(
			array(
				'name' => __('Default', $this->plugin_name),
				'colors' => array(
					'bg' => '#D35151',
					'txt' => '#FFFFFF', 
					'link' => '#FFF500', 
					'link_hover' => '#FFF500', 
					'button' => '#000000', 
					'button_bg' => '#FFF500'
				),
			),
			array(
				'name' => __('Yellow', $this->plugin_name),
				'colors' => array(
					'bg' => '#F3C500',
					'txt' => '#000000', 
					'link' => '#D35151', 
					'link_hover' => '#D35151', 
					'button' => '#FFFFFF', 
					'button_bg' => '#000000'
				)
			),
			array(
				'name' => __('While', $this->plugin_name),
				'colors' => array(
					'bg' => '#FFFFFF',
					'txt' => '#000000', 
					'link' => '#FFA000', 
					'link_hover' => '#FFA000', 
					'button' => '#000000', 
					'button_bg' => '#FFA000'
				)
			),
			array(
				'name' => __('Turquoise', $this->plugin_name),
				'colors' => array(
					'bg' => '#1abc9c',
					'txt' => '#ffffff', 
					'link' => '#FFF500', 
					'link_hover' => '#FFF500', 
					'button' => '#000000', 
					'button_bg' => '#FFF500'
				)
			),
			array(
				'name' => __('Peter River', $this->plugin_name),
				'colors' => array(
					'bg' => '#3498db',
					'txt' => '#FFFFFF', 
					'link' => '#FFA000', 
					'link_hover' => '#FFA000', 
					'button' => '#000000', 
					'button_bg' => '#FFA000'
				),
			),
			array(
				'name' => __('Amethyst', $this->plugin_name),
				'colors' => array(
					'bg' => '#9b59b6',
					'txt' => '#FFFFFF', 
					'link' => '#FFA000', 
					'link_hover' => '#FFA000', 
					'button' => '#000000', 
					'button_bg' => '#FFA000'
				)
			),
			array(
				'name' => __('Wet Asphalt', $this->plugin_name),
				'colors' => array(
					'bg' => '#34495e',
					'txt' => '#FFFFFF', 
					'link' => '#FFA000', 
					'link_hover' => '#FFA000', 
					'button' => '#000000', 
					'button_bg' => '#FFA000'
				)
			),
			array(
				'name' => __('Orange', $this->plugin_name),
				'colors' => array(
					'bg' => '#f39c12',
					'txt' => '#FFFFFF', 
					'link' => '#F5FF00', 
					'link_hover' => '#F5FF00', 
					'button' => '#000000', 
					'button_bg' => '#F5FF00'
				)
			),
			array(
				'name' => __('Pumpkin', $this->plugin_name),
				'colors' => array(
					'bg' => '#d35400',
					'txt' => '#FFFFFF', 
					'link' => '#FFD800', 
					'link_hover' => '#FFD800', 
					'button' => '#000000', 
					'button_bg' => '#FFD800'
				)
			),
			array(
				'name' => __('Pomegranate', $this->plugin_name),
				'colors' => array(
					'bg' => '#c0392b',
					'txt' => '#FFFFFF', 
					'link' => '#FFE200', 
					'link_hover' => '#FFE200', 
					'button' => '#000000', 
					'button_bg' => '#FFE200'
				)
			),
			array(
				'name' => __('Silver', $this->plugin_name),
				'colors' => array(
					'bg' => '#bdc3c7',
					'txt' => '#000000', 
					'link' => '#00A0D2', 
					'link_hover' => '#00A0D2', 
					'button' => '#FFFFFF', 
					'button_bg' => '#00A0D2'
				)
			),
			array(
				'name' => __('Honey Flower', $this->plugin_name),
				'colors' => array(
					'bg' => '#674172',
					'txt' => '#FFFFFF', 
					'link' => '#FFBE00', 
					'link_hover' => '#FFBE00', 
					'button' => '#000000', 
					'button_bg' => '#FFBE00'
				)
			),
			array(
				'name' => __('Light Wisteria', $this->plugin_name),
				'colors' => array(
					'bg' => '#BE90D4',
					'txt' => '#FFFFFF', 
					'link' => '#FFBE00', 
					'link_hover' => '#FFBE00', 
					'button' => '#000000', 
					'button_bg' => '#FFBE00'
				)
			),
			array(
				'name' => __('Cape Honey', $this->plugin_name),
				'colors' => array(
					'bg' => '#FDE3A7',
					'txt' => '#000000', 
					'link' => '#D35151', 
					'link_hover' => '#D35151', 
					'button' => '#FFFFFF', 
					'button_bg' => '#000000'
				)
			),
		);
		return apply_filters( 'wp_notification_color_sets', $default_palettes );
	}

	/**
	 * Add custom columns to "mts_notification_bar" listing table
	 *
	 * @since    1.0.3
	 *
	 * @param array   $columns
	 * @return array   $columns
	 */
	public function mtsnb_ad_columns_head( $columns ) {

		$columns['impressions'] =  __( 'Impressions', $this->plugin_name );
		$columns['clicks']      =  __( 'Clicks', $this->plugin_name );

 		return $columns;
	}

	/**
	 * Add our column content
	 *
	 * @since    1.0.3
	 *
	 * @param string   $deprecated
	 * @param string   $column_name
	 * @param string   $term_id
	 * @return string   $icon
	 */
	public function mtsnb_ad_column_content( $column, $post_id ) {

		if ( $column == 'impressions') {

			$opt_array = get_option('mtsnb_stats');

			if ( isset( $opt_array[ $post_id ]['impressions'] ) ) {

				echo $opt_array[ $post_id ]['impressions'];

			} else {

				echo '0';
			}
		}

		if ( $column == 'clicks') {

			$opt_array = get_option('mtsnb_stats');

			if ( isset( $opt_array[ $post_id ]['clicks'] ) ) {

				echo $opt_array[ $post_id ]['clicks'];

			} else {

				echo '0';
			}
		}
	}

	/**
	 * Stats for A/B tests
	 *
	 * @since    1.0.3
	 */
	public function ab_test_stats( $variation, $bar_id ) {

		$opt_array = get_option('mtsnb_stats');

		$visitors    = isset( $opt_array[ $bar_id ][ $variation.'_count' ] ) ? $opt_array[ $bar_id ][ $variation.'_count' ] : 0;
		$impressions = isset( $opt_array[ $bar_id ][ $variation.'_impressions' ] ) ? $opt_array[ $bar_id ][ $variation.'_impressions' ] : 0;
		$clicks      = isset( $opt_array[ $bar_id ][ $variation.'_clicks' ] ) ? $opt_array[ $bar_id ][ $variation.'_clicks' ] : 0;

		echo '<div class="mtsnb-test-stat">' . sprintf( __( 'Visitors: %d', $this->plugin_name ), $visitors ) . '</div>';
		echo '<div class="mtsnb-test-stat">' . sprintf( __( 'Impressions: %d', $this->plugin_name ), $impressions ) . '</div>';
		echo '<div class="mtsnb-test-stat">' . sprintf( __( 'Clicks: %d', $this->plugin_name ), $clicks ) . '</div>';
	}

	/**
	 * Reset single A/B test stats
	 *
	 * @since    1.0.3
	 */
	public function reset_ab_stats() {

		$bar_id = $_POST['bar_id'];

		$opt_array = get_option('mtsnb_stats');

		// Backup impressions and clicks
		$impressions = isset( $opt_array[ $bar_id ]['impressions'] ) ? $opt_array[ $bar_id ]['impressions'] : '';
		$clicks      = isset( $opt_array[ $bar_id ]['clicks'] ) ? $opt_array[ $bar_id ]['clicks'] : '';

		// Remove all stats
		if ( isset( $opt_array[ $bar_id ] ) ) {

			unset( $opt_array[ $bar_id ] );
		}

		// Bring back impressions and clicks
		if ( !empty( $impressions ) ) {

			$opt_array[ $bar_id ]['impressions'] = $impressions;
		}
		if ( !empty( $clicks ) ) {

			$opt_array[ $bar_id ]['clicks'] = $clicks;
		}

		update_option( 'mtsnb_stats', $opt_array );

		if ( isset( $_COOKIE['mtsnb_ab_'.$bar_id] ) ) {
			unset( $_COOKIE['mtsnb_ab_'.$bar_id] );
			$secure = ( 'https' === parse_url( home_url(), PHP_URL_SCHEME ) );// maybe not needed
			setcookie( 'mtsnb_ab_'.$bar_id, '', time() - 3600, COOKIEPATH, COOKIE_DOMAIN, $secure );
		}

		echo '<div class="mtsnb-test-stat">' . sprintf( __( 'Visitors: %d', $this->plugin_name ), 0 ) . '</div>';
		echo '<div class="mtsnb-test-stat">' . sprintf( __( 'Impressions: %d', $this->plugin_name ), 0 ) . '</div>';
		echo '<div class="mtsnb-test-stat">' . sprintf( __( 'Clicks: %d', $this->plugin_name ), 0 ) . '</div>';
		
		die(); // this is required to terminate immediately and return a proper response
	}

	/**
	 * Deactivate free plugin
	 *
	 * @since    1.0.9
	 */
	public function check_version() {

		if ( defined( 'MTSNBF_PLUGIN_BASE' ) ) {

			if ( is_plugin_active( MTSNBF_PLUGIN_BASE ) ) {

				deactivate_plugins( MTSNBF_PLUGIN_BASE );
				add_action( 'admin_notices', array( $this, 'disabled_notice' ) );
				if ( isset( $_GET['activate'] ) ) {
					unset( $_GET['activate'] );
				}
			}
		}
	}

	/**
	 * Deactivation notice
	 *
	 * @since    1.0.9
	 */
	public function disabled_notice() {
		?>
		<div class="updated">
			<p><?php _e( 'Free version of WP Notification Bar plugin disabled. Pro version is active!', $this->plugin_name ); ?></p>
		</div>
	<?php
	}

	/**
	 * Notification Bar update messages
	 *
	 * @since    1.0.9
	 *
	 * @param array   $messages
	 * @return array   $messages
	 */
	public function mtsnb_update_messages( $messages ) {

		global $post;

		$post_ID = $post->ID;
		$post_type = get_post_type( $post_ID );

		if ('mts_notification_bar' == $post_type ) {

			$messages['mts_notification_bar'] = array(
				0 => '', // Unused. Messages start at index 1.
				1 => __( 'Notification Bar updated.', $this->plugin_name ),
				2 => __( 'Custom field updated.', $this->plugin_name ),
				3 => __( 'Custom field deleted.', $this->plugin_name ),
				4 => __( 'Notification Bar updated.', $this->plugin_name ),
				5 => isset($_GET['revision']) ? sprintf( __('Notification Bar restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
				6 => __( 'Notification Bar published.', $this->plugin_name ),
				7 => __( 'Notification Bar saved.', $this->plugin_name ),
				8 => __( 'Notification Bar submitted.', $this->plugin_name),
				9 => sprintf( __('Notification Bar  scheduled for: <strong>%1$s</strong>.', $this->plugin_name ), date_i18n( __( 'M j, Y @ H:i' ), strtotime( $post->post_date ) ) ),
				10 => __('Notification Bar draft updated.', $this->plugin_name ),
			);
		}

		return $messages;
	}

	/**
	 * Single post view bar select
	 *
	 * @since    1.1.0
	 */
	public function mtsnb_select_metabox_insert() {
		
		$force_bar_post_types = $this->force_bar_post_types;

		if ( $force_bar_post_types && is_array( $force_bar_post_types ) ) {

			foreach ( $force_bar_post_types as $screen ) {

				add_meta_box(
					'mtsnb_single_bar_metabox',
					__( 'Notification Bar', $this->plugin_name ),
					array( $this, 'mtsnb_select_metabox_content' ),
					$screen,
					'side',
					'default'
				);
			}
		}
	}
	public function mtsnb_select_metabox_content( $post ) {

		// Add an nonce field so we can check for it later.
		wp_nonce_field('mtsnb_select_metabox_save', 'mtsnb_select_metabox_nonce');

		/*
		* Use get_post_meta() to retrieve an existing value
		* from the database and use the value for the form.
		*/
		$bar = get_post_meta( $post->ID, '_mtsnb_override_bar', true );

		$processed_item_ids = '';
		if ( !empty( $bar ) ) {
			// Some entries may be arrays themselves!
			$processed_item_ids = array();
			foreach ($bar as $this_id) {
				if (is_array($this_id)) {
					$processed_item_ids = array_merge( $processed_item_ids, $this_id );
				} else {
					$processed_item_ids[] = $this_id;
				}
			}

			if (is_array($processed_item_ids) && !empty($processed_item_ids)) {
				$processed_item_ids = implode(',', $processed_item_ids);
			} else {
				$processed_item_ids = '';
			}
		}
		?>
		<p>
			<label for="mtsnb_override_bar_field"><?php _e( 'Select Notification Bar (optional):', $this->plugin_name ); ?></label><br />
			<input style="width: 400px;" type="hidden" id="mtsnb_override_bar_field" name="mtsnb_override_bar_field" class="mtsnb-bar-select"  value="<?php echo $processed_item_ids; ?>" />
		</p>
		<p>
			<i><?php _e( 'Selected notification bar will override any other bar.', $this->plugin_name ); ?></i>
		</p>
		<?php
	}

	public function mtsnb_select_metabox_save( $post_id ) {

		// Check if our nonce is set.
		if ( ! isset( $_POST['mtsnb_select_metabox_nonce'] ) ) {
			return;
		}
		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $_POST['mtsnb_select_metabox_nonce'], 'mtsnb_select_metabox_save' ) ) {
			return;
		}
		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Check the user's permissions.
		if ( 'page' == $_POST['post_type'] ) {

			if ( ! current_user_can( 'edit_page', $post_id ) )
				return;

		} else {

			if ( ! current_user_can( 'edit_post', $post_id ) )
				return;
		}

		/* OK, its safe for us to save the data now. */
		if ( ! isset( $_POST['mtsnb_override_bar_field'] ) ) {
			return;
		}

		$val = $_POST['mtsnb_override_bar_field'];

		if (strpos($val, ',') === false) {
			// No comma, must be single value - still needs to be in an array for now
			$post_ids = array( $val );
		} else {
			// There is a comma so it's explodable
			$post_ids = explode(',', $val);
		}

		// Update the meta field in the database.
		update_post_meta( $post_id, '_mtsnb_override_bar', $post_ids );
	}

	/**
	 * Bar select ajax function
	 *
	 * @since    1.0.1
	 */
	public function mtsnb_get_bars() {

		$result = array();

		$search = $_REQUEST['q'];

		$ads_query = array(
			'posts_per_page' => -1,
			'post_status' => array('publish'),
			'post_type' => 'mts_notification_bar',
			'order' => 'ASC',
			'orderby' => 'title',
			'suppress_filters' => false,
			's'=> $search
		);
		$posts = get_posts( $ads_query );

		// We'll return a JSON-encoded result.
		foreach ( $posts as $this_post ) {
			$post_title = $this_post->post_title;
			$id = $this_post->ID;

			$result[] = array(
				'id' => $id,
				'title' => $post_title,
			);
		}

	    echo json_encode( $result );

	    die();
	}

	public function mtsnb_get_bar_titles() {
		$result = array();

		if (isset($_REQUEST['post_ids'])) {
			$post_ids = $_REQUEST['post_ids'];
			if (strpos($post_ids, ',') === false) {
				// There is no comma, so we can't explode, but we still want an array
				$post_ids = array( $post_ids );
			} else {
				// There is a comma, so it must be explodable
				$post_ids = explode(',', $post_ids);
			}
		} else {
			$post_ids = array();
		}

		if (is_array($post_ids) && ! empty($post_ids)) {

			$posts = get_posts(array(
				'posts_per_page' => -1,
				'post_status' => array('publish'),
				'post__in' => $post_ids,
				'post_type' => 'mts_notification_bar'
			));
			foreach ( $posts as $this_post ) {
				$result[] = array(
					'id' => $this_post->ID,
					'title' => $this_post->post_title,
				);
			}
		}

		echo json_encode( $result );

		die();
	}
}
