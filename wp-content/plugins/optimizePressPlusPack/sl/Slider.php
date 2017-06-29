<?php

namespace OpSl\Element;

use OpSl\Util\Element as Util;

class Slider implements ElementInterface
{
    protected $atts;

    public function __construct($atts)
    {
        $this->atts = $atts['data'];
    }

    public function render()
    {
        /*
         * Flexslider initialization
         */
        $javascript = '
        <script>
        (function ($) {
            $(window).load(function() {
                $(".op_slider_' . $this->atts['element_id'] . '").flexslider({
                    smoothHeight:   true,
                    pauseOnHover:   true,
                    animation:      "' . $this->atts['animation_type'] . '",
                    animationLoop:  ' . $this->atts['animation_loop'] . ',
                    slideshow:      ' . $this->atts['slideshow_autostart'] . ',
                    animationSpeed: ' . $this->atts['animation_speed'] . ',
                    slideshowSpeed: ' . $this->atts['slideshow_speed'] . '
                });
            });
        }(opjq));
        </script>';

        $markup = '
        <div class="flexslider op_slider op-slider-style-' . $this->atts['style'] . ' op-slider-sizing-' . $this->atts['slideshow_sizing'] . ' op_slider_' . $this->atts['element_id'] . '">
            <ul class="slides">
                %s
            </ul>
        </div>';

        return array(
            'javascript' => $javascript,
            'markup' => $markup
        );
    }
}