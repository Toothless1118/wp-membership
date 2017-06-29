<?php

class OptimizePress_Elements_EvergreenCountdown
{
    /**
     * @var string
     */
    protected $tag = 'op_evergreen_countdown_timer';

    /**
     * Cookie ID
     * @var string
     */
    protected $cookieName;

    /**
     * Registering actions and filters
     */
    public function __construct()
    {
        /*
         * Filters
         */
        add_filter('op_assets_after_addons', array($this, 'addToAssetList'));
        add_filter('op_assets_parse_list', array($this, 'addToParseList'));
        add_filter('op_assets_lang_list', array($this, 'addToLangList'));
        add_filter('op_assets_addons_path', array($this, 'elementPath'), 10, 2);
        add_filter('op_assets_addons_url', array($this, 'elementUrl'), 10, 2);
        add_filter('op_cacheable_elements', array($this, 'addToCacheableElementsList'));
        add_filter('op_assets_oppp_element_styles', array($this, 'elementOpppStyles'), 10, 2);

        /*
         * Shortcodes
         */
        add_shortcode($this->tag, array($this, 'renderShortcode'));
    }

    /**
     * Add OPPP exclusive element styles to list for style picker to style differently.
     * @param  array $styles
     * @param  string $id
     * @return array
     */
    public function elementOpppStyles($styles, $id)
    {
        if ('op_assets_addon_' . $this->tag . '_style' === $id) {
            $styles = array_merge($styles, array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13));
        }

        return $styles;
    }

    /**
     * Parses parent ('op_slider') shortcode
     * @param  array $atts
     * @param  string $content
     * @return string
     */
    public function renderShortcode($atts, $content)
    {
         // Decode encoded chars
        $atts = op_urldecode($atts);

        // Get the attributes from the shortcode
        $atts = shortcode_atts(array(
            'style'                 => 1,
            'days'                  => 0,
            'hours'                 => 23,
            'minutes'               => 59,
            'seconds'               => 59,
            'action'                => 'none',
            'redirect_url'          => '',
            'years_text_singular'   => __('Year', 'optimizepress-plus-pack'),
            'years_text'            => __('Years', 'optimizepress-plus-pack'),
            'months_text_singular' => __('Month', 'optimizepress-plus-pack'),
            'months_text'           => __('Months', 'optimizepress-plus-pack'),
            'weeks_text_singular'   => __('Week', 'optimizepress-plus-pack'),
            'weeks_text'            => __('Weeks', 'optimizepress-plus-pack'),
            'days_text_singular'    => __('Day', 'optimizepress-plus-pack'),
            'days_text'             => __('Days', 'optimizepress-plus-pack'),
            'hours_text_singular'   => __('Hour', 'optimizepress-plus-pack'),
            'hours_text'            => __('Hours', 'optimizepress-plus-pack'),
            'minutes_text_singular' => __('Minute', 'optimizepress-plus-pack'),
            'minutes_text'          => __('Minutes', 'optimizepress-plus-pack'),
            'seconds_text_singular' => __('Second', 'optimizepress-plus-pack'),
            'seconds_text'          => __('Seconds', 'optimizepress-plus-pack'),
            'format'                => 'dhms',
        ), $atts, $this->tag);

        // Check for missing http in redirect URL
        if (isset($atts['redirect_url']) && !empty($atts['redirect_url']) && !(strpos($atts['redirect_url'], 'http://') === 0 || strpos($atts['redirect_url'], 'https://') === 0)) {
	        $protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://';
            $atts['redirect_url'] = $protocol . $atts['redirect_url'];
        }

        $elementId          = 'ec_' . md5(serialize($atts));
        $timeString         = sprintf('+ %d day %d hour %d minute %d second', $atts['days'], $atts['hours'], $atts['minutes'], $atts['seconds']);
        $cookieTimestamp    = $this->getCookieTimestamp($elementId);
        $sinceTimestamp     = time();//strtotime($timeString);

        // Check for expiration
        if (null === $cookieTimestamp) { // New request or a year has passed
            $this->setCookieTimestamp($sinceTimestamp, $elementId);
            $sinceTimestamp = strtotime($timeString);
        } else {
            // We add time to cookie time
            $cookieTimestamp = strtotime($timeString, $cookieTimestamp);

            if ($sinceTimestamp > $cookieTimestamp) { // Expired request
                // Check expiry action
                switch ($atts['action']) {
                    case 'redirect':
                        if (isset($atts['redirect_url']) && !empty($atts['redirect_url'])) { // Only when the redirect URL exists
                            wp_redirect($atts['redirect_url']);
                            exit();
                        }
                        break;
                    case 'restart_repeat':
                        $this->setCookieTimestamp($sinceTimestamp, $elementId);
                        $sinceTimestamp = strtotime($timeString, $sinceTimestamp);
                        break;
                    case 'hide':
                        return;
                }
            } else { // Active request - time diff is used
                $sinceTimestamp = $cookieTimestamp;
            }
        }

        $endDate            = date('Y/m/d H:i:s', $sinceTimestamp) . ' GMT '/* . get_option('gmt_offset')*/;

        /*
         * Cache busting
         */
        if (function_exists('wp_using_ext_object_cache')) {
            $extCache = wp_using_ext_object_cache();
            if (true === $extCache) {
                wp_using_ext_object_cache(false);
            }
        }

        // Different dependencies when OP_SCRIPT_DEBUG is turned on and off (all styles are concatenated into opplus-front-all.css & opplus-back-all for production)
        if (OP_SCRIPT_DEBUG === '') {
            wp_enqueue_style(OP_SN.'-countdown_timer', OPPP_BASE_URL . 'css/elements/countdown_timer' . OP_SCRIPT_DEBUG . '.css', array(), OPPP_VERSION, 'all');
        }

        $output = '';

        if (false === $data = get_transient('el_' . $elementId)) {
            $data = op_sl_parse('evergreen_countdown_timer', $atts);
            if (false === is_string($data) || empty($data)) {
                return;
            }

            set_transient('el_' . $elementId, $data, OP_SL_ELEMENT_CACHE_LIFETIME);
        }

        $output = str_replace(array('%id%', '%date%'), array($this->tag . '_' . op_generate_id(), $endDate), $data);

        /*
         * Cache busting
         */
        if (function_exists('wp_using_ext_object_cache')) {
            wp_using_ext_object_cache($extCache);
        }

        $fontMap =   array( 1 => '', 2 => '', 3 => '', 4 => 'open_sans', 5 => 'open_sans', 6 => 'open_sans_condensed',
                        7 => 'open_sans', 8 => 'montserrat', 9 => 'montserrat', 10 => 'montserrat', 11 => 'signika',
                        12 => array('open_sans', 'roboto'), 13 => 'play');

        if(is_array($fontMap[$atts['style']])){
            foreach($fontMap[$atts['style']] as $font){
                $output = $this->addCustomFont($font) . $output;
            }
        }else{
            $output = $this->addCustomFont($fontMap[$atts['style']]) . $output;
        }

        return $output;
    }

    /**
     * Adds custom translations for JS strings needed by custom element
     * @param  array $strings
     * @return array
     */
    public function addToLangList($strings)
    {
        $strings['days']                = __('Days', 'optimizepress-plus-pack');
        $strings['hours']               = __('Hours', 'optimizepress-plus-pack');
        $strings['minutes']             = __('Minutes', 'optimizepress-plus-pack');
        $strings['seconds']             = __('Seconds', 'optimizepress-plus-pack');
        $strings['action_after_expiry'] = __('Action After Expiry', 'optimizepress-plus-pack');
        $strings['redirect']            = __('Redirect', 'optimizepress-plus-pack');
        $strings['hide']                = __('Hide', 'optimizepress-plus-pack');
        $strings['restart_repeat']      = __('Restart', 'optimizepress-plus-pack');

        return $strings;
    }

    /**
     * Adds new element to asset list
     * @param  array $assets
     * @return array
     */
    public function addToAssetList($assets)
    {
        $assets['addon'][$this->tag] = array(
            'title'         => __('Evergreen Countdown timer', 'optimizepress-plus-pack'),
            'description'   => __('Insert an evergreen countdown timer on your pages that restarts for every new user. Great for creating urgency for conversions', 'optimizepress-plus-pack'),
            'settings'      => 'Y',
            'image'         => OPPP_BASE_URL . 'images/elements/' . $this->tag . '/' . $this->tag . '.png',
            'base_path'     => OPPP_BASE_URL . 'js/elements/',
        );

        return $assets;
    }

    /**
     * Adds new element to parse list
     * @param  array $assets
     * @return array
     */
    public function addToParseList($assets)
    {
        $assets[$this->tag] = array(
            'asset' => 'addon/' . $this->tag,
        );

        return $assets;
    }

    /**
     * Returns changed images path
     * @param  string $path
     * @param  string $tag
     * @return string
     */
    public function elementPath($path, $tag)
    {
        if ($tag === $this->tag) {
            $path = OPPP_BASE_DIR . 'images/elements/';
        }
        return $path;
    }

    /**
     * Returns changed images URL
     * @param  string $url
     * @param  string $tag
     * @return string
     */
    public function elementUrl($url, $tag)
    {
        if ($tag === $this->tag) {
            $url = OPPP_BASE_URL . 'images/elements/';
        }
        return $url;
    }

    /**
     * Get cookie timestamp for current queried object or null if it doesn't exist
     * @param string $elementId
     * @return mixed
     */
    protected function getCookieTimestamp($elementId)
    {
        // For LE we must always show counter
        if (defined('OP_LIVEEDITOR')) {
            return null;
        }

        if (isset($_COOKIE[$this->getCookieName($elementId)]) && !empty($_COOKIE[$this->getCookieName($elementId)])) {
            return $_COOKIE[$this->getCookieName($elementId)];
        }

        return null;
    }

    /**
     * Set cookie timestamp that will last a year
     * @param integer $timestamp
     */
    protected function setCookieTimestamp($timestamp, $elementId)
    {
        setcookie($this->getCookieName($elementId), $timestamp, time() + YEAR_IN_SECONDS);
    }

    /**
     * Lazy generate cookie name based on queried object
     * @param string $elementId
     * @return string
     */
    protected function getCookieName($elementId)
    {
        if (null === $this->cookieName) {
            if (defined('OP_LIVEEDITOR')) {
                $this->cookieName = $this->tag . '_expiry_timestamp';
            } else {
                $this->cookieName = $this->tag . '_expiry_timestamp_' . get_queried_object_id();
            }
        }

        return $this->cookieName . '_' . $elementId;
    }

    /**
     * Add element key and name to cacheable elements list.
     * Filter is read in OP Helper Tools plugin and uses it to compile a list of elements whose transient cache can be cleared.
     * @param array $elements
     * @return array
     */
    public function addToCacheableElementsList($elements)
    {
        $elements['el_ec_'] = __('Evergreen Countdown', 'optimizepress-plus-pack');

        return $elements;
    }

    public function addCustomFont($font){
        $font_string = '';
        global $custom_font_loaded;

        switch($font) {
            case 'open_sans':
                if (!isset($custom_font_loaded['open_sans'])) {
                    $font_string = '@import url("https://fonts.googleapis.com/css?family=Open+Sans:700,600,400");';
                    $custom_font_loaded['open_sans'] = true;
                }
                break;

            case 'open_sans_condensed':
                if (!isset($custom_font_loaded['open_sans_condensed'])) {
                    $font_string = '@import url("https://fonts.googleapis.com/css?family=Open+Sans+Condensed:300,700|Open+Sans:700,300,400");';
                    $custom_font_loaded['open_sans_condensed'] = true;
                }
                break;

            case 'montserrat':
                if (!isset($custom_font_loaded['montserrat'])) {
                    $font_string = '@import url("https://fonts.googleapis.com/css?family=Montserrat:400,700");';
                    $custom_font_loaded['montserrat'] = true;
                }
                break;

            case 'signika':
                if (!isset($custom_font_loaded['signika'])) {
                    $font_string = '@import url("https://fonts.googleapis.com/css?family=Signika+Negative:400,600,300,700");';
                    $custom_font_loaded['signika'] = true;
                }
                break;

            case 'roboto':
                if (!isset($custom_font_loaded['roboto'])) {
                    $font_string = '@import url("https://fonts.googleapis.com/css?family=Roboto+Slab:400,700");';
                    $custom_font_loaded['roboto'] = true;
                }
                break;

            case 'play':
                if (!isset($custom_font_loaded['play'])) {
                    $font_string = '@import url("https://fonts.googleapis.com/css?family=Play:400,700");';
                    $custom_font_loaded['play'] = true;
                }
                break;
        }

        if ($font_string !== '') {
            return '<style class="countdown-custom-font">' . $font_string . '</style>';
        }

    }
}

new OptimizePress_Elements_EvergreenCountdown();
