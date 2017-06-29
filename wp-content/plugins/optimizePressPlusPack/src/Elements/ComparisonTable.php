<?php

class OptimizePress_Elements_ComparisonTable
{
    /**
     * @var string
     */
    protected $parentTag = 'op_comparison_table';

    /**
     * @var string
     */
    protected $childTag = 'op_comparison_table_item';

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
        if ('op_assets_addon_' . $this->parentTag . '_style' === $id) {
            $styles = array_merge($styles, array(1, 2, 3, 4, 5));
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
        add_shortcode($this->childTag, array($this, 'childShortcode'));
    }

    /**
     * Adds children to existing elements
     *
     * @param array $children
     * @return array
     */
    public function addToChildrenList($children)
    {

        $children['addon'][$this->parentTag][] = $this->childTag;

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
            wp_enqueue_style('oppp-comparison-table', OPPP_BASE_URL . 'css/elements/op_comparison_table' . OP_SCRIPT_DEBUG . '.css', array(), OPPP_VERSION, 'all');
        }
    }

    /**
     * Enqueue scripts
     * @return void
     */
    public function enqueueScripts()
    {

        if (OP_SCRIPT_DEBUG === '' && !wp_script_is(OP_SN . 'comparison-table-front', 'enqueued')) {
            wp_enqueue_script(OP_SN . 'comparison-table-front', OPPP_BASE_URL . 'js/elements/init_comparison_table' . OP_SCRIPT_DEBUG . '.js', array(OP_SN . '-noconflict-js'), OPPP_VERSION, 'all');
            wp_localize_script( OP_SN . 'comparison-table-front', 'oppp_path', OPPP_BASE_URL );
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
        // Element ID used for caching
        $elementId = 'el_ct_' . md5(serialize($atts) . $content);

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

            //Get data from shortcodes
            $atts = shortcode_atts(array(
                'style'             => 1,
                'features'          => '',
                'guarantee_text'    => '',
                'guarantee_icon'    => '0.png',
            ), $atts);

            // fix tab/pricing_table conflict
            // $content = preg_replace('/\[tab(.*?)\](.*?)\[\/tab\]/', '[op_pricing_table_child$1]$2[/op_pricing_table_child]', $content);
            preg_match_all('/\[op_comparison_table_item(.*?)\](.*?)\[\/op_comparison_table_item\]/', $content, $matches);
            $content = do_shortcode(op_clean_shortcode_content($content));
            $content = op_process_asset_content($content);

            // Add width & height attributes to icon images
            preg_match_all('/src=\'(.*?)\'/im', $content, $results);
            foreach ($results[1] as $src) {
                $imgSize = op_get_image_html_attribute($src);
                $imgSize = str_replace('"', "'", $imgSize);
                $content = str_replace($src . "'", $src . "' " . $imgSize, $content);
            }

            $atts['id'] = op_generate_id();
            $atts['oppp_base_url'] = OPPP_BASE_URL;
            $atts['content'] = $content;
            $atts['total'] = isset($matches[0]) ? count($matches[0]) : 0;
            $atts['guarantee_icon_size_attributes'] = op_get_image_html_attribute(OPPP_BASE_URL . 'images/elements/op_comparison_table/guarantee/' . $atts['guarantee_icon']);

            // Render element from attributes
            $data = op_sl_parse('comparison_table', $atts);

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
            'features' => '',
            'li_class' => '',
            'a_class' => ''
        ), $atts);

        //Clear the template
        _op_tpl('clear');

        //Process the new template and load it
        return _op_tpl('_load_file', OPPP_BASE_DIR . 'templates/elements/comparison_table/' . $data['style'] . '_child.php', $data, true);
    }

    /**
     * Adds new element to asset list
     * @param  array $assets
     * @return array
     */
    public function addToAssetList($assets)
    {
        // This element is only available in
        // live editor (not on posts),
        // since autop messes
        // up its markup
        if (defined('OP_LIVEEDITOR')) {
            $assets['addon'][$this->parentTag] = array(
                'title'         => __('Pricing Comparison Table', 'optimizepress-plus-pack'),
                'description'   => __('Educate visitors and increase conversions with the pricing comparisons table element.', 'optimizepress-plus-pack'),
                'settings'      => 'Y',
                'image'         => OPPP_BASE_URL . 'images/elements/' . $this->parentTag . '/' . $this->parentTag . '.png',
                'base_path'     => OPPP_BASE_URL . 'js/elements/',
            );
        }

        return $assets;
    }

    /**
     * Add additional strings for translation
     * @param array $strings
     * @return array
     */
    public function addToLangList($strings)
    {
        $strings['features'] = __('Features', 'optimizepress-plus-pack');
        $strings['comparison_table_info'] = __('Features added must be connected to the columns in Columns section.', 'optimizepress-plus-pack');
        $strings['guarantee'] = __('Guarantee', 'optimizepress-plus-pack');
        $strings['guarantee_description'] = __('Guarantee text');
        $strings['guarantee_info'] = __('Guarantee will be visible in the table header, above the features section.');

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


    /**
     * Adds new element to parse list
     * @param  array $assets
     * @return array
     */
    public function addToParseList($assets)
    {
        $assets[$this->parentTag] = array(
            'asset' => 'addon/' . $this->parentTag,
            'child_tags' => array($this->childTag),
        );

        return $assets;
    }

    /**
     * Add element key and name to cacheable elements list.
     * Filter is read in OP Helper Tools plugin and uses it to compile a list of elements whose transient cache can be cleared.
     * @param array $elements
     * @return array
     */
    public function addToCacheableElementsList($elements)
    {
        $elements['el_ct_'] = __('Comparison Table', 'optimizepress-plus-pack');

        return $elements;
    }
}

new OptimizePress_Elements_ComparisonTable();
