<?php

class Oppp_Elements_Product_Showcase
{
    /**
     * @var OptimizePress_Product_Showcase
     */
    protected static $instance;

    /**
     * @var string
     */
    protected $parentTag = 'op_product_showcase';

    /**
     * @var string
     */
    protected $childTag = 'op_product_showcase_child';

    /**
     * @var string
     */
    protected $elementId;

    /**
     * @var string
     */
    protected $elementStyle;

    /**
     * @var number
     */
    protected $currentSlide = 1;

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
        add_filter('wp_print_styles', array($this, 'inLiveEditorRenderCSS'));
        add_filter('op_cacheable_elements', array($this, 'addToCacheableElementsList'));
        add_filter('op_assets_oppp_element_styles', array($this, 'elementOpppStyles'), 10, 2);

        /*
         * Shortcodes
         */
        add_shortcode($this->parentTag, array($this, 'parentShortcode'));
        add_shortcode($this->childTag, array($this, 'childShortcode'));
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
            $styles = array_merge($styles, array(1, 2));
        }

        return $styles;
    }

    /**
     * Parses parent ('op_product_showcase') shortcode
     * @param  array $atts
     * @param  string $content
     * @return string
     */
    public function parentShortcode($atts, $content)
    {
        // Decode encoded chars
        $atts = op_urldecode($atts);

        $this->elementId = 'prsh_' . md5(serialize($atts) . $content);

        $atts = shortcode_atts(array(
            'style'                         => 1,
            'element_size'                  => '100',
            'element_border'                => '',
            'animation_type'                => 'fade',
            'animation_speed'               => 300,
            'thumbnail_size'                => 'medium',
            'selected_image_border_color'   => '#aed3ef',
            'instruction_text'   => ''
        ), $atts, $this->parentTag);

        $this->elementStyle = $atts['style'];

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

        if (false === $data = get_transient('el_' . $this->elementId)) {

            $atts['element_id'] = $this->elementId;
            $data = op_sl_parse('product_showcase', $atts);

            if (false === is_array($data) && empty($data)) {
                return;
            }

            set_transient('el_' . $this->elementId, $data, OP_SL_ELEMENT_CACHE_LIFETIME);
        }

        // Dependency is different for production and dev environments
        if (wp_script_is(OP_SN.'-noconflict-js', 'enqueued')) {
            $js_dependency = OP_SN.'-noconflict-js';
        } else {
            $js_dependency = OP_SN.'-op-jquery-base-all';
        }

        /*
         * We are loading JS only when the page is renderding and not in Live Editor
         */
        if (!defined('OP_LIVEEDITOR')) {

            if (OP_SCRIPT_DEBUG === '') {
                wp_enqueue_script(OP_SN . '-flexslider', OPPP_BASE_URL . 'js/elements/jquery.flexslider.min.js', array($js_dependency), OPPP_VERSION, false);
                wp_enqueue_script(OP_SN . '-zoom', OPPP_BASE_URL . 'js/components/jquery.zoom.min.js', array($js_dependency), OPPP_VERSION, false);
            }

        }

        if (OP_SCRIPT_DEBUG === '') {
            wp_enqueue_style(OP_SN.'-flexslider', OPPP_BASE_URL . 'css/elements/flexslider' . OP_SCRIPT_DEBUG . '.css', array(), OPPP_VERSION, 'all');
            wp_enqueue_style(OP_SN.'-addon-product-showcase', OPPP_BASE_URL . 'css/elements/op_product_showcase' . OP_SCRIPT_DEBUG . '.css', array(dOP_SN.'-flexslider'), OPPP_VERSION, 'all');
        }

        if (isset($data['markup'])) {

            // If some thumb attribute is for some reason left empty,
            // we replace it with real image source to ensure that no thumbnails are broken
            $thumb_content = preg_replace('/(image="(\S+)")\s(thumbnail=\"\")/i', '$1 thumbnail="$2"', $content);

            // We replace remove all image attributes and then...
            $thumb_content = preg_replace('/image(_width|_height)?=".*?"/i', '', $thumb_content);

            // ...change all thumbnail attributes to images, so that shortcode on second pass correctly pareses thumbnails
            $thumb_content = preg_replace('/thumbnail/i', 'image', $thumb_content);

            $output .= sprintf($data['markup'], do_shortcode($content), do_shortcode($thumb_content));

        }

        if (isset($data['javascript']) && !defined('OP_LIVEEDITOR')) {
            $output .= $data['javascript'];
        }

        /*
         * Cache busting
         */
        if (function_exists('wp_using_ext_object_cache')) {
            wp_using_ext_object_cache($extCache);
        }

        return $output;
    }

    /**
     * Adds slider CSS in Live Editor
     * @return void
     */
    public function inLiveEditorRenderCSS()
    {
        if (defined('OP_LIVEEDITOR') && OP_SCRIPT_DEBUG === '') {
            wp_enqueue_style(OP_SN.'-addon-product-showcase', OPPP_BASE_URL . 'css/elements/op_product_showcase' . OP_SCRIPT_DEBUG . '.css', array(), OPPP_VERSION, 'all');
        }
    }

    /**
     * Parses parent ('op_product_showcase') shortcode
     * @param  array $atts
     * @param  string $content
     * @return string
     */
    public function childShortcode($atts, $content)
    {

        // Decode encoded chars
        $atts = op_urldecode($atts);

        $atts = shortcode_atts(array(
            'image' => '',
            'image_width' => '',
            'image_height' => '',
            'description' => '',
        ), $atts, $this->childTag);

        $output = '';

        $slideStyle = '';
        $image = !empty($atts['image']) ? $atts['image'] : '';
        $image_width = !empty($atts['image_width']) ? $atts['image_width'] : '';
        $image_height = !empty($atts['image_height']) ? $atts['image_height'] : '';
        $description = !empty($atts['description']) ? $atts['description'] : '';

        $output .= '<li>
                        <div class="op-product-showcase-photo-wrap">
                            <img src="' . $image . '" width="' . $image_width . '" height="' . $image_height . '" alt="' . htmlspecialchars($description) . '" />
                        </div>
                    </li>';

        return $output;

    }

    /**
     * Adds custom translations for JS strings needed by custom element
     * @param  array $strings
     * @return array
     */
    public function addToLangList($strings)
    {
        $strings['instruction_text']                        = __('Instruction Text', 'optimizepress-plus-pack');
        $strings['product_showcase_image']                  = __('Image<br /> <em>Please upload at least 1000px wide image to enable zoom functionality.</em>', 'optimizepress-plus-pack');
        $strings['product_showcase_image_description']      = __('Image alt Text <br /><em>This text will not be visible on the page. It serves mainly for SEO purposes.</em>', 'optimizepress-plus-pack');
        $strings['selected_image']                          = __('Show this as a main image by default', 'optimizepress-plus-pack');
        $strings['product_showcase_thumbnail']              = __('Thumbnail', 'optimizepress-plus-pack');
        $strings['product_showcase_image_size']             = __('Please use the biggest product image you can find.', 'optimizepress-plus-pack');
        $strings['thumbnail_size']                          = __('Thumbnail size', 'optimizepress-plus-pack');
        $strings['product_showcase_thumb_border_color']     = __('Border color of the selected image thumbnail.', 'optimizepress-plus-pack');
        $strings['product_showcase_advanced']               = __('Customize specific styling options for your Product Showcase element');
        $strings['general_options']                         = __('General Options');
        $strings['product_showcase_element_size']           = __('Product Showcase Element Size');
        $strings['product_showcase_element_border_color']   = __('Product Showcase Border Color');

        $strings['image_options']                           = __('Product Image Options');
        $strings['thumbnail_options']                       = __('Thumbnail Options');

        return $strings;
    }

    /**
     * Adds new element to asset list
     * @param  array $assets
     * @return array
     */
    public function addToAssetList($assets)
    {
        $assets['addon'][$this->parentTag] = array(
            'title'         => __('Product Showcase', 'optimizepress-plus-pack'),
            'description'   => __('Showcase your products like industry leaders such as Apple and Amazon with the product showcase element.', 'optimizepress-plus-pack'),
            'settings'      => 'Y',
            'image'         => OPPP_BASE_URL . 'images/elements/' . $this->parentTag . '/' . $this->parentTag . '.png',
            'base_path'     => OPPP_BASE_URL . 'js/elements/',
        );

        return $assets;
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
     * Add element key and name to cacheable elements list.
     * Filter is read in OP Helper Tools plugin and uses it to compile a list of elements whose transient cache can be cleared.
     * @param array $elements
     * @return array
     */
    public function addToCacheableElementsList($elements)
    {
        $elements['el_prsh_'] = __('Product Showcase', 'optimizepress-plus-pack');

        return $elements;
    }

}
new Oppp_Elements_Product_Showcase();
