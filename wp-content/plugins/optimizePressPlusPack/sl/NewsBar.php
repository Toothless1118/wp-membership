<?php

namespace OpSl\Element;

use OpSl\Util\Element as Util;

class NewsBar implements ElementInterface
{
    protected $atts;

    public function __construct($atts)
    {
        $this->atts = $atts['data'];
    }

    public function render()
    {
        // Init potentialy empty attributes (removed from request) to avoid PHP notices
        $featureUrl = '#';
        $featureText = $mainText = $mainBackground = $color = $featureWidth = $featureFontColor = $mainFontColor = '';

        // Init "feature_url" attribute check to avoid PHP notices
        if (isset($this->atts['feature_url']) && !empty($this->atts['feature_url'])) {
            $featureUrl = $this->atts['feature_url'];
        }

        // Init "feature_text" attribute check to avoid PHP notices
        if (isset($this->atts['feature_text']) && !empty($this->atts['feature_text'])) {
            $featureText = $this->atts['feature_text'];
        }

        // Init "main_text" attribute check to avoid PHP notices
        if (isset($this->atts['main_text']) && !empty($this->atts['main_text'])) {
            $mainText = $this->atts['main_text'];
        }

        // Init "main_background" attribute check to avoid PHP notices
        if (isset($this->atts['main_background']) && !empty($this->atts['main_background'])) {
            $mainBackground = 'background-color:' . $this->atts['main_background'] . ';';
        }

        // Init "color" attribute check to avoid PHP notices
        if (isset($this->atts['color']) && !empty($this->atts['color'])) {
            $color = 'background-color:' . $this->atts['color'] . '; border-color:' . $this->atts['color'] . ';';
        }

        // Init "feature_width" attribute check to avoid PHP notices
        if (isset($this->atts['feature_width']) && !empty($this->atts['feature_width'])) {
            $featureWidth = 'width:' . $this->atts['feature_width'] . ';';
        }

        // Init "feature_font_color" attribute check to avoid PHP notices
        if (isset($this->atts['feature_font_color']) && !empty($this->atts['feature_font_color'])) {
            $featureFontColor = 'color:' . $this->atts['feature_font_color'] . ';';
        }

        // Init "main_font_color" attribute check to avoid PHP notices
        if (isset($this->atts['main_font_color']) && !empty($this->atts['main_font_color'])) {
            $mainFontColor = 'color:' . $this->atts['main_font_color'] . ';';
        }

        $html = '
            <div id="' . $this->atts['id'] . '" class="news-bar news-bar-style-' . $this->atts['style'] . ' news-bar-position-' . $this->atts['feature_position'] . '">
                <p style="' . $mainBackground . '">
                    <strong style="' . $color . $featureWidth . '">
                        <a href="' . $featureUrl . '" style="' . $featureFontColor . '">' . urldecode($featureText) . '</a>
                    </strong>
                    <span style="' . $mainFontColor . '">' . urldecode($mainText) . '</span>
                </p>
            </div>';

        return array(
            'markup' => $html,
        );
    }
}