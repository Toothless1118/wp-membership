<?php

class Oppp_Elements_Slider
{
    /**
     * @var string
     */
    protected $parentTag = 'op_slider';

    /**
     * @var string
     */
    protected $childTag = 'op_slide';

    /**
     * @var string
     */
    protected $elementId;

    /**
     * @var string
     */
    protected $elementStyle;

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
            $styles = array_merge($styles, array(1, 2, 3));
        }

        return $styles;
    }

    /**
     * Parses parent ('op_slider') shortcode
     * @param  array $atts
     * @param  string $content
     * @return string
     */
    public function parentShortcode($atts, $content)
    {
        $this->elementId = 'sl_' . md5(serialize($atts) . $content);

        $atts = shortcode_atts(array(
            'style'                 => 1,
            'animation_type'        => 'slide',
            'animation_loop'        => 'y',
            'slideshow_autostart'   => 'y',
            'slideshow_sizing'      => 'normal',
            'animation_speed'       => 7000,
            'slideshow_speed'       => 700
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

            /*
             * We need to create "bool" strings for FlexSlider JS init
             */
            if ('y' === $atts['animation_loop']) {
                $atts['animation_loop'] = 'true';
            } else {
                $atts['animation_loop'] = 'false';
            }
            if ('y' === $atts['slideshow_autostart']) {
                $atts['slideshow_autostart'] = 'true';
            } else {
                $atts['slideshow_autostart'] = 'false';
            }

            $atts['element_id'] = $this->elementId;

            $data = op_sl_parse('slider', $atts);
            if (false === is_array($data) && empty($data)) {
                return;
            }

            set_transient('el_' . $this->elementId, $data, OP_SL_ELEMENT_CACHE_LIFETIME);
        }

        /*
         * We are loading JS only when the page is renderding and not in Live Editor
         */
        if (!defined('OP_LIVEEDITOR')) {
            /*
             * This should go to class constructor if we could find if slider shortcode was in the page or not
             */

            // Different dependencies when OP_SCRIPT_DEBUG is turned on and off
            if (OP_SCRIPT_DEBUG === '') {
                wp_enqueue_script(OP_SN . '-flexslider', OPPP_BASE_URL . 'js/elements/jquery.flexslider.min.js', array(OP_SN . '-noconflict-js'), OPPP_VERSION, false);
                wp_enqueue_style(OP_SN . '-flexslider', OPPP_BASE_URL . 'css/elements/flexslider' . OP_SCRIPT_DEBUG . '.css', array(), OPPP_VERSION, 'all');
            }

            if (isset($data['javascript'])) {
                $output .= $data['javascript'];
            }

        } else {
            if (OP_SCRIPT_DEBUG === '') {
                wp_enqueue_style(OP_SN . '-addon-slider', OPPP_BASE_URL . 'css/elements/op_slider_admin' . OP_SCRIPT_DEBUG . '.css', array(), OPPP_VERSION, 'all');
            }
        }

        if (OP_SCRIPT_DEBUG === '') {
            wp_enqueue_style(OP_SN . '-flexslider-custom', OPPP_BASE_URL . 'css/elements/flexslider-custom' . OP_SCRIPT_DEBUG . '.css', array(OP_SN . '-flexslider'), OPPP_VERSION, 'all');
        }

        if (isset($data['markup'])) {
            $output .= sprintf($data['markup'], do_shortcode($content));
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
            wp_enqueue_style(OP_SN . '-addon-slider', OPPP_BASE_URL . 'css/elements/op_slider_admin' . OP_SCRIPT_DEBUG . '.css', array(), OPPP_VERSION, 'all');
            wp_enqueue_style(OP_SN . '-flexslider', OPPP_BASE_URL . 'css/elements/flexslider' . OP_SCRIPT_DEBUG . '.css', array(), OPPP_VERSION, 'all');
            wp_enqueue_style(OP_SN . '-flexslider-custom', OPPP_BASE_URL . 'css/elements/flexslider-custom' . OP_SCRIPT_DEBUG . '.css', array(), OPPP_VERSION, 'all');
        }
    }

    /**
     * Parses parent ('op_slider') shortcode
     * @param  array $atts
     * @param  string $content
     * @return string
     */
    public function childShortcode($atts, $content)
    {
        $atts = shortcode_atts(array(
            'href'      => null,
            'image'     => '',
            'title'     => '',
            'bg_color'  => null,
        ), $atts, $this->childTag);

        $output = '';
        $img_size = op_get_image_html_attribute($atts['image']);
        switch ($this->elementStyle) {
            case 1:
            case 3:
                $output .= '
                        <div class="op-slide-description">
                            <h1>' . $atts['title'] . '</h1>
                            ' . $content . '
                        </div>
                        <img class="op-slide-img" src="' . $atts['image'] . '" ' . $img_size . ' />';
                break;
            case 2:
                $output .= '
                        <img class="op-slide-img" src="' . $atts['image'] . '" ' . $img_size . ' />';
                break;
        }

        if (null !== $atts['href'] && !empty($atts['href'])) {
            $output = '<a href="' . esc_url($atts['href']) . '">' . $output . '</a>';
        }

        /*
         * Generating slide style
         */
        $cssStyles = array();
        if (null !== $atts['bg_color'] && !empty($atts['bg_color'])) {
            $cssStyles[] = 'background-color: ' . $atts['bg_color'];
        }

        $slideStyle = '';
        if (count($cssStyles) > 0) {
            $slideStyle = ' style="' . implode('; ', $cssStyles) . '"';
        }

        return '<li' . $slideStyle . '>' . $output . '</li>';
    }

    /**
     * Adds custom translations for JS strings needed by custom element
     * @param  array $strings
     * @return array
     */
    public function addToLangList($strings)
    {

        $strings['slider_advanced']         = __('Customize specific styling options for your slider text', 'optimizepress-plus-pack');
        $strings['title_font_styling']      = __('Title font styling', 'optimizepress-plus-pack');
        $strings['content_font_styling']    = __('Content font styling', 'optimizepress-plus-pack');
        $strings['animation_type']          = __('Animation type', 'optimizepress-plus-pack');
        $strings['animation_loop']          = __('Loop animation', 'optimizepress-plus-pack');
        $strings['slideshow_autostart']     = __('Slideshow autostart', 'optimizepress-plus-pack');
        $strings['slideshow_sizing']        = __('Slideshow sizing', 'optimizepress-plus-pack');
        $strings['slideshow_speed']         = __('Speed of slideshow cycling (in milliseconds)', 'optimizepress-plus-pack');
        $strings['animation_speed']         = __('Speed of animation (in milliseconds)', 'optimizepress-plus-pack');
        $strings['slider_image_size']       = __('Image <br /> (500px X 300px recommended)', 'optimizepress-plus-pack');

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
            'title'         => __('Slider', 'optimizepress-plus-pack'),
            'description'   => __('Effectively showcase your content with this engaging slider element', 'optimizepress-plus-pack'),
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
        $elements['el_sl_'] = __('Slider', 'optimizepress-plus-pack');

        return $elements;
    }
}

new Oppp_Elements_Slider();