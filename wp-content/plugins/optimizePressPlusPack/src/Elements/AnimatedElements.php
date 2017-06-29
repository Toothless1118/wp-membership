<?php

class OptimizePress_Elements_AnimatedElements
{
    /**
     * Defined animations.
     * @var array
     */
    protected $animations = array();

    /**
     * Holder of all inline CSS per selector.
     * @var array
     */
    protected $inlineCss = array();

    /**
     * Available CSS effects.
     * @var array
     */
    protected $effects = array(
        'bounce',
        'flash',
        'rubberBand',
        'shake',
        'swing',
        'tada',
        'bounceIn',
        'bounceInDown',
        'bounceInLeft',
        'bounceInRight',
        'bounceInUp',
        'fadeIn',
        'fadeInDown',
        'fadeInDownBig',
        'fadeInLeft',
        'fadeInLeftBig',
        'fadeInRight',
        'fadeInRightBig',
        'fadeInUp',
        'fadeInUpBig',
        'slideInDown',
        'slideInLeft',
        'slideInRight',
        'slideInUp',
        'slideOutDown',
        'slideOutLeft',
        'slideOutRight',
        'slideOutUp'
    );

    /**
     * Init hooks and filters.
     */
    public function __construct()
    {
        add_action('wp_print_styles', array($this, 'registerAssets'), 15);
        add_action('op_advanced_element_options_after', array($this, 'formOptions'));

        add_filter('op_element_advanced_options', array($this, 'parseAdvancedOptions'));
        add_filter('op_cacheable_elements', array($this, 'addToCacheableElementsList'));
    }

    /**
     * Register and enqueue assets.
     * @return void
     */
    public function registerAssets()
    {
        // Animations shouldn't be shown in LE cause it may be difficult
        // to access their action buttons due to their animation
        if (defined('OP_LIVEEDITOR')) {

            // But we want the admin styles for the element itself, if debug version is not enabled
            if (OP_SCRIPT_DEBUG === '') {
                wp_enqueue_style('op-animated-elements-admin', OPPP_BASE_URL . 'css/elements/op-animated-elements-admin.css', null, '1.0.0', 'all');
            }

            return;
        }

        if ($this->animations) {
            if (OP_SCRIPT_DEBUG === '') {
                // Enqueueing all scripts
                wp_enqueue_script('jquery-waypoints', OPPP_BASE_URL . 'js/components/jquery.waypoints.min.js', array('jquery'), '4.0.0', true);
                wp_enqueue_script('op-animated-elements', OPPP_BASE_URL . 'js/elements/op-animated-elements.js', array('jquery-waypoints'), '1.0.0', true);

                // Attaching animations data
                wp_localize_script('op-animated-elements', 'OPAnimations', array('elements' => $this->animations));

                // Enqueueing all styles
                wp_enqueue_style('animate.css', OPPP_BASE_URL . 'css/components/animate.min.css', array(), '3.5.1', 'all');
                wp_enqueue_style('op-animated-elements', OPPP_BASE_URL . 'css/elements/op-animated-elements.css', array('animate.css'), '1.0.0', 'all');

                // Attaching inline CSS
                wp_add_inline_style('op-animated-elements', $this->generateInlineCss());
            } else {
                // Attaching animations data to frontend merged JS
                wp_localize_script(OP_SN . 'plus-pack-js-front-all', 'OPAnimations', array('elements' => $this->animations));

                // Attaching inline CSS to fronted merged CSS
                wp_add_inline_style(OP_SN . 'plus-pack-css-front-all', $this->generateInlineCss());
            }
        }
    }

    /**
     * Add additional form fields to advanced elements popup screen.
     * @return void
     */
    public function formOptions()
    {
        /*
         * Cache busting
         */
        if (function_exists('wp_using_ext_object_cache')) {
            $extCache = wp_using_ext_object_cache();
            if (true === $extCache) {
                wp_using_ext_object_cache(false);
            }
        }

        $cacheKey = 'animated_elements_form';

        if (false === $fields = get_transient($cacheKey)) {
            $fields = op_sl_parse('animated_elements', array(
                'effects'   => $this->effects,
                'labels'    => array(
                    // 'animate_when_scrolled_into_view' => __('Animate element when scrolled into view?', 'optimizepress-plus-pack'),
                    'choose_effect' => __('Choose effect', 'optimizepress-plus-pack'),
                    'select_effect' => __('None', 'optimizepress-plus-pack'),
                    'animated_element_title' => __('Animate Element', 'optimizepress-plus-pack'),
                    'animated_element_description' => __('Options for animating this element when scrolled into view.', 'optimizepress-plus-pack'),
                    'animation_trigger_direction' => __('Animation trigger direction', 'optimizepress-plus-pack'),
                    'down' => __('Down', 'optimizepress-plus-pack'),
                    'up' => __('Up', 'optimizepress-plus-pack'),
                    'animation_delay_timer' => __('Animation delay timer', 'optimizepress-plus-pack'),
                    'enter_delay_in_seconds' => esc_attr__('Enter delay in seconds', 'optimizepress-plus-pack'),
                    'infinite_animation' => __('Infinite animation?', 'optimizepress-plus-pack'),
                ),
            ));

            if (empty($fields)) {
                return;
            }

            set_transient($cacheKey, $fields, OP_SL_ELEMENT_CACHE_LIFETIME);
        }

        /*
         * Cache busting
         */
        if (function_exists('wp_using_ext_object_cache')) {
            wp_using_ext_object_cache($extCache);
        }

        echo $fields;
    }

    /**
     * Parse advanced options for given element and init animations if needed.
     * @param  array $element
     * @return array
     */
    public function parseAdvancedOptions($element)
    {
        // Check if element has additional data
        if ( ! empty($element['element_data_style'])) {
            // Decode data
            $dataStyle = json_decode(base64_decode($element['element_data_style']));

            // Check if extras are defined
            if ( ! isset($dataStyle->extras) || empty($dataStyle->extras)) {
                return $element;
            }

            // Check if animation is turned on
            if ( ! isset($dataStyle->extras->animationEffect)
            || empty($dataStyle->extras->animationEffect)
            || $dataStyle->extras->animationEffect == '') {
                return $element;
            }

            // Generate unique element selector class
            $selector = uniqid('anim-el-');

            // Add element to animation list
            $this->animations[] = array(
                'effect' => $dataStyle->extras->animationEffect,
                'selector' => $selector,
                'direction' => isset($dataStyle->extras->animationDirection) ? $dataStyle->extras->animationDirection : 'down',
            );

            // Clear classes left from saving of element markup classes
            $element['element_class'] = str_replace(array('animated') + $this->effects, '', $element['element_class']);

            // Append selector class to element
            $element['element_class'] .= ' to-be-animated ' . $selector;

            // Add infinite class name
            if (isset($dataStyle->extras->animationInfinite) && (int) $dataStyle->extras->animationInfinite === 1) {
                $element['element_class'] .= ' infinite';
            }

            // Introdouce animation delay
            if (isset($dataStyle->extras->animationDelay)) {
                $this->addAnimationDelay($selector, (int) $dataStyle->extras->animationDelay);
            }

        }

        return $element;
    }

    /**
     * Generate inline CSS string.
     * @return string
     */
    protected function generateInlineCss()
    {
        $css = '';

        if ( ! count($this->inlineCss)) {
            return $css;
        }

        foreach ($this->inlineCss as $selector => $rules) {
            if ( ! count($rules)) {
                continue;
            }

            $css .= ' .' . $selector . ' {';
            foreach ($rules as $rule) {
                $css .= $rule;
            }
            $css .= '}';
        }

        return $css;
    }

    /**
     * Add inline CSS rule for animation delay for given selector.
     * @return void
     */
    protected function addAnimationDelay($selector, $delay)
    {
        $this->inlineCss[$selector][] = '-webkit-animation-delay: ' . $delay . 's;';
        $this->inlineCss[$selector][] = '-moz-animation-delay: ' . $delay . 's;';
        $this->inlineCss[$selector][] = 'animation-delay: ' . $delay . 's;';
    }

    /**
     * Add element key and name to cacheable elements list.
     * Filter is read in OP Helper Tools plugin and uses it to compile a list of elements whose transient cache can be cleared.
     * @param array $elements
     * @return array
     */
    public function addToCacheableElementsList($elements)
    {
        $elements['animated_elements'] = __('Animated Elements', 'optimizepress-plus-pack');

        return $elements;
    }
}

new OptimizePress_Elements_AnimatedElements;