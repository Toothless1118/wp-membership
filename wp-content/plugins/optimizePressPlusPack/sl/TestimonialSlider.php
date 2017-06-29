<?php

namespace OpSl\Element;

use OpSl\Util\Element as Util;

class TestimonialSlider implements ElementInterface
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
        $element_id = $this->atts['element_id'];

        $javascript = '';
        // $javascript .= print_r($this->atts, true);

        // smootheight is not working properly with
        // columns, so we force it off when
        // there's more columns
        $smoothHeight = 'true';

        switch($this->atts['style']) {
            case 1:
                $animation_type = $this->atts['animation_type'] == 'default' ? 'fade' : $this->atts['animation_type'];
                $javascript .= '
                <script>
                (function ($) {
                    $(window).load(function() {
                        $(".op_testimonial_slider_' . $this->atts['element_id'] . '").flexslider({
                            selector: ".op-testimonial-slides > li",
                            pauseOnHover: true,
                            touch: true,
                            controlNav: false,
                            smoothHeight: true,
                            animation:      "' . $animation_type . '",
                            animationLoop:  ' . $this->atts['animation_loop'] . ',
                            slideshow:      ' . $this->atts['slideshow_autostart'] . ',
                            animationSpeed: ' . $this->atts['animation_speed'] . ',
                            slideshowSpeed: ' . $this->atts['slideshow_speed'] . '
                        });
                    });
                }(opjq));
                </script>';
                $markup = '
                <div class="flexslider op-testimonial-slider op-testimonial-slider-style-' . $this->atts['style'] . '">
                    <h2 class="op-testimonial-slider-title">' . $this->atts['title'] . '</h2>
                    <div class="op_testimonial_slider_' . $this->atts['element_id'] . '">
                        <ul class="op-testimonial-slides">
                            %s
                        </ul>
                    </div>
                </div>';
                break;

            case 2:
                $animation_type = $this->atts['animation_type'] == 'default' ? 'slide' : $this->atts['animation_type'];
                $javascript .= '
                <script>
                (function ($) {
                    $(window).load(function() {
                        $(".op_testimonial_slider_' . $this->atts['element_id'] . '").flexslider({
                            selector: ".op-testimonial-slides > li",
                            pauseOnHover: true,
                            touch: true,
                            controlNav: true,
                            smoothHeight: true,
                            animation:      "' . $animation_type . '",
                            animationLoop:  ' . $this->atts['animation_loop'] . ',
                            slideshow:      ' . $this->atts['slideshow_autostart'] . ',
                            animationSpeed: ' . $this->atts['animation_speed'] . ',
                            slideshowSpeed: ' . $this->atts['slideshow_speed'] . '
                        });
                    });
                }(opjq));
                </script>';
                $markup = '
                <div class="flexslider op-testimonial-slider op-testimonial-slider-style-' . $this->atts['style'] . '">
                    <div class="op_testimonial_slider_' . $this->atts['element_id'] . '">
                        <ul class="op-testimonial-slides">
                            %s
                        </ul>
                    </div>
                </div>';
                break;

            case 3:
                $animation_type = $this->atts['animation_type'] == 'default' ? 'slide' : $this->atts['animation_type'];
                $javascript .= '
                <script>
                (function ($) {
                    $(window).load(function() {
                        $(".op_testimonial_slider_' . $this->atts['element_id'] . '").flexslider({
                            selector: ".op-testimonial-slides > li",
                            pauseOnAction: true,
                            pauseOnHover: true,
                            touch: true,
                            controlNav: true,
                            directionNav: false,
                            smoothHeight: true,
                            animation:      "' . $animation_type . '",
                            animationLoop:  ' . $this->atts['animation_loop'] . ',
                            slideshow:      ' . $this->atts['slideshow_autostart'] . ',
                            animationSpeed: ' . $this->atts['animation_speed'] . ',
                            slideshowSpeed: ' . $this->atts['slideshow_speed'] . '
                        });
                    });
                }(opjq));
                </script>';
                $additional_style = !empty($this->atts['background_color']) ? ' style="background-color:' . $this->atts['background_color'] . ';"' : '';
                $markup = '
                <div class="flexslider op-testimonial-slider op-testimonial-slider-style-' . $this->atts['style'] . '"' . $additional_style . '>
                    <div class="op_testimonial_slider_' . $this->atts['element_id'] . '">
                        <ul class="op-testimonial-slides">
                            %s
                        </ul>
                    </div>
                </div>';
                break;

            case 4:
                $animation_type = $this->atts['animation_type'] == 'default' ? 'fade' : $this->atts['animation_type'];
                $columns = $this->atts['columns'];
                switch ($columns) {
                    case 1:
                        $itemWidth = '890';
                        $maxItems = '1';
                        $max1 = 1;
                        $max2 = 1;
                        $max3 = 1;
                        break;
                    case 2:
                        $itemWidth = '450';
                        $maxItems = '2';
                        $max1 = 2;
                        $max2 = 2;
                        $max3 = 1;

                        // There's a bug in flexslider that causes columns
                        // not to work when animation is set to fade,
                        // so we're forcing slide animation when
                        // there's more columns.
                        // This should be fixed in flexslider 2.7.0,
                        // and removed from here at that time
                        $animation_type = 'slide';

                        // Forcing smooth height to off,
                        // because it isn't working
                        // properly with columns
                        $smoothHeight = 'false';
                        break;
                    case 3:
                        $itemWidth = '260';
                        $maxItems = '3';
                        $max1 = 3;
                        $max2 = 2;
                        $max3 = 1;

                        // There's a bug in flexslider that causes columns
                        // not to work when animation is set to fade,
                        // so we're forcing slide animation when
                        // there's more columns.
                        // This should be fixed in flexslider 2.7.0,
                        // and removed from here at that time
                        $animation_type = 'slide';

                        // Forcing smooth height to off,
                        // because it isn't working
                        // properly with columns
                        $smoothHeight = 'false';
                        break;
                }
                $javascript .= '
                <script>
                (function ($) {
                    $(window).load(function() {
                        var current_flexslider;

                        function getGridSize() {
                            if (window.innerWidth < 768) {
                                return ' . $max3 . ';
                            } else if (window.innerWidth < 960) {
                                return ' . $max2 . ';
                            } else {
                                return ' . $max1 . ';
                            }
                            return result;
                        }

                        $(window).on("resize", function() {
                            var $currentRow = $(current_flexslider).parentsUntil(".row").parent();
                            var gridSize = getGridSize();

                            if ($currentRow.hasClass("narrow") || $currentRow.hasClass("two-columns")) {
                                gridSize = ' . $max3 . '
                            }

                            if (current_flexslider && current_flexslider.vars) {
                                current_flexslider.vars.minItems = gridSize;
                                current_flexslider.vars.maxItems = gridSize;
                            }
                        });

                        $(".op_testimonial_slider_' . $this->atts['element_id'] . '").flexslider({
                            selector: ".op-testimonial-slides > li",
                            pauseOnHover: true,
                            pauseOnAction: true,
                            touch: true,
                            controlNav: true,
                            directionNav: false,
                            itemMargin: 0,
                            itemWidth: ' . $itemWidth . ',
                            minItems: getGridSize(),
                            maxItems: getGridSize(),
                            move: 0,
                            smoothHeight: ' . $smoothHeight . ',
                            animation: "' . $animation_type . '",
                            animationLoop:  ' . $this->atts['animation_loop'] . ',
                            slideshow:      ' . $this->atts['slideshow_autostart'] . ',
                            animationSpeed: ' . $this->atts['animation_speed'] . ',
                            slideshowSpeed: ' . $this->atts['slideshow_speed'] . ',
                            start: function(slider){
                                current_flexslider = slider;

                                // We actually need to trigger resize two times, because otherwise the slider
                                // is not properly sized when placed into 2 or 3 columns layout
                                $(window).trigger("resize");
                                setTimeout(function () {
                                    $(window).trigger("resize");
                                }, 100);
                            }
                        });

                    });
                }(opjq));
                </script>';
                $title_color_style = $this->atts['title_color'] !== '' ? ' style="color:' . $this->atts['title_color'] . ';"' : '';
                $markup = '
                <div class="flexslider op-testimonial-slider op-testimonial-slider-style-' . $this->atts['style'] . '">
                    <h2 class="op-testimonial-slider-title"' . $title_color_style . '>' . $this->atts['title'] . '</h2>
                    <h3 class="op-testimonial-slider-subtitle">' . $this->atts['subtitle'] . '</h3>
                    <div class="op_testimonial_slider_' . $this->atts['element_id'] . '">
                        <ul class="op-testimonial-slides op-testimonial-slides-' . $columns . '-columns">
                            %s
                        </ul>
                    </div>
                </div>';
                break;

                case 5:
                $animation_type = $this->atts['animation_type'] == 'default' ? 'slide' : $this->atts['animation_type'];
                $columns = $this->atts['columns'];
                switch ($columns) {
                    case 1:
                        $itemWidth = '920';
                        $maxItems = '1';
                        $max1 = 1;
                        $max2 = 1;
                        $max3 = 1;
                        break;
                    case 2:
                        $itemWidth = '450';
                        $maxItems = '2';
                        $max1 = 2;
                        $max2 = 2;
                        $max3 = 1;

                        // There's a bug in flexslider that causes columns
                        // not to work when animation is set to fade,
                        // so we're forcing slide animation when
                        // there's more columns.
                        // This should be fixed in flexslider 2.7.0,
                        // and removed from here at that time
                        $animation_type = 'slide';

                        // Forcing smooth height to off,
                        // because it isn't working
                        // properly with columns
                        $smoothHeight = 'false';
                        break;
                    case 3:
                        $itemWidth = '295';
                        $maxItems = '3';
                        $max1 = 3;
                        $max2 = 2;
                        $max3 = 1;

                        // There's a bug in flexslider that causes columns
                        // not to work when animation is set to fade,
                        // so we're forcing slide animation when
                        // there's more columns.
                        // This should be fixed in flexslider 2.7.0,
                        // and removed from here at that time
                        $animation_type = 'slide';

                        // Forcing smooth height to off,
                        // because it isn't working
                        // properly with columns
                        $smoothHeight = 'false';
                        break;
                }
                $javascript .= '
                <script>
                (function ($) {
                    $(window).load(function() {
                        var current_flexslider;

                        function getGridSize() {
                            if (window.innerWidth < 768) {
                                return ' . $max3 . ';
                            } else if (window.innerWidth < 960) {
                                return ' . $max2 . ';
                            } else {
                                return ' . $max1 . ';
                            }
                            return result;
                        }

                        $(window).on("resize", function() {
                            var $currentRow = $(current_flexslider).parentsUntil(".row").parent();
                            var gridSize = getGridSize();

                            if ($currentRow.hasClass("narrow") || $currentRow.hasClass("two-columns")) {
                                gridSize = ' . $max3 . '
                            }

                            if (current_flexslider && current_flexslider.vars) {
                                current_flexslider.vars.minItems = gridSize;
                                current_flexslider.vars.maxItems = gridSize;
                            }
                        });

                        $(".op_testimonial_slider_' . $this->atts['element_id'] . '").flexslider({
                            selector: ".op-testimonial-slides > li",
                            pauseOnHover: true,
                            pauseOnAction: true,
                            touch: true,
                            controlNav: false,
                            directionNav: true,
                            itemMargin: 0,
                            itemWidth: ' . $itemWidth . ',
                            minItems: getGridSize(),
                            maxItems: getGridSize(),
                            move: 0,
                            smoothHeight: ' . $smoothHeight . ',
                            animation: "' . $animation_type . '",
                            animationLoop:  ' . $this->atts['animation_loop'] . ',
                            slideshow:      ' . $this->atts['slideshow_autostart'] . ',
                            animationSpeed: ' . $this->atts['animation_speed'] . ',
                            slideshowSpeed: ' . $this->atts['slideshow_speed'] . ',
                            start: function(slider){
                                current_flexslider = slider;

                                // We actually need to trigger resize two times, because otherwise the slider
                                // is not properly sized when placed into 2 or 3 columns layout
                                $(window).trigger("resize");
                                setTimeout(function () {
                                    $(window).trigger("resize");
                                }, 100);
                            }
                        });

                    });
                }(opjq));
                </script>';
                $markup = '
                <div class="flexslider op-testimonial-slider op-testimonial-slider-style-' . $this->atts['style'] . '">
                    <h2 class="op-testimonial-slider-title">' . $this->atts['title'] . '</h2>
                    <div class="op_testimonial_slider_' . $this->atts['element_id'] . '">
                        <ul class="op-testimonial-slides op-testimonial-slides-' . $columns . '-columns">
                            %s
                        </ul>
                    </div>
                </div>';
                break;

                case 6:
                $animation_type = $this->atts['animation_type'] == 'default' ? 'fade' : $this->atts['animation_type'];
                $columns = $this->atts['columns'];
                switch ($columns) {
                    case 1:
                        $itemWidth = '940';
                        $maxItems = '1';
                        $max1 = 1;
                        $max2 = 1;
                        $max3 = 1;
                        break;
                    case 2:
                        $itemWidth = '470';
                        $maxItems = '2';
                        $max1 = 2;
                        $max2 = 2;
                        $max3 = 1;

                        // There's a bug in flexslider that causes columns
                        // not to work when animation is set to fade,
                        // so we're forcing slide animation when
                        // there's more columns.
                        // This should be fixed in flexslider 2.7.0,
                        // and removed from here at that time
                        $animation_type = 'slide';

                        // Forcing smooth height to off,
                        // because it isn't working
                        // properly with columns
                        $smoothHeight = 'false';
                        break;
                    case 3:
                        $itemWidth = '310';
                        $maxItems = '3';
                        $max1 = 3;
                        $max2 = 2;
                        $max3 = 1;

                        // There's a bug in flexslider that causes columns
                        // not to work when animation is set to fade,
                        // so we're forcing slide animation when
                        // there's more columns.
                        // This should be fixed in flexslider 2.7.0,
                        // and removed from here at that time
                        $animation_type = 'slide';

                        // Forcing smooth height to off,
                        // because it isn't working
                        // properly with columns
                        $smoothHeight = 'false';
                        break;
                }
                $javascript .= '
                <script>
                (function ($) {
                    $(window).load(function() {
                        var current_flexslider;

                        function getGridSize() {
                            if (window.innerWidth < 768) {
                                return ' . $max3 . ';
                            } else if (window.innerWidth < 960) {
                                return ' . $max2 . ';
                            } else {
                                return ' . $max1 . ';
                            }
                            return result;
                        }

                        $(window).on("resize", function() {
                            var $currentRow = $(current_flexslider).parentsUntil(".row").parent();
                            var gridSize = getGridSize();

                            if ($currentRow.hasClass("narrow") || $currentRow.hasClass("two-columns")) {
                                gridSize = ' . $max3 . '
                            }

                            if (current_flexslider && current_flexslider.vars) {
                                current_flexslider.vars.minItems = gridSize;
                                current_flexslider.vars.maxItems = gridSize;
                            }
                        });

                        $(".op_testimonial_slider_' . $this->atts['element_id'] . '").flexslider({
                            selector: ".op-testimonial-slides > li",
                            pauseOnHover: true,
                            pauseOnAction: true,
                            touch: true,
                            controlNav: true,
                            directionNav: false,
                            itemMargin: 0,
                            itemWidth: ' . $itemWidth . ',
                            minItems: getGridSize(),
                            maxItems: getGridSize(),
                            move: 0,
                            smoothHeight: ' . $smoothHeight . ',
                            animation: "' . $animation_type . '",
                            animationLoop:  ' . $this->atts['animation_loop'] . ',
                            slideshow:      ' . $this->atts['slideshow_autostart'] . ',
                            animationSpeed: ' . $this->atts['animation_speed'] . ',
                            slideshowSpeed: ' . $this->atts['slideshow_speed'] . ',
                            start: function(slider){
                                current_flexslider = slider;

                                // We actually need to trigger resize two times, because otherwise the slider
                                // is not properly sized when placed into 2 or 3 columns layout
                                $(window).trigger("resize");
                                setTimeout(function () {
                                    $(window).trigger("resize");
                                }, 100);
                            }
                        });

                    });
                }(opjq));
                </script>';
                $title_color_style = $this->atts['title_color'] !== '' ? ' style="color:' . $this->atts['title_color'] . ';"' : '';
                $markup = '
                <div class="flexslider op-testimonial-slider op-testimonial-slider-style-' . $this->atts['style'] . '">
                    <h2 class="op-testimonial-slider-title"' . $title_color_style . '>' . $this->atts['title'] . '</h2>
                    <h3 class="op-testimonial-slider-subtitle">' . $this->atts['subtitle'] . '</h3>
                    <div class="op_testimonial_slider_' . $this->atts['element_id'] . '">
                        <ul class="op-testimonial-slides op-testimonial-slides-' . $columns . '-columns">
                            %s
                        </ul>
                    </div>
                </div>';
                break;
        }

        return array(
            'javascript' => $javascript,
            'markup' => $markup,
            'id' => $element_id
        );
    }
}