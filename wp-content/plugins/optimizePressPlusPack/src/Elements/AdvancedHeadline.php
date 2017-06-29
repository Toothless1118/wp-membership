<?php

class OptimizePress_Elements_AdvancedHeadline
{
    /**
     * @var string
     */
    protected $tag = 'op_advanced_headline';

    /**
     * @var string
     */
    protected $elementId;

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
            $styles = array_merge($styles, array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16));
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
            wp_enqueue_style('oppp-advanced-headline', OPPP_BASE_URL . 'css/elements/advanced_headline' . OP_SCRIPT_DEBUG . '.css', array(), OPPP_VERSION, 'all');
        }
    }

    /**
     * Enqueue scripts
     * @return void
     */
    public function enqueueScripts()
    {
        // Different dependencies when OP_SCRIPT_DEBUG is turned on and off
        if (!defined('OP_LIVEEDITOR') && (OP_SCRIPT_DEBUG === '')) {
            wp_enqueue_script('oppp-advanced-headline', OPPP_BASE_URL . 'js/elements/advanced_headline' . OP_SCRIPT_DEBUG . '.js', array(OP_SN . '-noconflict-js'), OPPP_VERSION, false);
        }
    }

    /**
     * Parses ('op_advanced_headline') shortcode
     * @param  array $atts
     * @param  string $content
     * @return string
     */
    public function shortcode($atts, $content)
    {
        $this->elementId = 'el_ah_' . md5(serialize($atts) . $content);

        /*
         * Cache busting
         */
        if (function_exists('wp_using_ext_object_cache')) {
            $extCache = wp_using_ext_object_cache();
            if (true === $extCache) {
                wp_using_ext_object_cache(false);
            }
        }

        $output = '';

        if (false === $data = get_transient($this->elementId)) {

            if (!isset($atts['style'])) {
                $atts['style'] = 1;
            }

            if (!isset($atts['effect'])) {
                $atts['effect'] = 'type';
            }

            $parts = array();
            for ($a = 0; $a < count($atts); $a += 1) {
                if (isset($atts['part_' . $a])) {
                    $parts[] = base64_decode($atts['part_' . $a]);
                } else {
                    break;
                }
            }

            $atts['element_id'] = $this->elementId;
            $atts['image_path'] = OPPP_BASE_URL . 'images/elements/' . $this->tag . '/';
            $atts['content']    = base64_decode($content);
            $atts['parts']      = $parts;
            $atts['font']       = op_asset_font_style($atts);
            $data = op_sl_parse('advanced_headline', $atts);

            if (false === is_array($data) && empty($data)) {
                return;
            }

            set_transient($this->elementId, $data, OP_SL_ELEMENT_CACHE_LIFETIME);
        }

        if (isset($data['markup'])) {
            $output .= $data['markup'];
        }

        // Loading font
        if (!empty($atts['font_family'])) {
            op_add_fonts($atts['font_family']);
        }
        // Adding Font Style
        op_asset_font_style($atts);

        /*
         * Cache busting
         */
        if (function_exists('wp_using_ext_object_cache')) {
            wp_using_ext_object_cache($extCache);
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
            'title'         => __('Advanced Headline', 'optimizepress-plus-pack'),
            'description'   => __('Draw the attention to your headlines with this powerful headline element', 'optimizepress-plus-pack'),
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
        $strings['effect']                  = __('Effect', 'optimizepress-plus-pack');
        $strings['rotate_x_axis']           = __('Rotate X-Axis', 'optimizepress-plus-pack');
        $strings['letters_slide_from_top']  = __('Letters Slide From Top', 'optimizepress-plus-pack');
        $strings['letters_rotate_y_axis']   = __('Letters Rotate Y-Axis', 'optimizepress-plus-pack');
        $strings['loading_bar']             = __('Loading Bar', 'optimizepress-plus-pack');
        $strings['slide_from_top']          = __('Slide From Top', 'optimizepress-plus-pack');
        $strings['clip']                    = __('Clip', 'optimizepress-plus-pack');
        $strings['zoom']                    = __('Zoom', 'optimizepress-plus-pack');
        $strings['scale']                   = __('Scale', 'optimizepress-plus-pack');
        $strings['push']                    = __('Push', 'optimizepress-plus-pack');
        $strings['accent']                  = __('Highlight colour', 'optimizepress-plus-pack');
        $strings['static_text']             = __('Static text', 'optimizepress-plus-pack');
        $strings['animated_text']           = __('Animated Text', 'optimizepress-plus-pack');
        $strings['type_fast']               = __('Type Fast', 'optimizepress-plus-pack');
        $strings['line_height_px']          = __('Line Height (px)', 'optimizepress-plus-pack');

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
        $elements['el_ah_'] = __('Advanced Headline', 'optimizepress-plus-pack');

        return $elements;
    }
}

new OptimizePress_Elements_AdvancedHeadline();
