<?php

class OptimizePress_Elements_SocialSharing
{
    /**
     * @var string
     */
    protected $tag = 'social_sharing';

    /**
     * Registering actions and filters
     */
    public function __construct()
    {
        /*
         * Filters
         */
        add_filter('op_assets_before_addons', array($this, 'addToAssetList'), 100);
        // add_filter('op_assets_lang_list', array($this, 'addToLangList'));
        add_filter('op_assets_core_path', array($this, 'elementPath'), 10, 2);
        add_filter('op_assets_core_url', array($this, 'elementUrl'), 10, 2);

        /*
         * Actions
         */
        add_action('admin_enqueue_scripts', array($this, 'enqueueStyles'));
        add_action('wp_enqueue_scripts', array($this, 'enqueueStyles'));
        add_action('op_assets_after_shortcode_init', array($this, 'initShortcodes'));
        add_filter('op_assets_oppp_element_styles', array($this, 'elementOpppStyles'), 10, 2);
    }

    /**
     * Add OPPP exclusive element styles to list for style picker to style differently.
     * @param  array $styles
     * @param  string $id
     * @return array
     */
    public function elementOpppStyles($styles, $id)
    {
        if ('op_assets_core_' . $this->tag . '_style' === $id) {
            $styles = array_merge($styles, array("style-22","style-23","style-24", "style-25", "style-26", "style-27", "style-28"));
        }

        return $styles;
    }

    /**
     * Enqueue styles
     * @return void
     */
    public function enqueueStyles()
    {
        // Different dependencies when OP_SCRIPT_DEBUG is turned on and off (all styles are concatenated into opplus-front-all.css & opplus-back-all for production)
        if (OP_SCRIPT_DEBUG === '') {
            wp_enqueue_style('op_social_sharing', OPPP_BASE_URL . 'css/elements/social_sharing' . OP_SCRIPT_DEBUG . '.css', array(), OPPP_VERSION, 'all');
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
        global $post;
        $page_id = defined('OP_PAGEBUILDER_ID') ? OP_PAGEBUILDER_ID : $post->ID;
        $url = get_permalink($page_id);
        $atts = op_urldecode($atts);

        $data = shortcode_atts(array(
            'style' => 'horizontal',
            'fb_like_url' => $url,
            'fb_color' => 'light',
            'fb_lang' => op_default_attr('comments', 'facebook', 'language'),
            'fb_text' => 'Like',
            'fb_button_text' => 'Share',
            'tw_text' => 'Share',
            'tw_color' => 'light',
            'tw_lang' => 'en',
            'tw_url' => $url,
            'tw_button_text' => 'Share',
            'tw_name' => '',
            'g_url' => $url,
            'g_color' => 'light',
            'g_lang' => 'en-GB',
            'g_button_text' => 'Share',
            'p_url' => $url,
            'p_color' => 'light',
            'p_image_url' => $url,
            'p_description' => '',
            'su_url' => $url,
            'linkedin_url' => $url,
            'linkedin_color' => 'light',
            'linkedin_lang' => 'en_US',
            'alignment' => 'center',
            'background_color' => ''
        ),$atts);

        extract($data);

        $attrs = ($style=='horizontal' ? array(
            'fb_button_style' => 'button_count',
            'fb_extra' => ' data-width="89"',
            'tw_extra' => ' style="width:98px"',
            'g_size' => 'medium',
            'g_extra' => ' style="width:85px"',
            'su_style' => '1',
            'p_extra' => ' data-pin-config="beside"',
            // 'p_extra' => ' data-pin-config="beside"',
        ) : array(
            'fb_extra' => '',
            'fb_button_style' => 'box_count',
            'tw_extra' => ' data-count="vertical"',
            'g_size' => 'tall',
            'su_style' => '5',
            'g_extra' => '',
            'p_extra' => ' data-pin-config="above"',
        ));

        $data['id'] = 'social-sharing-'.op_generate_id();

        //Init Facebook html
        $fbAppId = op_default_attr('comments','facebook','id') ? 'appId: ' . op_default_attr('comments','facebook','id') . ',' : '';
        $data['fb_html'] = '
            <script>
            window.fbAsyncInit = function() {
                FB.init({
                    ' . $fbAppId . '
                    xfbml      : true,
                    version    : \'v2.7\'
                });
                opjq(window).trigger("OptimizePress.fbAsyncInit");
            };
            (function(d, s, id){
                var js, fjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id)) {return;}
                js = d.createElement(s); js.id = id;
                js.src = "//connect.facebook.net/' . $fb_lang . '/all.js";
                fjs.parentNode.insertBefore(js, fjs);
            }(document, \'script\', \'facebook-jssdk\'));

            if (typeof FB !== "undefined") { FB.XFBML.parse(); }
            </script>
            <div class="fb-like" data-send="false"'.($fb_like_url!=''?' data-href="'.$fb_like_url.'"':'').' data-layout="'.$attrs['fb_button_style'].'" data-show-faces="false" data-action="'.$fb_text.'" data-colorscheme="'.$fb_color.'"'.$attrs['fb_extra'].'></div>
        ';

        //Init Twitter html
        $data['twitter_html'] = '
            <a href="https://twitter.com/share" class="twitter-share-button"'.(ucfirst($tw_text) != ''?' data-text="'.op_attr($tw_text).'"':'').($tw_url != ''?' data-url="'.op_attr($tw_url).'"':'').($tw_name != ''?' data-via="'.op_attr($tw_name).'"':'').' data-lang="'.$tw_lang.'"'.$attrs['tw_extra'].'>'.__('Tweet','optimizepress-plus-pack').'</a>
            <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="https://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
            <script>
            if (typeof twttr === "object"
                && typeof twttr.widgets === "object"
                && typeof twttr.widgets.load === "function") {
                try {
                    twttr.widgets.load();
                } catch(e) {}
            }
            </script>
        ';

        //Init Google+ html
        $data['gplus_html'] = '
            <div class="g-plusone" data-size="'.$attrs['g_size'].'" data-href="'.$g_url.'"'.$attrs['g_extra'].'></div>
            <script type="text/javascript">
              window.___gcfg = {lang: \''.$g_lang.'\'};
              (function() {
                var po = document.createElement(\'script\'); po.type = \'text/javascript\'; po.async = true;
                po.src = \'https://apis.google.com/js/plusone.js\';
                var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(po, s);
              })();
            </script>
        ';

        //Init Pinterest html
        // $data['pinterest_html'] = '
        //     <span class="pinbreak"><a href="http://pinterest.com/pin/create/button/?url='.$p_url.'&media='.$p_image_url.($p_description!=''?'&description='.$p_description:'').'" class="pin-it-button" data-pin-do="buttonPin" count-layout="'.$style.'"'.$attrs['p_extra'].'><img border="0" src="//assets.pinterest.com/images/PinExt.png" title="'.__('Pin It','optimizepress-plus-pack').'" /></a></span>
        //     <script type="text/javascript" src="//assets.pinterest.com/js/pinit.js"></script>
        // ';
        $data['pinterest_html'] = '
            <span class="pinbreak"><a href="https://pinterest.com/pin/create/button/?url='.$p_url.'&media='.$p_image_url.($p_description!=''?'&description='.$p_description:'').'" class="pin-it-button" data-pin-do="buttonPin" '.$attrs['p_extra'].'><img border="0" src="//assets.pinterest.com/images/PinExt.png" title="'.__('Pin It','optimizepress-plus-pack').'" width="43" height="21" /></a></span>
            <script type="text/javascript" src="//assets.pinterest.com/js/pinit.js"></script>
            <script>
            if (typeof window.parsePinBtns === "function") {
                window.parsePinBtns();
            }
            </script>
        ';

        //Init StumbleUpon html
        $data['su_html'] = '
            <div class="op-stumbleupon-badge">
            <su:badge layout="'.$attrs['su_style'].'" location="'.$su_url.'"></su:badge>

            <script type="text/javascript">
              (function() {
                var li = document.createElement(\'script\'); li.type = \'text/javascript\'; li.async = true;
                li.src = (\'https:\' == document.location.protocol ? \'https:\' : \'http:\') + \'//platform.stumbleupon.com/1/widgets.js\';
                var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(li, s);
              })();
            </script>
            </div>
        ';

        //Init LinkedIn html
        $data['linkedin_html'] = '
            <script src="//platform.linkedin.com/in.js" type="text/javascript">
            lang: '.$linkedin_lang.'
                </script>
                <script type="IN/Share" data-url="'.$linkedin_url.'" data-counter="top"></script>
                <script>
                if (typeof IN === "object" && typeof IN.parse === "function") {
                    IN.parse();
                }
                </script>
        ';

        //if (is_admin()) return __('--- Social Sharing Element ---', 'optimizepress-plus-pack');

        //Clear the template
        _op_tpl('clear');

        // Enqueue countdown scripts, but only if the page was not created with live editor
        if (get_post_meta($post->ID,'_'.OP_SN.'_pagebuilder',true) != 'Y') {
            op_sharrre_scripts();
        }

        //Process the new template and load it
        return _op_tpl('_load_file', OPPP_BASE_DIR . 'templates/elements/social_sharing/'.$data['style'].'.php', $data, true);
    }

    /**
     * Adds new element to asset list
     * @param  array $assets
     * @return array
     */
    public function addToAssetList($assets)
    {
        $assets['core'][$this->tag]['settings']  = 'Y';
        // $assets['core'][$this->tag]['image']     = OPPP_BASE_URL . 'images/elements/' . $this->tag . '/' . $this->tag . '.png';
        $assets['core'][$this->tag]['base_path'] = OPPP_BASE_URL . 'js/elements/';

        return $assets;
    }

    /**
     * Add additional strings for translation
     * @param array $strings
     * @return array
     */
    public function addToLangList($strings)
    {
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

new OptimizePress_Elements_SocialSharing();
