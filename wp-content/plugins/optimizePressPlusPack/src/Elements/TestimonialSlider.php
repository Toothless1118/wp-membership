<?php

class Oppp_Elements_Testimonial_Slider
{
    /**
     * @var OptimizePress_Testimonial_Slider
     */
    protected static $instance;

    /**
     * @var string
     */
    protected $parentTag = 'op_testimonial_slider';

    /**
     * @var string
     */
    protected $childTag = 'op_testimonial_slide';

    /**
     * @var string
     */
    protected $elementId;

    /**
     * @var string
     */
    protected $elementStyle;

    /**
     * @var integer
     */
    protected $currentSlide = 1;

    /**
     * @var array
     */
    protected $font = array();

    /**
     * @var array
     */
    protected $testimonialTitleFont = array();

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
            $styles = array_merge($styles, array(1, 2, 3, 4, 5, 6));
        }

        return $styles;
    }

    /**
     * Parses parent ('op_testimonial_slider') shortcode
     * @param  array $atts
     * @param  string $content
     * @return string
     */
    public function parentShortcode($atts, $content)
    {
        $this->elementId = 'sl_' . md5(serialize($atts) . $content);

        $atts = shortcode_atts(array(
            'style'                          => 1,
            'animation_type'                 => 'slide',
            'animation_loop'                 => 'y',
            'slideshow_autostart'            => 'y',
            'animation_speed'                => 7000,
            'slideshow_speed'                => 700,
            'title'                          => '',
            'title_color'                    => '',
            'subtitle'                       => '',
            'background_color'               => '',
            'columns'                        => '1',
            'font_font'                      => '',
            'font_size'                      => '',
            'font_style'                     => '',
            'font_spacing'                   => '',
            'font_color'                     => '',
            'font_shadow'                    => '',
            'testimonial_title_font_font'    => '',
            'testimonial_title_font_size'    => '',
            'testimonial_title_font_style'   => '',
            'testimonial_title_font_spacing' => '',
            'testimonial_title_font_color'   => '',
            'testimonial_title_font_shadow'  => '',
        ), $atts, $this->parentTag);

        $this->elementStyle = $atts['style'];


        foreach ($atts as $key => $value){
            if(0 === strpos($key, 'font')){
                $this->font[$key] = $value;
            }
            if(0 === strpos($key, 'testimonial_title_fon')){
                $this->testimonialTitleFont[$key] = $value;
            }
        }

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

            $data = op_sl_parse('testimonial_slider', $atts);
            if (false === is_array($data) && empty($data)) {
                return;
            }

            set_transient('el_' . $this->elementId, $data, OP_SL_ELEMENT_CACHE_LIFETIME);
        }

        // Dependency is different for production and dev environments
        if (wp_script_is(OP_SN . '-noconflict-js', 'enqueued')) {
            $js_dependency = OP_SN . '-noconflict-js';
        } else {
            $js_dependency = OP_SN . '-op-jquery-base-all';
        }

        /*
         * We are loading JS only when the page is renderding and not in Live Editor
         */
        if (!defined('OP_LIVEEDITOR')) {

            if (OP_SCRIPT_DEBUG === '') {
                wp_enqueue_script(OP_SN . '-flexslider', OPPP_BASE_URL . 'js/elements/jquery.flexslider.min.js', array($js_dependency), OPPP_VERSION, false);
                wp_enqueue_style(OP_SN . '-flexslider', OPPP_BASE_URL . 'css/elements/flexslider' . OP_SCRIPT_DEBUG . '.css', array(), OPPP_VERSION, 'all');
            }

            if (isset($data['javascript'])) {
                $output .= $data['javascript'];
            }

        } else {
            if (OP_SCRIPT_DEBUG === '') {
                wp_enqueue_style(OP_SN . '-addon-testimonial-slider-admin', OPPP_BASE_URL . 'css/elements/op_testimonial_slider_admin' . OP_SCRIPT_DEBUG . '.css', array(), OPPP_VERSION, 'all');
            }
        }

        // Different dependencies when OP_SCRIPT_DEBUG is turned on and off (all styles are concatenated into opplus-front-all.css & opplus-back-all for production)
        if (OP_SCRIPT_DEBUG === '') {
            wp_enqueue_style(OP_SN.'-flexslider-custom', OPPP_BASE_URL . 'css/elements/flexslider-custom' . OP_SCRIPT_DEBUG . '.css', array(OP_SN.'-flexslider'), OPPP_VERSION, 'all');
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
        if (defined('OP_LIVEEDITOR')) {
            wp_enqueue_style(OP_SN . '-flexslider', OPPP_BASE_URL . 'css/elements/flexslider' . OP_SCRIPT_DEBUG . '.css', array(), OPPP_VERSION, 'all');
            wp_enqueue_style(OP_SN . '-flexslider-custom-testimonials', OPPP_BASE_URL . 'css/elements/flexslider-custom' . OP_SCRIPT_DEBUG . '.css', array(), OPPP_VERSION, 'all');
            wp_enqueue_style(OP_SN . '-addon-testimonial-slider-admin', OPPP_BASE_URL . 'css/elements/op_testimonial_slider_admin' . OP_SCRIPT_DEBUG . '.css', array(), OPPP_VERSION, 'all');
        }
    }

    /**
     * Parses parent ('op_testimonial_slider') shortcode
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
            'company'   => '',
            'button_color'  => '',
            'button_text' => '',
            'header_color'  => '',
            'background_color'  => '',
            'font_font' => ''
        ), $atts, $this->childTag);

        $output = '';

        $slideStyle = '';
        $read_more = '';
        $image = !empty($atts['image']) ? $atts['image'] : OPPP_BASE_URL . 'css/elements/images/testimonial-slider-user.png';
        $name = !empty($atts['title']) ? $atts['title'] : '';
        $company = !empty($atts['company']) ? $atts['company'] : '';

        if (!empty($atts['href']) && $atts['href'] !== '#') {
            $read_more = '<a href="' . $atts['href'] . '" class="op-btn-cta">' . $atts['button_text'] . '</a>';
        }

        switch ($this->elementStyle) {
            case 1:
                $output .= '
                    <div class="'. $atts['font_font'] .'"></div>
                    <div class="op-testimonial-slider-photo-wrap">
                        <img src="' . $image . '" class="op-testimonial-slider-photo" width="110" height="110" title="" alt="' . $name . '">
                        <span class="op-testimonial-slider-name"><strong>' . $name . '</strong> ' . $company . '</span>
                    </div>
                    <span class="op-testimonial-slider-right">
                        <blockquote>' . $content . '</blockquote>' .
                        $read_more . '
                    </span>';
                break;

            case 2:
                $output .= '
                    <span class="op-star-wrap" style="background-color: ' . $atts['header_color'] . '">
                        <span class="op-star-icon"> </span>
                        <span class="op-star-icon"> </span>
                        <span class="op-star-icon"> </span>
                        <span class="op-star-icon"> </span>
                        <span class="op-star-icon"> </span>
                    </span>
                    <span class="op-testimonial-slide-text">
                        <div class="op-testimonial-slide-text-cell">
                            <blockquote>' . $content . '</blockquote>
                            <span class="op-testimonial-slide-text-name"><strong>' . $name . '</strong> ' . $company . '</span>
                        </div>
                   </span>';
                break;

            case 3:
                $output .= '
                    <img src="' . $image . '" class="op-testimonial-slide-photo" width="91" height="91" title="" alt="' . $name . '" />
                    <span class="ts-right">
                        <blockquote>' . $content . '</blockquote>
                        <span class="op-testimonial-slide-name"><strong>' . $name . '</strong> ' . $company . ' </span>
                    </span>';
                break;

            case 4:
                $img = $image ? '<img src="' . $image . '" class="op-testimonial-slide-photo" width="110" height="110" title="" alt="' . $name . '">' : '';
                $output .= '
                    <blockquote>' . $content . '</blockquote>
                    <div class="op-testimonial-slide-photo-wrap">
                        ' . $img . '
                        <span class="op-testimonial-slide-name"><strong>' . $name . '</strong> ' . $company . '</span>
                    </div>';
                break;

            case 5:
                $img = $image ? '<div class="op-testimonial-slide-photo-wrap"><img src="' . $image . '" class="op-testimonial-slide-photo" width="100" height="100" title="" alt="' . $name . '"></div>' : '';
                $output .= $img . '
                    <blockquote><strong>' . $name . '</strong> - <span>' . $content . '</span></blockquote>';
                break;

            case 6:
                $img = $image ? '<div class="op-testimonial-slide-photo-wrap">
                                    <span class="curret">&nbsp;</span>
                                    <img src="' . $image . '" class="op-testimonial-slide-photo" width="110" height="110" title="" alt="' . $name . '">
                                </div>' : '';
                $output .= '<blockquote><div class="quote">' . $content . '</div><span class="op-testimonial-slide-name"><strong>' . $name . '</strong>' . $company . '</span>' . $img . '</blockquote>';
                break;
        }

        /**
         * Font settings and buton color CSS
         */
        if (!empty($atts['button_color'])) {
            $output .= '
                        <style>
                            .op-testimonial-slide-' . $this->elementId . '-' . $this->currentSlide . ' .op-btn-cta { color:' . $atts['button_color'] . '; }
                            .op-testimonial-slide-' . $this->elementId . '-' . $this->currentSlide . ' .op-btn-cta:hover {
                                background-color:' . $atts['button_color'] . ';
                                border-color:' . $atts['button_color'] . ';
                                color: #fff;
                            }
                            .op-testimonial-slide-' . $this->elementId . '-' . $this->currentSlide . ' .op-testimonial-slider-photo-wrap::before,
                            .op-testimonial-slide-' . $this->elementId . '-' . $this->currentSlide . ' .op-testimonial-slider-photo-wrap::after {
                                box-shadow: 0 0 1px ' . $atts['button_color'] . ';
                            }
                        </style>';
        }
        if (!empty($this->font['font_font'])){
            $output .= '
                        <style>
                            .op_testimonial_slider_' . $this->elementId . ' blockquote,
                            .op_testimonial_slider_' . $this->elementId . ' .op-testimonial-slide-name,
                            .op_testimonial_slider_' . $this->elementId . ' .op-testimonial-slider-name,
                            .op_testimonial_slider_' . $this->elementId . ' .op-testimonial-slide-text-name {
                               font-family: '. $this->font['font_font'] .';
                            }
                        </style>';
        }
        if (!empty($this->font['font_size'])){
            $output .= '
                        <style>
                            .op_testimonial_slider_' . $this->elementId . ' blockquote{
                               font-size: '. $this->font['font_size'] .'px !important;
                            }
                        </style>';
        }
        if (!empty($this->font['font_style'])){
            $output .= '
                        <style>
                            .op_testimonial_slider_' . $this->elementId . ' blockquote {
                               font-weight: '. $this->font['font_style'] .';
                            }
                        </style>';
        }
        if (!empty($this->font['font_color'])){
            $output .= '
                        <style>
                            .op_testimonial_slider_' . $this->elementId . ' blockquote {
                               color: '. $this->font['font_color'] .';
                            }
                        </style>';
        }
        if (!empty($this->font['font_spacing'])){
            $output .= '
                        <style>
                            .op_testimonial_slider_' . $this->elementId . ' blockquote {
                               letter-spacing: '. $this->font['font_spacing'] .'px;
                            }
                        </style>';
        }
        if (!empty($this->font['font_shadow'])){
            $output .= '
                        <style>
                            .op_testimonial_slider_' . $this->elementId . ' blockquote {
                                text-shadow: 1px 1px '. ($this->font['font_shadow'] === 'dark' ? '#000' : '#fff') .';
                            }
                        </style>';
        }
        if (!empty($this->testimonialTitleFont['testimonial_title_font_font'])){
            $output .= '
                        <style>
                            .op_testimonial_slider_' . $this->elementId . ' h2,
                            .op_testimonial_slider_' . $this->elementId . ' h3 {
                               font-family: "'. $this->testimonialTitleFont['testimonial_title_font_font'] .'" !important;
                            }
                        </style>';
        }
        if (!empty($this->testimonialTitleFont['testimonial_title_font_size'])){
            $output .= '
                        <style>
                            .op_testimonial_slider_' . $this->elementId . ' h2,
                            .op_testimonial_slider_' . $this->elementId . ' h3 {
                               font-size: '. $this->testimonialTitleFont['testimonial_title_font_size'] .'px !important;
                            }
                        </style>';
        }
        if (!empty($this->testimonialTitleFont['testimonial_title_font_style'])){
            $output .= '
                        <style>
                            .op_testimonial_slider_' . $this->elementId . ' h2,
                            .op_testimonial_slider_' . $this->elementId . ' h3 {
                               font-style: '. $this->testimonialTitleFont['testimonial_title_font_style'] .' !important;
                            }
                        </style>';
        }
        if (!empty($this->testimonialTitleFont['testimonial_title_font_color'])){
            $output .= '
                        <style>
                            .op_testimonial_slider_' . $this->elementId . ' h2,
                            .op_testimonial_slider_' . $this->elementId . ' h3 {
                               color: '. $this->testimonialTitleFont['testimonial_title_font_color'] .' !important;
                            }
                        </style>';
        }
        if (!empty($this->testimonialTitleFont['testimonial_title_font_spacing'])){
            $output .= '
                        <style>
                            .op_testimonial_slider_' . $this->elementId . ' h2,
                            .op_testimonial_slider_' . $this->elementId . ' h3 {
                               letter-spacing: '. $this->testimonialTitleFont['testimonial_title_font_spacing'] .'px;
                            }
                        </style>';
        }
        if (!empty($this->testimonialTitleFont['testimonial_title_font_shadow'])){
            $output .= '
                        <style>
                            .op_testimonial_slider_' . $this->elementId . ' h2,
                            .op_testimonial_slider_' . $this->elementId . ' h3 {
                               text-shadow: 2px 2px '. ($this->testimonialTitleFont['testimonial_title_font_shadow'] === 'dark' ? '#000' : '#fff') .';
                            }
                        </style>';
        }

        $returnString = '<li class="op-testimonial-slide-' . $this->elementId . '-' . $this->currentSlide . '"' . $slideStyle . '>' . $output . '</li>';

        $this->currentSlide += 1;

        return $returnString;
    }

    /**
     * Adds custom translations for JS strings needed by custom element
     * @param  array $strings
     * @return array
     */
    public function addToLangList($strings)
    {
        //See what our partners have to say:
        $strings['testimonial_slider_title']            = __('Testimonials title', 'optimizepress-plus-pack');
        $strings['testimonial_slider_title_color']      = __('Testimonials title color', 'optimizepress-plus-pack');
        $strings['testimonial_slider_subtitle']         = __('Testimonials subtitle', 'optimizepress-plus-pack');
        $strings['slider_background_color']             = __('Slider background color', 'optimizepress-plus-pack');
        $strings['testimonial_slider_columns']          = __('Number of columns', 'optimizepress-plus-pack');
        $strings['button_color']                        = __('Button color', 'optimizepress-plus-pack');
        $strings['header_color']                        = __('Header color', 'optimizepress-plus-pack');
        $strings['slider_advanced']                     = __('Customize specific styling options for your slider text', 'optimizepress-plus-pack');
        $strings['title_font_styling']                  = __('Title font styling', 'optimizepress-plus-pack');
        $strings['content_font_styling']                = __('Content font styling', 'optimizepress-plus-pack');
        $strings['animation_type']                      = __('Animation type', 'optimizepress-plus-pack');
        $strings['animation_loop']                      = __('Loop animation', 'optimizepress-plus-pack');
        $strings['slideshow_autostart']                 = __('Slideshow autostart', 'optimizepress-plus-pack');
        $strings['slideshow_speed']                     = __('Speed of slideshow cycling (in milliseconds)', 'optimizepress-plus-pack');
        $strings['animation_speed']                     = __('Speed of animation (in milliseconds)', 'optimizepress-plus-pack');
        $strings['testimonial_slider_font_settings']    = __('Testimonial Slider Styling', 'optimizepress-plus-pack');
        $strings['testimonial_slider_title_font_settings']    = __('Testimonial Slider Title Styling', 'optimizepress-plus-pack');

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
            'title'         => __('Testimonial Slider', 'optimizepress-plus-pack'),
            'description'   => __('Share testimonials in a compact and engaging way using this slider. Testimonials are a great way to provide social proof for your product or service.', 'optimizepress-plus-pack'),
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
        $elements['el_tesl_'] = __('Testimonial Slider', 'optimizepress-plus-pack');

        return $elements;
    }
}

new Oppp_Elements_Testimonial_Slider();
