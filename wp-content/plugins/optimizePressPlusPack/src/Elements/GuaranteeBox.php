<?php

class OptimizePress_Elements_GuaranteeBox
{
    /**
     * @var string
     */
    protected $contentTag = 'guarantee_content';

    /**
     * @var string
     */
    protected $boxTag = 'guarantee_box';

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
        if ('op_assets_core_' . $this->boxTag . '_style' === $id) {
            $styles = array_merge($styles, array(19, 20, 21, 22, 23, 24, 25));
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
            wp_enqueue_style('oppp-guaranteebox', OPPP_BASE_URL . 'css/elements/guaranteebox' . OP_SCRIPT_DEBUG . '.css', array(), OPPP_VERSION, 'all');
        }
    }

    /**
     * Initialize shortcodes (both with op prefix and without it)
     * @return void
     */
    public function initShortcodes()
    {
        add_shortcode($this->contentTag, array($this, 'contentShortcode'));
        add_shortcode('op_' . $this->contentTag, array($this, 'contentShortcode'));

        add_shortcode($this->boxTag, array($this, 'boxShortcode'));
        add_shortcode('op_' . $this->boxTag, array($this, 'boxShortcode'));
    }

    /**
     * Parses guarantee_content and op_guarantee_content shortcode
     * @param  array $atts
     * @param  string $content
     * @return string
     */
    public function contentShortcode($atts, $content)
    {
        return $this->boxShortcode($atts, $content);
    }

    /**
     * Parses guarantee_box and op_guarantee_box shortcode
     * @param  array $atts
     * @param  string $content
     * @return string
     */
    public function boxShortcode($atts, $content)
    {
        // Decode encoded chars
        $atts = op_urldecode($atts);

        //Extract the attributes into variables
        extract(shortcode_atts(array(
            'style' => '1',
            'title' => '',
        ), $atts, 'guarantee_box'));

        //Process attributes and settings to generate the HTML
        switch ($style){
            case 10:
            case 11:
            case 12:
            case 13:
            case 19:
            case 20:
            case 21:
            case 22:
            case 23:
            case 24:
            case 25:
                //Set up the data array for use with a template
                $data = array(
                    'title' => $title,
                    'title_style' => '',
                );

                //Clear out any current templates
                _op_tpl('clear');

                //Get the font styles
                $title_font = op_asset_font_style($atts);

                //Add HTML attribute for style if styles exist
                $data['title_style'] = (!empty($title_font) ? ' style=\'' . $title_font . '\'' : '');

                //Process font settings
                $font = op_asset_font_style($atts,'content_font_');
                $original_font_str = $GLOBALS['OP_LIVEEDITOR_FONT_STR'];

                //Add style attribute for font, if font exists
                if (!empty($font)) {
                    $style_str = ' style=\'' . $font . '\'';
                    $GLOBALS['OP_LIVEEDITOR_FONT_STR'] = array('elements' => array('p','a'), 'style_str' => $font);
                }

                //Process content
                $data['content'] = op_process_asset_content(op_texturize(do_shortcode(op_clean_shortcode_content($content))));

                //Return to the original string
                $GLOBALS['OP_LIVEEDITOR_FONT_STR'] = $original_font_str;

                //Return the HTML from the template
                return _op_tpl('_load_file', OPPP_BASE_DIR . 'templates/elements/guarantee_box/style_' . $style . '.php', $data, true);
                break;
            default:
                // If the style needs no processing, simply return the HTML
                $img_size = op_get_image_html_attribute(OPPP_BASE_URL . 'images/elements/guarantee_box/previews/guarantee_' . $style . '.png');
                return '<img src="' . OPPP_BASE_URL . 'images/elements/guarantee_box/previews/guarantee_' . $style . '.png" alt="" class="guarantee-box-' . $style . '" ' . $img_size . ' />';
        }
    }

    /**
     * Adds new element to asset list
     * @param  array $assets
     * @return array
     */
    public function addToAssetList($assets)
    {
        $assets['core'][$this->boxTag]['settings']  = 'Y';
        // $assets['core'][$this->boxTag]['image']     = OPPP_BASE_URL . 'images/elements/' . $this->boxTag . '/' . $this->boxTag . '.png';
        $assets['core'][$this->boxTag]['base_path'] = OPPP_BASE_URL . 'js/elements/';

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
        if ($tag === $this->boxTag) {
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
        if ($tag === $this->boxTag) {
            $url = OPPP_BASE_URL . 'images/elements/';
        }
        return $url;
    }
}

new OptimizePress_Elements_GuaranteeBox();