<?php

namespace OpSl\Element;

use OpSl\Util\Element as Util;

class EvergreenCountdownTimer implements ElementInterface
{
    protected $atts;

    public function __construct($atts)
    {
        $this->atts = $atts['data'];
    }

    public function render()
    {
        return sprintf('<div id="%%id%%" class="countdown-timer countdown-timer-style-%1$s" data-end="%%date%%" data-days_text_singular="%2$s" data-days_text="%3$s" data-hours_text_singular="%4$s" data-hours_text="%5$s" data-minutes_text_singular="%6$s" data-minutes_text="%7$s" data-seconds_text_singular="%8$s" data-seconds_text="%9$s" data-action="%10$s" data-redirect="%11$s" data-format="%12$s"><div id="countdownTimer"></div></div>', $this->atts['style'], $this->atts['days_text_singular'], $this->atts['days_text'], $this->atts['hours_text_singular'], $this->atts['hours_text'], $this->atts['minutes_text_singular'], $this->atts['minutes_text'], $this->atts['seconds_text_singular'], $this->atts['seconds_text'], $this->atts['action'], $this->atts['redirect_url'], $this->atts['format']);
    }
}