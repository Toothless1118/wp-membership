<?php

class OptimizePress_Elements_NewsBar
{

    /**
     * @var string
     */
    protected $tag = 'news_bar';

    /**
     * Registering actions and filters
     */
    public function __construct()
    {
        /*
         * Filters
         */
        add_filter('op_assets_before_addons', array($this, 'addToAssetList'), 100);
        add_filter('op_assets_lang_list', array($this, 'addToLangList'));
        add_filter('op_assets_core_path', array($this, 'elementPath'), 10, 2);
        add_filter('op_assets_core_url', array($this, 'elementUrl'), 10, 2);
        add_filter('op_cacheable_elements', array($this, 'addToCacheableElementsList'));
        add_filter('op_assets_oppp_element_styles', array($this, 'elementOpppStyles'), 10, 2);

        /*
         * Actions
         */
        add_action('admin_enqueue_scripts', array($this, 'enqueueStyles'));
        add_action('wp_enqueue_scripts', array($this, 'enqueueStyles'));
        add_action('op_assets_after_shortcode_init', array($this, 'initShortcodes'));
    }

    /**
     * Add OPPP exclusive element styles to list for style picker to style differently.
     * @param  array $styles
     * @param  string $id
     * @return array
     */
    public function elementOpppStyles($styles, $id)
    {
        if ('op_assets_core_' . $this->tag . '_style' === $id) {
            $styles = array_merge($styles, array(2, 3, 4, 5, 6, 7));
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
            wp_enqueue_style('oppp-news-bar', OPPP_BASE_URL . 'css/elements/news_bar' . OP_SCRIPT_DEBUG . '.css', array(), OPPP_VERSION, 'all');
        }
    }

    /**
     * Initialize shortcodes (both with op prefix and without it)
     * @return void
     */
    public function initShortcodes()
    {
        add_shortcode($this->tag, array($this, 'shortcode'));
        add_shortcode('op_' . $this->tag, array($this, 'shortcode'));
    }

    /**
     * Parses news_bar_box and op_news_bar_box shortcode
     * @param  array $atts
     * @param  string $content
     * @return string
     */
    public function shortcode($atts, $content)
    {
        // Element ID used for caching
        $elementId = 'el_nb_' . md5(serialize($atts));

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

            //Initialize variables
            $atts = shortcode_atts(array(
                'style'              => 1,
                'color'              => '#004a80',
                'feature_font_color' => '#ffffff',
                'feature_text'       => '',
                'feature_url'        => '',
                'feature_width'      => 'auto',
                'feature_position'   => 'left',
                'main_background'    => '#f2f2f2',
                'main_font_color'    => '#444444',
                'main_text'          => '',
            ), $atts);

            $atts['id'] = 'news-bar-' . op_generate_id();

            // Render element from attributes
            $data = op_sl_parse($this->tag, $atts);

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

        return $output;
    }

    /**
     * Adds new element to asset list
     * @param  array $assets
     * @return array
     */
    public function addToAssetList($assets)
    {
        $assets['core'][$this->tag]['settings']  = 'Y';
        // $assets['core'][$this->tag]['image']     = OPPP_BASE_URL . 'images/elements/' . $this->tag . '/' . $this->tag . '.png';
        $assets['core'][$this->tag]['base_path'] = OPPP_BASE_URL . 'js/elements/';

        return $assets;
    }

    /**
     * Add additional strings for translation
     * @param array $strings
     * @return array
     */
    public function addToLangList($strings)
    {
        $strings['news_bar_instructions'] = __('Use the below form to set additional parameters of news bar element', 'optimizepress-plus-pack');
        $strings['color'] = __('Feature background', 'optimizepress-plus-pack');
        $strings['feature_font_color'] = __('Feature font colour', 'optimizepress-plus-pack');
        $strings['feature_url'] = __('Redirect page on feature box click', 'optimizepress-plus-pack');
        $strings['feature_width'] = __('Size of feature box (auto|value px|value %|value em...).', 'optimizepress-plus-pack');
        $strings['feature_position'] = __('Feature box position', 'optimizepress-plus-pack');
        $strings['feature_position_left'] = __('Left', 'optimizepress-plus-pack');
        $strings['feature_position_right'] = __('Right', 'optimizepress-plus-pack');
        $strings['main_background'] = __('Main background', 'optimizepress-plus-pack');
        $strings['main_font_color'] = __('Main font colour', 'optimizepress-plus-pack');

        return $strings;
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
        $elements['el_nb_'] = __('News Bar', 'optimizepress-plus-pack');

        return $elements;
    }
}

new OptimizePress_Elements_NewsBar();
