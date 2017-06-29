<?php

class OptimizePress_Elements_Testimonials
{

    /**
     * @var string
     */
    protected $tag = 'testimonials';

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
            $styles = array_merge($styles, array(17, 18, 19, 20, 21, 22));
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
            wp_enqueue_style('oppp-testimonials', OPPP_BASE_URL . 'css/elements/testimonials' . OP_SCRIPT_DEBUG . '.css', array(), OPPP_VERSION, 'all');
	        wp_enqueue_style(OP_SN . '-flexslider', OPPP_BASE_URL . 'css/elements/flexslider' . OP_SCRIPT_DEBUG . '.css', array(), OPPP_VERSION, 'all');
            wp_enqueue_style(OP_SN.'-flexslider-custom', OPPP_BASE_URL . 'css/elements/flexslider-custom' . OP_SCRIPT_DEBUG . '.css', array(OP_SN.'-flexslider'), OPPP_VERSION, 'all');
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
     * Parses testimonial_box and testimonial shortcode
     * @param  array $atts
     * @param  string $content
     * @return string
     */
    public function shortcode($atts, $content)
    {
        // Decode encoded chars
        $atts = op_urldecode($atts);

        //Extract the shortcodes into variables
        extract(shortcode_atts(array(
            'style' => 1,
            'margin_top' => '',
            'margin_bottom' => ''
        ), $atts));

        //Initialize style arrays
        $cont_styles = array(1, 5, 9, 11, 14);
        $tblock_styles = array(5);

        //Initialize the return string
        $str = '';

        //Make sure there is a testimonial in the shortcode
        $mc = preg_match_all('/'.op_shortcode_regex('testimonial').'/s',$content,$matches);

        //Continue if there are matches
        if($mc > 0){
            //Loop through the testimonial elements
            for($i=0;$i<$mc;$i++){
                //Extract the data from the child shortcode
                $data = shortcode_atts(array(
                    'name' => '',
                    'company' => '',
                    'href' => '',
                    'image' => '',
                    'button_text' => '',
                    'header_color' => '',
                ), shortcode_parse_atts($matches[3][$i]));

                // Decode encoded chars
                if (is_array($data)) {
                    foreach($data as $key => $att) {
                        $data[$key] = urldecode($att);
                    }
                }

                //Get this testimonials content
                $data['content'] = nl2br(urldecode($matches[5][$i]));

                //Generate a font style out of the font attribute
                $data['font_str'] = op_asset_font_style($atts);

                //Get the current font settings
                $original_font_str = $GLOBALS['OP_LIVEEDITOR_FONT_STR'];

                //If font settings are present then set the style string and set the font string
                if(!empty($data['font_string'])) $GLOBALS['OP_LIVEEDITOR_FONT_STR'] = array('elements' => array('p','a'), 'style_str' => $font);

                //Return the font string back to normal
                $GLOBALS['OP_LIVEEDITOR_FONT_STR'] = $original_font_str;

                //Add margin to the data array
                $data['margin_top'] = $margin_top;
                $data['margin_bottom'] = $margin_bottom;

                //Decode encoded characters (needed for correct parsing of shortcodes)
                // $data['name'] = urldecode($data['name']);
                // $data['company'] = urldecode($data['company']);
                // $data['href'] = urldecode($data['href']);
                // $data['image'] = urldecode($data['image']);

                //Generate the element ID
                $data['id'] = 'testimonial-'.op_generate_id();

                //Clear out the template and reload it with the style of this testimonial, putting the
                //returned HTML into the display string
                _op_tpl('clear');
                $str .= _op_tpl('_load_file',OPPP_BASE_DIR.'templates/elements/testimonials/style_'.$style.'.php',$data,true);
            }

            //If there is a container div in this style, add it to the HTML
            if (in_array($style, $cont_styles))
                $str = '<div class="'.(in_array($style, $tblock_styles) ? 'testimonial-block-three cf' : 'testimonial-block cf').'">'.$str.'</div>';
        }

        return $str;
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

new OptimizePress_Elements_Testimonials();
