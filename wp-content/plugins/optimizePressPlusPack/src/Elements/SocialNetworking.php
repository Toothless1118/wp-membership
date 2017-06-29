<?php

class OptimizePress_Elements_SocialNetworking
{
    /**
     * @var string
     */
    protected $tag = 'op_social_networking';

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
        //add_filter('op_cacheable_elements', array($this, 'addToCacheableElementsList'));
        add_filter('op_assets_oppp_element_styles', array($this, 'elementOpppStyles'), 10, 2);

        /**
         * Actions
         */
        add_action('op_assets_after_shortcode_init', array($this, 'initShortcodes'));
        add_action('wp_enqueue_scripts', array($this, 'enqueueStyles'));

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
            $styles = array_merge($styles, array('style-1', 'style-2', 'style-3'));
        }

        return $styles;
    }

    /**
     * Enqueue styles
     * @return void
     */
    public function enqueueStyles()
    {
        if (OP_SCRIPT_DEBUG === '') {
            wp_enqueue_style('op_social_networking', OPPP_BASE_URL . 'css/elements/op_social_networking' . OP_SCRIPT_DEBUG . '.css', array(), OPPP_VERSION, 'all');
        }
    }


    /**
     * Initialize shortcodes (both with op prefix and without it)
     * @return void
     */
    public function initShortcodes()
    {
        add_shortcode($this->tag, array($this, 'shortcode'));
        add_shortcode('op_' . $this->tag, array($this, 'shortcode'));
    }

    /**
     * Parses guarantee_box and op_guarantee_box shortcode
     * @param  array $atts
     * @param  string $content
     * @return string
     */
    public function shortcode($atts, $content)
    {
        // Decode encoded chars
        $atts = op_urldecode($atts);


        $data = shortcode_atts(array(
            'style' => '',
            'new_tab' => '',
            'hide_text' => '',
            'background_color' => '',
            'icon_and_font_color_box' => '',
            'facebook_url' => '',
            'twitter_url' => '',
            'google_url' => '',
            'linkedin_url' => '',
            'youtube_url' => '',
            'instagram_url' => '',
            'pinterest_url' => '',
            'snapchat_url' => '',
        ),$atts);

        extract($data);

        $data['id'] = 'social-networking-'.op_generate_id();

        //Clear the template
        _op_tpl('clear');

        //Process the new template and load it
        return _op_tpl('_load_file', OPPP_BASE_DIR . 'templates/elements/social_networking/'.$data['style'].'.php', $data, true);
    }

    /**
     * Adds new element to asset list
     * @param  array $assets
     * @return array
     */
    public function addToAssetList($assets)
    {
        $assets['addon'][$this->tag] = array(
            'title'         => __('Social Networking', 'optimizepress-plus-pack'),
            'description'   => __('Increase your social exposure, grow your audience and boost your following.', 'optimizepress-plus-pack'),
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
            'asset' => 'addon/' . $this->tag
        );

        return $assets;
    }

    /**
     * Add additional strings for translation
     * @param array $strings
     * @return array
     */
    public function addToLangList($strings)
    {
        $strings['social_networking_fb'] = __('Facebook Account Link', 'optimizepress-plus-pack');
        $strings['social_networking_tw'] = __('Twitter Account Link', 'optimizepress-plus-pack');
        $strings['social_networking_g'] = __('Google Plus Account Link', 'optimizepress-plus-pack');
        $strings['social_networking_ln'] = __('LinkedIn Account Link', 'optimizepress-plus-pack');
        $strings['social_networking_yt'] = __('YouTube Account Link', 'optimizepress-plus-pack');
        $strings['social_networking_in'] = __('Instagram Account Link', 'optimizepress-plus-pack');
        $strings['social_networking_pt'] = __('Pinterest Account Link', 'optimizepress-plus-pack');
        $strings['social_networking_sc'] = __('Snapchat Account Link', 'optimizepress-plus-pack');
        $strings['social_networking_background_color'] = __('Social Networking Background Color', 'optimizepress-plus-pack');
        $strings['social_networking_new_tab'] = __('Open link in new tab?', 'optimizepress-plus-pack');
        $strings['social_networking_hide_text'] = __('Hide text?', 'optimizepress-plus-pack');
        $strings['social_networking_icon_and_font_color'] = __('Change Icons and Font color?', 'optimizepress-plus-pack');
        $strings['social_networking_icon_and_font_color_box'] = __('Social Networking Icon and Font Color', 'optimizepress-plus-pack');

        return $strings;
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
}

new OptimizePress_Elements_SocialNetworking();
