<?php

class OptimizePress_Elements_PricingTable
{
    /**
     * @var string
     */
    protected $parentTag = 'pricing_table';

    /**
     * @var string
     */
    protected $childTag = 'pricing_table_child';

    /**
     * @var string
     */
    protected $id = '';

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
        add_action('admin_enqueue_scripts', array($this, 'enqueueStyles'));
        add_action('wp_enqueue_scripts', array($this, 'enqueueStyles'));
        add_action('wp_enqueue_scripts', array($this, 'enqueueScripts'));
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
            $styles = array_merge($styles, array(4, 5, 6, 7, 8));
        }

        return $styles;
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
     * Adds children to existing elements
     *
     * @param array $children
     * @return array
     */
    public function addToChildrenList($children)
    {
        $children['pricing_table'][] = 'op_' . $this->childTag;

        return $children;
    }

    /**
     * Enqueue styles
     * @return void
     */
    public function enqueueStyles()
    {
        // Different dependencies when OP_SCRIPT_DEBUG is turned on and off (all styles are concatenated into opplus-front-all.css & opplus-back-all for production)
        if (OP_SCRIPT_DEBUG === '') {
            wp_enqueue_style('oppp-pricing-table', OPPP_BASE_URL . 'css/elements/pricing_table' . OP_SCRIPT_DEBUG . '.css', array(), OPPP_VERSION, 'all');
        }
    }

    /**
     * Enqueue scripts
     * @return void
     */
    public function enqueueScripts()
    {
        if (OP_SCRIPT_DEBUG === '') {
            wp_enqueue_script('oppp-pricing-table-front', OPPP_BASE_URL . 'js/elements/init_pricing_table' . OP_SCRIPT_DEBUG . '.js', array(OP_SN . '-noconflict-js'), OPPP_VERSION, 'all');
        }
    }


    /**
     * Parses pricing_table and op_pricing_table shortcode
     * @param  array  $atts
     * @param  string $content
     * @return string
     */
    public function parentShortcode($atts, $content)
    {
        // Element id
        $this->id = op_generate_id();

        // Decode encoded chars
        $atts = op_urldecode($atts);

        //Get data from shortcodes
        $data = shortcode_atts(array(
            'style' => 1
        ), $atts);

        // fix tab/pricing_table conflict
        $content = preg_replace('/\[tab(.*?)\](.*?)\[\/tab\]/s', '[op_pricing_table_child$1]$2[/op_pricing_table_child]', $content);
        preg_match_all('/\[op_pricing_table_child(.*?)\](.*?)\[\/op_pricing_table_child\]/', $content, $matches);

        $content = do_shortcode(op_clean_shortcode_content($content));
        $content = op_process_asset_content($content);

        $data['id'] = $this->id;
        $data['atts'] = $atts;
        $data['content'] = $content;
        $data['total'] = isset($matches[0]) ? count($matches[0]) : 0;

        //Clear the template
        _op_tpl('clear');

        //Process the new template and load it
        return _op_tpl('_load_file', OPPP_BASE_DIR . 'templates/elements/pricing_table/' . $data['style'] . '_parent.php', $data, true);
    }

    /**
     * @param  array  $atts
     * @param  string $content
     * @return string
     */
    public function childShortcode($atts, $content)
    {
        // Decode encoded chars
        $atts = op_urldecode($atts);

        //Get data from shortcodes
        $data = shortcode_atts(array(
            'style' => 1,
            'total' => 0,
            'title' => '',
            'package_description' => '',
            'price' => '',
            'pricing_unit' => '',
            'pricing_variable' => '',
            'pricing_description' => '',
            'most_popular' => '',
            'most_popular_text' => '',
            'order_button_text' => '',
            'order_button_url' => '',
            'feature_description' => '',
            'items' => '',
            'li_class' => '',
            'a_class' => ''
        ), $atts);

        //Clear the template
        _op_tpl('clear');

        //Process the new template and load it
        return _op_tpl('_load_file', OPPP_BASE_DIR . 'templates/elements/pricing_table/' . $data['style'] . '_child.php', $data, true);
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
     * Add additional strings for translation
     * @param array $strings
     * @return array
     */
    public function addToLangList($strings)
    {
        $strings['pricing_description'] = __('Pricing Description', 'optimizepress-plus-pack');
        $strings['feature_description'] = __('Feature Description', 'optimizepress-plus-pack');

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

new OptimizePress_Elements_PricingTable();
