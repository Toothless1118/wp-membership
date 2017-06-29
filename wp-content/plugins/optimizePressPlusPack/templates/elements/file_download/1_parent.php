<?php
$html = '';

// Styles with titles are handled here
if ((int) $atts['style'] === 2) {
    $html .= '<div class="downloadlist-title-container">
                <h3 class="downloadlist-title">' .
                    __('Downloads','optimizepress-plus-pack') .
                '</h3>
            </div>';
}

$html .= '<ul class="downloadlist-' . $atts['style'] . ' border">' . $content . '</ul>';

echo $html;