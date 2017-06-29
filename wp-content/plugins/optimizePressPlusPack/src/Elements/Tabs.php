<?php

class OptimizePress_Elements_Tabs
{
    /**
     * @var string
     */
    protected $tag = 'tabs';

    /**
     * @var string
     */
    protected $childElement = 'tab';

    /**
     * @var string
     */
    public $elementId = '';

    /**
     * @var string
     */
    public $elementStyle = '';

    /**
     * @var int
     */
    public $elementCount = 0;

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
        add_filter('op_assets_oppp_element_styles', array($this, 'elementOpppStyles'), 10, 2);

        /*
         * Actions
         */
        add_action('admin_enqueue_scripts', array($this, 'enqueueScripts'));
        add_action('wp_enqueue_scripts', array($this, 'enqueueScripts'));
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
            $styles = array_merge($styles, array(2, 3, 4, 5));
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
            wp_enqueue_style('oppp-tabs', OPPP_BASE_URL . 'css/elements/tabs' . OP_SCRIPT_DEBUG . '.css', array(), OPPP_VERSION, 'all');
        }
    }
        /**
     * Enqueue scripts
     * @return void
     */
    public function enqueueScripts()
    {
        if (OP_SCRIPT_DEBUG === '') {
            wp_enqueue_script('oppp-tabs', OPPP_BASE_URL . 'js/components/jquery-ui.js', array(OP_SN . '-noconflict-js'), OPPP_VERSION, true);
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
        add_shortcode($this->childElement, array($this, 'childElement'));
    }

    /**
     * Parses guarantee_box and op_guarantee_box shortcode
     * @param  array $atts
     * @param  string $content
     * @return string
     */
    public function shortcode($atts, $content)
    {
        //Initialize variables
        $html = '';

        // Decode encoded chars
        $atts = op_urldecode($atts);
        $this->elementId = op_generate_id();
        $this->elementCount = 1;
        $this->elementStyle = $atts['style'];

        $font = op_asset_font_style($atts);

        if($font != ''){
            $style_str = ' style=\''.$font.'\'';
            $GLOBALS['OP_LIVEEDITOR_FONT_STR'] = array('elements' => array('p','a'), 'style_str' => $font);
        }

        //Get data from shortcodes
        $data = shortcode_atts(array(
            'style' => 1,
            'has_icon' => '0',
            'tabs'  => array(),
            'tabs_html' => '',
            'id'    => $this->elementId,
            'color' => '#798123',
            'color_select' => '',
            'content' => do_shortcode($content)
        ), $atts);

        //Clean up the data before processing it
        $content = op_clean_shortcode_content($content);

        //Get the tabs from the passed data
        $mc = preg_match_all('/' . op_shortcode_regex('tab') . '/s', $content, $matches);

        //Ensure there is at least one tab
        if ($mc > 0) {
            //Loop through the tabs
            for ($i = 0; $i < $mc; $i++) {
                //Put this tab in the data array to be passed to template
                array_push($data['tabs'], shortcode_atts(array(
                    'title'     => '',
                    'li_class'  => '',
                    'a_class'   => '',
                    'icon'      => '',
                    'id'        => $i + 1
                ), shortcode_parse_atts($matches[3][$i])));

                // Decode encoded tab attribute chars
                if (is_array($data['tabs'][$i])) {
                    foreach($data['tabs'][$i] as $key => $att) {
                        $data['tabs'][$i][$key] = urldecode($att);
                    }
                }

            }
        }

        // li elements
        foreach ($data['tabs'] as $key => $value) {
            $li_class = '';
            if (!empty($value['li_class'])) {
                $li_class .= ' ' . urldecode($value['li_class']);
            }
            if ($key == 0) {
                $li_class .= ' ' . 'ui-tabs-active ui-state-active';
            }

            $a_class = '';
            if (!empty($value['a_class'])) {
                $a_class .= ' ' . urldecode($value['a_class']);
            }

            $data['tabs_html'] .= '<li' . ($li_class ? ' class="' . trim($li_class) . '"' : '') . '>';
                $data['tabs_html'] .= '<a href="#tab-' . $this->elementId . '-' . ($key + 1) . '"' . ($a_class ? ' class="' . trim($a_class) . '"' : '') . '>';
                    $data['tabs_html'] .= ($data['has_icon'] == '1' ? ('<img src="' . OPPP_BASE_URL . '/images/elements/tabs/img/' . urldecode($value['icon']) . '" alt="" width="20" height="20" />') : '');
                    //$data['tabs_html'] .= ($data['has_icon'] == '1' ? ('<i class="entypo-' . preg_replace('/\.svg$/', '', urldecode($value['icon'])) . '"></i>') : '');
                    $data['tabs_html'] .= '<span>';
                        $data['tabs_html'] .= urldecode($value['title']);
                    $data['tabs_html'] .= '</span>';
                $data['tabs_html'] .= '</a>';
            $data['tabs_html'] .= '</li>';
        }

        _op_tpl('clear');

        return _op_tpl('_load_file', OPPP_BASE_DIR . 'templates/elements/tabs/style_'.$data['style'].'.php', $data, true);
    }

    /**
     * Child element
     * @param  array  $atts
     * @param  string $content
     * @return string
     */
    public function childElement($atts, $content = '')
    {
        $content = do_shortcode(op_texturize(op_clean_shortcode_content(urldecode($content))));
        $content = op_process_asset_content($content);
        $content = '<div id="tab-' . $this->elementId . '-' . $this->elementCount . '" class="tab-content">' . $content . '</div>';

        $this->elementCount++;

        return $content;
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

new OptimizePress_Elements_Tabs();
