<?php
$html = '';
if ($protected && (false === $hideContent || false === $hideFiles)) {
    $html .= '<strong class="dl-name">' . $atts['title'] . '</strong>';
    $html .= '<div class="op-downloadlist-5 double-column cf">';
        $html .= '<div class="op-download-left">';
            $html .= '<p class="dl-desc">' . urldecode($content) . '</p>';
        $html .= '</div>';
        $html .= '<div class="op-download-right">';
            $html .= '<a class="btn-dl" ' . $blank . 'href="'. $fileLink . '">Download</a>';
        $html .= '</div>';
    $html .= '</div>';
} else if (!$protected) {
    $html .= '<strong class="dl-name">' . $atts['title'] . '</strong>';
    $html .= '<div class="op-downloadlist-5 double-column cf">';
        $html .= '<div class="op-download-left">';
            $html .= '<p class="dl-desc">' . urldecode($content) . '</p>';
        $html .= '</div>';
        $html .= '<div class="op-download-right">';
            $html .= '<a class="btn-dl" ' . $blank . 'href="'. $fileLink . '">Download</a>';
        $html .= '</div>';
    $html .= '</div>';
}

echo $html;
