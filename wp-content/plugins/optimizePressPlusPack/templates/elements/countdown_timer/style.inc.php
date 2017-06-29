<?php
$custom_font_loaded = Array();

function load_countdown_font($font) {

    global $custom_font_loaded;
    $font_string = '';

    switch($font) {
        case 'open_sans':
                if (!isset($custom_font_loaded['open_sans'])) {
                    $font_string = '@import url("http://fonts.googleapis.com/css?family=Open+Sans:700,600,400");';
                    $custom_font_loaded['open_sans'] = true;
                }
                break;

            case 'open_sans_condensed':
                if (!isset($custom_font_loaded['open_sans_condensed'])) {
                    $font_string = '@import url("http://fonts.googleapis.com/css?family=Open+Sans+Condensed:300,700|Open+Sans:700,300,400");';
                    $custom_font_loaded['open_sans_condensed'] = true;
                }
                break;

            case 'montserrat':
                if (!isset($custom_font_loaded['montserrat'])) {
                    $font_string = '@import url("http://fonts.googleapis.com/css?family=Montserrat:400,700");';
                    $custom_font_loaded['montserrat'] = true;
                }
                break;

            case 'signika':
                if (!isset($custom_font_loaded['signika'])) {
                    $font_string = '@import url("http://fonts.googleapis.com/css?family=Signika+Negative:400,600,300,700");';
                    $custom_font_loaded['signika'] = true;
                }
                break;

            case 'roboto':
                if (!isset($custom_font_loaded['roboto'])) {
                    $font_string = '@import url("http://fonts.googleapis.com/css?family=Roboto+Slab:400,700");';
                    $custom_font_loaded['roboto'] = true;
                }
                break;

            case 'play':
                if (!isset($custom_font_loaded['play'])) {
                    $font_string = '@import url("http://fonts.googleapis.com/css?family=Play:400,700");';
                    $custom_font_loaded['play'] = true;
                }
                break;
    }

    if ($font_string !== '') {
        echo '<style class="countdown-custom-font">' . $font_string . '</style>';
    }
}
