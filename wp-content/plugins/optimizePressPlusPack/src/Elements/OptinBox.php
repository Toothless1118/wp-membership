<?php

class OptimizePress_Elements_OptinBox
{

    /**
     * @var string
     */
    protected $tag = 'optin_box';

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
        add_filter('op_assets_oppp_element_styles', array($this, 'elementOpppStyles'), 10, 2);

        /*
         * Actions
         */
        add_action('admin_enqueue_scripts', array($this, 'enqueueStyles'));
        add_action('wp_enqueue_scripts', array($this, 'enqueueStyles'));
        add_action('op_assets_after_shortcode_init', array($this, 'initShortcodes'));
    }

    /**
     * Enqueue styles
     * @return void
     */
    public function enqueueStyles()
    {
        // Different dependencies when OP_SCRIPT_DEBUG is turned on and off (all styles are concatenated into opplus-front-all.css & opplus-back-all for production)
        if (OP_SCRIPT_DEBUG === '') {
            wp_enqueue_style('oppp-optin-box', OPPP_BASE_URL . 'css/elements/optin_box' . OP_SCRIPT_DEBUG . '.css', array(), OPPP_VERSION, 'all');
        }
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
            $styles = array_merge($styles, array(25, 26, 27, 28, 29, 30, 31));
        }

        return $styles;
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
     * Parses optin_box_box and op_optin_box_box shortcode
     * @param  array $atts
     * @param  string $content
     * @return string
     */
    public function shortcode($atts, $content)
    {
        /*
         * Cache busting
         */
        if (function_exists('wp_using_ext_object_cache')) {
            $extCache = wp_using_ext_object_cache();
            if (true === $extCache) {
                wp_using_ext_object_cache(false);
            }
        }

        // Decode encoded chars
        $atts = op_urldecode($atts);

        $uidOpBox = 'optbox_' . md5(serialize($atts) . $content . (int)defined('OP_LIVEEDITOR') . get_option('op_form_privacy_privacy_checkbox_text') . get_option('op_form_privacy_newsletter_checkbox_text'));

        if (false === $cached = get_transient('el_' . $uidOpBox)) {
            $oldAtts = $atts;
            $atts = shortcode_atts(array(
                'integration_type'          => 'custom',
                'email_address'             => '',
                'redirect_url'              => '',
                'style'                     => '1',
                'action'                    => '',
                'disable_name'              => 'N',
                'method'                    => 'post',
                'submit'                    => '',
                'email_field'               => '',
                'email_default'             => '',
                'email_order'               => 0,
                'name_field'                => '',
                'name_default'              => '',
                'name_order'                => 0,
                'top_color'                 => '',
                'thank_you_page'            => '',
                'already_subscribed_url'    => '',
                'action_page'               => '',
                'list'                      => '',
                'width'                     => '',
                'margin_top'                => '',
                'margin_right'              => '',
                'margin_bottom'             => '',
                'margin_left'               => '',
                'alignment'                 => 'center',
                'button_below'              => 'N',
                'gotowebinar'               => null,
                'double_optin'              => 'N',
                'signup_form_id'            => '',
                'welcome_email'             => 'N',
                'form_id'                   => null,
                'opm_integration'           => 'N',
                'opm_level'                 => 0,
                'opm_packages'              => '',
            ), $atts);
            extract($atts);

            $redirect_url = str_replace("&#038;", "&", $redirect_url);

            $data = array(
                'content'       => array(
                    'headline'  => '',
                    'paragraph' => '',
                    'privacy'   => '',
                ),
                'fields'        => array(),
                'submit_button' => '',
                'hidden_str'    => '',
                'extra_fields'  => array(),
                'top_color'     => $top_color,
                'width'         => $width,
                'margin_top'    => $margin_top,
                'margin_right'  => $margin_right,
                'margin_bottom' => $margin_bottom,
                'margin_left'   => $margin_left,
                'box_alignment' => $alignment,
                'shortcodes'    => array(),
            );

            $mc = preg_match_all('/'.op_shortcode_regex('optin_box_hidden|optin_box_field|optin_box_button').'/s',$content,$matches);
            $buttons = 0;
            if($mc > 0){
                for($i=0;$i<$mc;$i++){
                    switch($matches[2][$i]){
                        case 'optin_box_hidden':
                            $data['hidden_str'] .= op_clean_shortcode_content($matches[5][$i]);
                            break;
                        case 'optin_box_field':
                            $field = shortcode_atts(array(
                                'name' => '',
                            ), shortcode_parse_atts($matches[3][$i]));
                            if($field['name'] != ''){
                                $data['content'][$field['name']] = op_clean_shortcode_content($matches[5][$i]);
                                if($field['name'] == 'paragraph'){
                                    $data['content'][$field['name']] = wpautop(op_texturize(base64_decode($data['content'][$field['name']])));
                                }
                            }
                            break;
                        case 'optin_box_button':
                            $button_atts = shortcode_parse_atts($matches[3][$i]);
                            $button_atts['element_type'] = 'button';
                            $button_content = $matches[5][$i];
                            $type = op_get_var($button_atts,'type',1);
                            if ($type == '0') {
                                $uid = md5('btn_0_' . $button_content);
                                if (false === $buttonMarkup = get_transient('el_' . $uid)) {
                                    $buttonMarkup = op_sl_parse('button_0', $button_content);
                                    /*
                                     * Save only when there is some content returned
                                     */
                                    if (is_string($buttonMarkup) && 0 === strpos($buttonMarkup, '##')) {
                                        $buttonMarkup = substr($buttonMarkup, 2);
                                    } elseif (!empty($buttonMarkup)) {
                                        set_transient('el_' . $uid, $buttonMarkup, OP_SL_ELEMENT_CACHE_LIFETIME);
                                    }
                                }
                                $data['submit_button'] = $buttonMarkup;
                            } else if ($type == '1') {
                                $buttonShortcode = '[button_1';
                                foreach ($button_atts as $attribute => $value) {
                                    $buttonShortcode .= ' ' . $attribute . '="' . $value .'"';
                                }
                                $buttonShortcode .= ']' . $button_content . '[/button_1]';
                                $data['submit_button'] = '%%_custom_button_%%';
                                $data['shortcodes']['custom_button'] = $buttonShortcode;
                            } else {
                                $func = 'button_'.$type;
                                $data['submit_button'] = call_user_func_array(array('OptimizePress_Default_Assets',$func),array($button_atts,$button_content));
                            }
                            /*
                             * Used on some templates as "textual link"
                             */
                            if (op_get_var($button_atts, 'button_below') === 'Y') {
                                $data['link_button'] = '<button type="submit" class="default-link">'.$button_content.'</button>';
                            } else {
                                $data['link_button'] = '';
                            }
                            break;
                    }
                }
            }

            $has_name = true;
            if ($integration_type === 'email') {
                $has_name = false;
            }

            if (!$has_name) {
                $atts['action'] = '%%redirect_url%%';
                $atts['method'] = 'post';
            }
            $atts['op_current_url'] = '%%redirect_url%%';
            $atts['data'] = $data;
            $atts['no_name_styles'] = array('1','4','5','6','9','10');
            $atts['site_url_process_optin_form'] = get_bloginfo('url') . '/process-optin-form/';
            $atts['atts'] = $oldAtts;
            $atts['has_name'] = $has_name;

            $returnedData = op_sl_parse('optin_box', $atts);
            if (false === is_array($returnedData) && empty($returnedData)) {
                return;
            }
            extract($returnedData);

            if (!isset($required_fields)) {
                $required_fields = array();
            }

            if (!isset($hidden_fields)) {
                $hidden_fields = array();
            }

            $required = ' required="required"';

            /*
             * We need this check to remove required attribute (it was firing in feature box element)
             */
            if (defined('OP_LIVEEDITOR')) {
                $required = '';
            }

            $replacement_fields = array();
            foreach($extra_fields as $name => $value){
                $requiredField = in_array($name, $required_fields) ? $required : '';
                if (in_array($name, $hidden_fields)) {
                    $data['extra_fields'][$name] = '<input type="hidden" name="' . $name . '" value="%%_' . $name . '_%%" />';
                    $data['shortcodes'][$name] = rawurldecode($value);
                } else {
                    $data['extra_fields'][$name] = '<div class="text-box"><input type="text"' . $requiredField . ' name="'.$name.'" placeholder="'.$value.'" value="%%' . $name . '%%" /></div>';
                    $replacement_fields[] = $name;
                }
            }

            if(!$has_name){
                $fields['email']['name'] = 'email';
                $has_name_field && $fields['name']['name'] = 'name';
                $hidden = array(
                    'email_to' => $email_address,
                    'redirect_url' => $redirect_url,
                    'extra_fields' => $extra_fields,
                    'fields' => $fields
                );
                $data['hidden_str'] .= '<input type="hidden" name="op_optin_form_data" value="'.op_attr(base64_encode(json_encode($hidden))).'" /><input type="hidden" name="op_optin_form" value="Y" />';
            }

            $data['hidden_str'] = empty($data['hidden_str']) ? '' : '<div style="display:none">'.$data['hidden_str'].'</div>';

            foreach($fields as $name => $info){
                $input_container_start_html = ($style!=12 && $style!=15 && $style!=19 && $style!=20 && $style!=21 && $style!=22 && $style!=23 && $style!=24 ? '<div class="text-box '.$name.'">' : '');
                $input_container_end_html = ($style!=12 && $style!=15 && $style!=19 && $style!=20 && $style!=21 && $style!=22 && $style!=23 && $style!=24 ? '</div>' : '');
                $requiredField = in_array($name . '_field', $required_fields) ? $required : '';
                if ($name === 'email') {
                    $data['fields'][$name.'_field'] = $input_container_start_html.'<input type="email"' . $requiredField . ' name="'.$info['name'].'"'.($info['text']==''?'':' placeholder="'.$info['text'].'"').' value="%%' . $info['name'] . '%%" />'.$input_container_end_html;
                } else {
                    $data['fields'][$name.'_field'] = $input_container_start_html.'<input type="text"' . $requiredField . ' name="'.$info['name'].'"'.($info['text']==''?'':' placeholder="'.$info['text'].'"').' value="%%' . $info['name'] . '%%" />'.$input_container_end_html;
                }
                $replacement_fields[] = $info['name'];
            }

            if (!isset($data['extra_fields']) || !is_array($data['extra_fields'])) {
                $data['extra_fields'] = array();
            }

            if (isset($order)) {
                $data['order'] = $order;
            }

            _op_tpl('clear');
            $template = _op_tpl('_load_file', OPPP_BASE_DIR . 'templates/elements/optin_box/style_' . $style . '.php', $data, true);

            $cached = array(
                'template'              => $template,
                'replacement_fields'    => $replacement_fields,
                'shortcodes'            => isset($data['shortcodes']) ? $data['shortcodes'] : null,
            );

            /*
             * Cache busting needed again because of child elements
             */
            if (function_exists('wp_using_ext_object_cache')) {
                wp_using_ext_object_cache(false);
            }
            set_transient('el_' . $uidOpBox, $cached, OP_SL_ELEMENT_CACHE_LIFETIME);
        }

        /*
         * Loading validation script
         */
        op_validation_script();

        /*
         * Replace variables (prefixed and suffixed with %%)
         */
        if (is_array($cached)) {
            $html = str_replace('%%redirect_url%%', op_current_url(), $cached['template']);
            if (isset($cached['shortcodes']) && is_array($cached['shortcodes']) && count($cached['shortcodes']) > 0) {
                foreach ($cached['shortcodes'] as $key => $value) {
                    $html = str_replace('%%_' . $key . '_%%', do_shortcode($value), $html);
                }

            }
            foreach ($cached['replacement_fields'] as $field) {
                $html = str_replace('%%' . $field . '%%', getOptinUrlValue($field), $html);
            }
        } else {
            $html = str_replace('%%redirect_url%%', op_current_url(), $cached);
        }

        /*
         * Fix optin nonce
         */
        $nonce = wp_nonce_field('op_optin', 'op_optin_nonce', true, false);
        $html = str_replace('</form>', $nonce . '</form>', $html);

        /*
         * Cache busting
         */
        if (function_exists('wp_using_ext_object_cache')) {
            wp_using_ext_object_cache($extCache);
        }

        return $html;
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

new OptimizePress_Elements_OptinBox();
