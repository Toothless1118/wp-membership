<?php

class OptimizePress_Elements_ScrollEnhancer
{
    /**
     * @var string
     */
    protected $tag = 'op_scroll_enhancer';

    /**
     * Registering actions and filters
     */
    public function __construct()
    {
        /*
         * Filters
         */
        add_filter('op_assets_after_addons', array($this, 'addToAssetList'));
        add_filter('op_assets_parse_list', array($this, 'addToParseList'));
        add_filter('op_assets_lang_list', array($this, 'addToLangList'));
        add_filter('op_assets_addons_path', array($this, 'elementPath'), 10, 2);
        add_filter('op_assets_addons_url', array($this, 'elementUrl'), 10, 2);
        add_filter('op_cacheable_elements', array($this, 'addToCacheableElementsList'));
        add_filter('op_assets_oppp_element_styles', array($this, 'elementOpppStyles'), 10, 2);

        /*
         * Actions
         */
        add_action('admin_enqueue_scripts', array($this, 'enqueueScripts'));
        add_action('wp_enqueue_scripts', array($this, 'enqueueScripts'));
        add_action('admin_enqueue_scripts', array($this, 'enqueueStyles'));
        add_action('wp_enqueue_scripts', array($this, 'enqueueStyles'));

        /*
         * Shortcodes
         */
        add_shortcode($this->tag, array($this, 'shortcode'));
    }

    /**
     * Add OPPP exclusive element styles to list for style picker to style differently.
     * @param  array $styles
     * @param  string $id
     * @return array
     */
    public function elementOpppStyles($styles, $id)
    {
        if ('op_assets_addon_' . $this->tag . '_style' === $id) {
            $styles = array_merge($styles, array(1, 2, 3, 4, 5, 6, 7, 8));
        }

        return $styles;
    }

    /**
     * Enqueue styles
     * @return void
     */
    public function enqueueStyles()
    {
        // Different dependencies when OP_SCRIPT_DEBUG is turned on and off (all styles are concatenated into opplus-front-all.css & opplus-back-all for production)
        if (OP_SCRIPT_DEBUG === '') {
            // wp_enqueue_style(OP_SN . 'common', OP_CSS . 'common' . OP_SCRIPT_DEBUG . '.css', array(), OP_VERSION, 'all');
            wp_enqueue_style('op-scroll-enhancer', OPPP_BASE_URL . 'css/elements/op_scroll_enhancer' . OP_SCRIPT_DEBUG . '.css', array(), OPPP_VERSION, 'all');
            wp_enqueue_style('op-animations-custom', OPPP_BASE_URL . 'css/components/' . 'op-animations-custom.css', array(), OPPP_VERSION, 'all');
        }
    }

    /**
     * Enqueue scripts
     * @return void
     */
    public function enqueueScripts()
    {
        // Different dependencies when OP_SCRIPT_DEBUG is turned on and off
        if (OP_SCRIPT_DEBUG === '') {
            // wp_enqueue_script('op-scroll-enhancer', OPPP_BASE_URL . 'js/elements/op_scroll_enhancer' . OP_SCRIPT_DEBUG . '.js', array(OP_SN . '-noconflict-js'), OPPP_VERSION, false);
            wp_enqueue_script('init-op-scroll-enhancer', OPPP_BASE_URL . 'js/elements/init_op_scroll_enhancer' . OP_SCRIPT_DEBUG . '.js', array(OP_SN . '-noconflict-js'), OPPP_VERSION, false);
            wp_enqueue_script('animatescroll', OPPP_BASE_URL . 'js/elements/animatescroll' . OP_SCRIPT_DEBUG . '.js', array(OP_SN . '-noconflict-js'), OPPP_VERSION, false);

        }
    }

    /**
     * Parses ('op_scroll_enhancer') shortcode
     * @param  array $atts
     * @param  string $content
     * @return string
     */
    public function shortcode($atts, $content)
    {
        // Element ID used for caching
        $elementId = 'el_se_' . md5(serialize($atts) . $content);

        // Cache busting
        if (function_exists('wp_using_ext_object_cache')) {
            $extCache = wp_using_ext_object_cache();
            if (true === $extCache) {
                wp_using_ext_object_cache(false);
            }
        }

        $output = '';

        if (false === $data = get_transient($elementId)) {

             // Decode encoded chars
            $atts = op_urldecode($atts);

            // Get the attributes from the shortcode
            $atts = shortcode_atts(array(
                'style'     => 1,
                'margin'    => 0,
                'element'   => '',
                'hover'     => '',
                'padding'   => 0,
                'speed'     => 800,
                'effect'    => 'swing',
                'animation' => 'bounce',
            ), $atts, $this->tag);

            // Assign unique ID
            $atts['id'] = 'op_scroll_enhancer_' . op_generate_id();

            // Render element from attributes
            $data = op_sl_parse('scroll_enhancer', $atts);

            if (false === is_array($data) && empty($data)) {
                return;
            }

            set_transient($elementId, $data, OP_SL_ELEMENT_CACHE_LIFETIME);
        }

        // Append output if defined
        if (isset($data['markup'])) {
            $output .= $data['markup'];
        }

        // Cache busting
        if (function_exists('wp_using_ext_object_cache')) {
            wp_using_ext_object_cache($extCache);
        }

        if (defined('OP_LIVEEDITOR')) {
            $output = '<div style="position:absolute;bottom:0;">!!! SCROLL ENHANCER ELEMENT !!!</div>' . $output;
        }

        return $output;
    }

    /**
     * Adds new element to asset list
     * @param  array $assets
     * @return array
     */
    public function addToAssetList($assets)
    {
        $assets['addon'][$this->tag] = array(
            'title'         => __('Scroll Enhancer', 'optimizepress-plus-pack'),
            'description'   => __('Use the scroll enhancer element to help guide your visitors to scroll down your page content, which leads to lower bounce rates and better conversions.', 'optimizepress-plus-pack'),
            'settings'      => 'Y',
            'image'         => OPPP_BASE_URL . 'images/elements/' . $this->tag . '/' . $this->tag . '.png',
            'base_path'     => OPPP_BASE_URL . 'js/elements/',
        );

        return $assets;
    }

    /**
     * Add additional strings for translation
     * @param array $strings
     * @return array
     */
    public function addToLangList($strings)
    {
        $strings['scroll_margin']         = __('Scroll Enhancer Element Top Margin (in pixels)', 'optimizepress-plus-pack');
        $strings['scroll_element']        = __('Scroll to Element (name tag, element ID or CSS selector)<br />if this option is missing we will use element after Scroll Enhancer Element', 'optimizepress-plus-pack');
        $strings['scroll_hover']          = __('Display Description on Scroll Enhancer Element Hover', 'optimizepress-plus-pack');
        $strings['scroll_padding']        = __('Padding (scroll position offset in pixels)', 'optimizepress-plus-pack');
        $strings['scroll_speed']          = __('Speed (miliseconds)', 'optimizepress-plus-pack');
        $strings['scroll_effect']         = __('Scroll Effect', 'optimizepress-plus-pack');
        $strings['effect_cancel']         = __('No Scroll', 'optimizepress-plus-pack');
        $strings['effect_swing']          = __('Scroll To', 'optimizepress-plus-pack');
        $strings['effect_none']           = __('Jump To', 'optimizepress-plus-pack');
        $strings['animation']             = __('Link Animation', 'optimizepress-plus-pack');
        $strings['animation_flash']       = __('Flash', 'optimizepress-plus-pack');
        $strings['animation_bounce']      = __('Bounce', 'optimizepress-plus-pack');
        $strings['animation_bounceIn']    = __('Bounce In', 'optimizepress-plus-pack');
        $strings['animation_bounceOut']   = __('Bounce Out', 'optimizepress-plus-pack');
        $strings['animation_fadeIn']      = __('Fade In', 'optimizepress-plus-pack');
        $strings['animation_fadeInUp']    = __('Fade In Up', 'optimizepress-plus-pack');
        $strings['animation_fadeInDown']  = __('Fade In Down', 'optimizepress-plus-pack');
        $strings['animation_fadeOut']     = __('Fade Out', 'optimizepress-plus-pack');
        $strings['animation_fadeOutUp']   = __('Fade Out Up', 'optimizepress-plus-pack');
        $strings['animation_fadeOutDown'] = __('Fade Out Down', 'optimizepress-plus-pack');
        $strings['animation_rotateIn']    = __('Rotate In', 'optimizepress-plus-pack');
        $strings['animation_rotateOut']   = __('Rotate Out', 'optimizepress-plus-pack');

        return $strings;
    }

    /**
     * Adds new element to parse list
     * @param  array $assets
     * @return array
     */
    public function addToParseList($assets)
    {
        $assets[$this->tag] = array(
            'asset' => 'addon/' . $this->tag,
            // 'child_tags' => array($this->childTag),
        );

        return $assets;
    }

    /**
     * Returns changed images path
     * @param  string $path
     * @param  string $tag
     * @return string
     */
    public function elementPath($path, $tag)
    {
        if ($tag === $this->tag) {
            $path = OPPP_BASE_DIR . 'images/elements/';
        }
        return $path;
    }

    /**
     * Returns changed images URL
     * @param  string $url
     * @param  string $tag
     * @return string
     */
    public function elementUrl($url, $tag)
    {
        if ($tag === $this->tag) {
            $url = OPPP_BASE_URL . 'images/elements/';
        }
        return $url;
    }

    /**
     * Add element key and name to cacheable elements list.
     * Filter is read in OP Helper Tools plugin and uses it to compile a list of elements whose transient cache can be cleared.
     * @param array $elements
     * @return array
     */
    public function addToCacheableElementsList($elements)
    {
        $elements['el_se_'] = __('Scroll Enhancer', 'optimizepress-plus-pack');

        return $elements;
    }
}

new OptimizePress_Elements_ScrollEnhancer();
