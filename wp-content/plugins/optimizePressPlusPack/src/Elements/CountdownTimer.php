<?php

class OptimizePress_Elements_CountdownTimer
{
    /**
     * @var string
     */
    protected $tag = 'countdown_timer';

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
        add_filter('wp_print_styles', array($this, 'inLiveEditorRenderCSS'));
        add_filter('op_assets_oppp_element_styles', array($this, 'elementOpppStyles'), 10, 2);

        /*
         * Actions
         */
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
            $styles = array_merge($styles, array(4, 5, 6, 7, 8, 9, 10, 11, 12, 13));
        }

        return $styles;
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
     * Parses countdown_timer and op_countdown_timer shortcode
     * @param  array $atts
     * @param  string $content
     * @return string
     */
    public function shortcode($atts, $content)
    {
        // Decode encoded chars
        $atts = op_urldecode($atts);

        //Get the attributes from the shortcode
        $data = shortcode_atts(array(
            'style' => 1,
            'end_date' => '',
            'redirect_url' => '',
            'id' => 'countdown_timer_' . op_generate_id(),
            'years_text_singular' => __('Year', 'optimizepress-plus-pack'),
            'years_text' => __('Years', 'optimizepress-plus-pack'),
            'months_text_singular' => __('Month', 'optimizepress-plus-pack'),
            'months_text' => __('Months', 'optimizepress-plus-pack'),
            'weeks_text_singular' => __('Week', 'optimizepress-plus-pack'),
            'weeks_text' => __('Weeks', 'optimizepress-plus-pack'),
            'days_text_singular' => __('Day', 'optimizepress-plus-pack'),
            'days_text' => __('Days', 'optimizepress-plus-pack'),
            'hours_text_singular' => __('Hour', 'optimizepress-plus-pack'),
            'hours_text' => __('Hours', 'optimizepress-plus-pack'),
            'minutes_text_singular' => __('Minute', 'optimizepress-plus-pack'),
            'minutes_text' => __('Minutes', 'optimizepress-plus-pack'),
            'seconds_text_singular' => __('Second', 'optimizepress-plus-pack'),
            'seconds_text' => __('Seconds', 'optimizepress-plus-pack'),
        ), $atts);

        //Clear the template
        _op_tpl('clear');

        // Different dependencies when OP_SCRIPT_DEBUG is turned on and off (all styles are concatenated into opplus-front-all.css & opplus-back-all for production)
        if (OP_SCRIPT_DEBUG === '') {
            wp_enqueue_style(OP_SN . '-countdown_timer', OPPP_BASE_URL . 'css/elements/countdown_timer' . OP_SCRIPT_DEBUG . '.css', array(), OPPP_VERSION, 'all');
        }

        //Process the new template and load it
        return _op_tpl('_load_file', OPPP_BASE_DIR . 'templates/elements/countdown_timer/'.$data['style'] . '.php',$data,true);
    }

    /**
     * Adds countdown_timer CSS in Live Editor
     * @return void
     */
    public function inLiveEditorRenderCSS()
    {
        if (defined('OP_LIVEEDITOR')) {
            wp_enqueue_style(OP_SN . '-countdown_timer', OPPP_BASE_URL . 'css/elements/countdown_timer' . OP_SCRIPT_DEBUG . '.css', array(), OPPP_VERSION, 'all');
        }
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

new OptimizePress_Elements_CountdownTimer();