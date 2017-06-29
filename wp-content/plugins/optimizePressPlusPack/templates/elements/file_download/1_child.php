<?php
$html = '';
if ($protected && (false === $hideContent || false === $hideFiles)) {
    $html .= '<li>';
        $img_size = op_get_image_html_attribute(OP_ASSETS_URL . 'images/' . $atts['icon_folder'] . '/icons/' . $atts['icon']);
        $html .= '<a ' . $blank . ' href="' . $fileLink . '"><img src="' . OP_ASSETS_URL . 'images/' . $atts['icon_folder'] . '/icons/' . $atts['icon'] . '" alt="icon" class="thumb" ' . $img_size . ' /></a>';
        $html .= '<div class="content">';
            $html .= '<a ' . $blank . 'href="'. $fileLink . '">' . $atts['title'] . '</a>';
            $html .= '<p>' . urldecode($content) . '</p>';
        $html .= '</div>';
    $html .= '</li>';
} else if (!$protected) {
    $html .= '<li>';
        $img_size = op_get_image_html_attribute(OP_ASSETS_URL . 'images/' . $atts['icon_folder'] . '/icons/' . $atts['icon']);
        $html .= '<a ' . $blank . ' href="' . $fileLink.'"><img src="' . OP_ASSETS_URL . 'images/' . $atts['icon_folder'] . '/icons/' . $atts['icon'] . '" alt="icon" class="thumb" ' . $img_size . '/></a>';
        $html .= '<div class="content">';
            $html .= '<a ' . $blank.' href="' . $fileLink . '">' . $atts['title'] . '</a>';
            $html .= '<p>' . urldecode($content) . '</p>';
        $html .= '</div>';
    $html .= '</li>';
}

echo $html;