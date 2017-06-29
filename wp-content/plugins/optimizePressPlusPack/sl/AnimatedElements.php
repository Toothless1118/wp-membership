<?php

namespace OpSl\Element;

use OpSl\Util\Element as Util;

class AnimatedElements implements ElementInterface
{
    public function __construct($atts)
    {
        $this->labels   = $atts['data']['labels'];
        $this->effects  = $atts['data']['effects'];
    }

    public function render()
    {
        $output = '
        <div class="op-row-animated-elements-form">
            <h3>' . $this->labels['animated_element_title'] .'</h3>
            <p>' . $this->labels['animated_element_description'] . '</p>';
            //<label>' .  $this->labels['animate_when_scrolled_into_view'] . '</label>
            //<input type="checkbox" class="op_element_advanced_options_extras" name="op_animate_element_when_scrolled_into_view" data-name="animateWhenScrolled" id="op_animate_element_when_scrolled_into_view" />
            $output .= '
            <div class="op-animated-elements-form-row">
                <div class="op-animated-elements-form-column">
                    <label>' .  $this->labels['choose_effect'] . '</label>
                    <select name="op_animate_element_effect" class="op_element_advanced_options_extras" data-name="animationEffect" id="op_animate_element_effect">
                        <option value="">' .  $this->labels['select_effect'] . '</option>';

                    foreach ($this->effects as $effect) {
                        $output .= '
                        <option value="' .  $effect . '">' .  $effect . '</option>';
                    }

                    $output .= '
                    </select>
                </div>

                <div class="op-animated-elements-form-column">
                    <label>' .  $this->labels['animation_trigger_direction'] . '</label>
                    <select name="op_animate_element_direction" class="op_element_advanced_options_extras" data-name="animationDirection" id="op_animate_element_direction">
                        <option value="down" selected="selected">' .  $this->labels['down'] . '</option>
                        <option value="up">' .  $this->labels['up'] . '</option>
                    </select>
                </div>
            </div>

            <div class="op-animated-elements-form-row">
                <div class="op-animated-elements-form-column">
                    <label>' .  $this->labels['animation_delay_timer'] . '</label>
                    <input type="text" class="op_element_advanced_options_extras" placeholder="' .  $this->labels['enter_delay_in_seconds'] . '" name="op_animate_element_delay" data-name="animationDelay" id="op_animate_element_delay" />
                </div>

                <div class="op-animated-elements-form-column">
                    <label>' .  $this->labels['infinite_animation'] . '</label>
                    <input type="checkbox" class="op_element_advanced_options_extras" name="op_animate_element_infinite" data-name="animationInfinite" id="op_animate_element_infinite" />
                </div>
            </div>
        </div>';


        return $output;
    }
}