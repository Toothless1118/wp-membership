<?php

class OptimizePress_Elements_QNAElement
{
    /**
     * @var string
     */
    protected $parentTag = 'qna_elements';

    /**
     * @var string
     */
    protected $childTag = 'question';

    /**
     * @var string
     */
    protected $selectedStyle;

    /**
     * Registering actions and filters
     */
    public function __construct()
    {
        /*
         * Filters
         */
        add_filter('op_assets_before_addons', array($this, 'addToAssetList'), 100);
        add_filter('op_assets_children_list', array($this, 'addToChildrenList'));
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
        if ('op_assets_core_' . $this->parentTag . '_style' === $id) {
            $styles = array_merge($styles, array("style3","style4","style5","style6","style7"));
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
            wp_enqueue_style('op_qna_elements', OPPP_BASE_URL . 'css/elements/qna_elements' . OP_SCRIPT_DEBUG . '.css', array(), OPPP_VERSION, 'all');
        }
    }

    /**
     * Enqueue scripts
     * @return void
     */
    public function enqueueScripts()
    {
        if (OP_SCRIPT_DEBUG === '') {
            wp_enqueue_script('op_qna_elements', OPPP_BASE_URL . 'js/elements/init_qna_elements' . OP_SCRIPT_DEBUG . '.js', array(OP_SN . '-noconflict-js', 'jquery-ui-accordion'), OPPP_VERSION, true);
        }
    }

    /**
     * Initialize shortcodes (both with op prefix and without it)
     * @return void
     */
    public function initShortcodes()
    {
        add_shortcode($this->parentTag, array($this, 'parentShortcode'));
        add_shortcode('op_' . $this->parentTag, array($this, 'parentShortcode'));

        add_shortcode($this->childTag, array($this, 'childShortcode'));
        add_shortcode('op_' . $this->childTag, array($this, 'childShortcode'));
    }

    /**
     * Parses guarantee_box and op_guarantee_box shortcode
     * @param  array $atts
     * @param  string $content
     * @return string
     */
    public function parentShortcode($atts, $content)
    {
        // Decode encoded chars
        $atts = op_urldecode($atts);

        extract(shortcode_atts(array(
            'style' => 'qa-text',
            'columns' => '2'
        ), $atts));

        $data = array(
            'columns' => '',
            'style' => '',
            'content' => ''
        );

        switch($columns){
            case 3:
                $columns = 'three';
                break;
            default:
                $columns = 'two';
                break;
        }

        $this->selectedStyle = $style;

        $data['columns'] = $columns;
        $data['style'] = $style;
        $data['content'] = do_shortcode(op_clean_shortcode_content($content));

        return _op_tpl('_load_file', OPPP_BASE_DIR . 'templates/elements/qna_elements/' . $style . '_parent.php', $data, true);
    }

    /**
     * Parses guarantee_box and op_guarantee_box shortcode
     * @param  array $atts
     * @param  string $content
     * @return string
     */
    public function childShortcode($atts, $content)
    {
        // Decode encoded chars
        $atts = op_urldecode($atts);
        $content = op_urldecode($content);

        $data = shortcode_atts(array(
            'question' => '',
            'titletextcolor' => '',
            'textcolor' => '',
            'bgcolor' => ''
        ), $atts);

        $data['content'] = op_texturize(do_shortcode(op_clean_shortcode_content($content)));

        return _op_tpl('_load_file', OPPP_BASE_DIR . 'templates/elements/qna_elements/' . $this->selectedStyle . '_child.php', $data, true);
    }

    /**
     * Adds new element to asset list
     * @param  array $assets
     * @return array
     */
    public function addToAssetList($assets)
    {
        $assets['core'][$this->parentTag]['settings']  = 'Y';
        // $assets['core'][$this->parentTag]['image']     = OPPP_BASE_URL . 'images/elements/' . $this->parentTag . '/' . $this->parentTag . '.png';
        $assets['core'][$this->parentTag]['base_path'] = OPPP_BASE_URL . 'js/elements/';

        return $assets;
    }

    /**
     * Adds children to existing elements
     *
     * @param array $children
     * @return array
     */
    public function addToChildrenList($children)
    {
        $children['qna_elements'][] = $this->childTag;
        $children['qna_elements'][] = 'op_' . $this->childTag;

        return $children;
    }

    /**
     * Add additional strings for translation
     * @param array $strings
     * @return array
     */
    public function addToLangList($strings)
    {
        $strings['title_text_color']    = __('Title text Colour', 'optimizepress-plus-pack');
        $strings['text_color']          = __('Text Colour', 'optimizepress-plus-pack');

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
        if ($tag === $this->parentTag) {
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
        if ($tag === $this->parentTag) {
            $url = OPPP_BASE_URL . 'images/elements/';
        }
        return $url;
    }
}

new OptimizePress_Elements_QNAElement();
