<?php

class OptimizePress_Elements_ProgressBar
{

    /**
     * @var string
     */
    protected $tag = 'progress_bar';

    /**
     * Registering actions and filters
     */
    public function __construct()
    {
        /*
         * Filters
         */
        add_filter('op_assets_before_addons', array($this, 'addToAssetList'), 100);
        // add_filter('op_assets_lang_list', array($this, 'addToLangList'));
        add_filter('op_assets_core_path', array($this, 'elementPath'), 10, 2);
        add_filter('op_assets_core_url', array($this, 'elementUrl'), 10, 2);
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
            $styles = array_merge($styles, array(4, 5, 6, 7));
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
            wp_enqueue_style('oppp-progress-bar', OPPP_BASE_URL . 'css/elements/progress_bar' . OP_SCRIPT_DEBUG . '.css', array(), OPPP_VERSION, 'all');
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
     * Parses progress_bar_box and op_progress_bar_box shortcode
     * @param  array $atts
     * @param  string $content
     * @return string
     */
    public function shortcode($atts, $content)
    {
        // Decode encoded chars
        $atts = op_urldecode($atts);

        //Extract shortcodes to variables
        $data = shortcode_atts(array(
            'style' => 1,
            'percentage' => 0,
            'id' => op_generate_id(),
            'color' => '',
            'content' => urldecode(op_clean_shortcode_content($content))
        ), $atts);

        wp_enqueue_script('jquery-ui-progressbar', false, array(OP_SN . '-noconflict-js'));

        _op_tpl('clear');

        return _op_tpl('_load_file', OPPP_BASE_DIR . 'templates/elements/progress_bar/' . $data['style'] . '.php', $data, true);
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
}

new OptimizePress_Elements_ProgressBar();
