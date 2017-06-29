<?php

class OptimizePress_Elements_FileDownload
{
    /**
     * @var array
     */
    private static $temp_val = array();

    /**
     * @var string
     */
    protected $childTag = 'file_download_item';

    /**
     * @var string
     */
    protected $parentTag = 'file_download';

    /**
     * Registering actions and filters
     */
    public function __construct()
    {
        /*
         * Filters
         */
        add_filter('op_assets_before_addons', array($this, 'addToAssetList'), 100);
        add_filter('op_assets_children_list', array($this, 'addToChildrenList'));
        add_filter('op_assets_lang_list', array($this, 'addToLangList'));
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
     * Add OPPP exclusive element styles to list for style picker to style differently.
     * @param  array $styles
     * @param  string $id
     * @return array
     */
    public function elementOpppStyles($styles, $id)
    {
        if ('op_assets_core_' . $this->parentTag . '_style' === $id) {
            $styles = array_merge($styles, array(5));
        }

        return $styles;
    }

    /**
     * Initialize shortcodes (both with op prefix and without it)
     * @return void
     */
    public function initShortcodes()
    {
        add_shortcode($this->parentTag, array($this, 'parentShortcode'));
        add_shortcode('op_' . $this->parentTag, array($this, 'parentShortcode'));

        add_shortcode($this->childTag, array($this, 'childShortcode'));
        add_shortcode('op_' . $this->childTag, array($this, 'childShortcode'));
    }

    /**
     * Adds children to existing elements
     *
     * @param array $children
     * @return array
     */
    public function addToChildrenList($children)
    {
        $children['file_download'][] = 'op_' . $this->childTag;

        return $children;
    }

    /**
     * Enqueue styles
     * @return void
     */
    public function enqueueStyles()
    {
        // Different dependencies when OP_SCRIPT_DEBUG is turned on and off (all styles are concatenated into opplus-front-all.css & opplus-back-all for production)
        if (OP_SCRIPT_DEBUG === '') {
            wp_enqueue_style('op_file_download', OPPP_BASE_URL . 'css/elements/file_download' . OP_SCRIPT_DEBUG . '.css', array(), OPPP_VERSION, 'all');
        }
    }

    /**
     * Parses file_download and op_file_download shortcode
     * @param  array $atts
     * @param  string $content
     * @return string
     */
    public function parentShortcode($atts, $content)
    {
        // Decode encoded chars
        $atts = op_urldecode($atts);

        $atts = shortcode_atts(array(
            'style' => 1,
        ), $atts);

        self::$temp_val['op_file_download'] = $atts['style'];

        $content = do_shortcode(op_clean_shortcode_content($content));
        $content = op_process_asset_content($content);

        $data['atts'] = $atts;
        $data['content'] = $content;

        switch($atts['style']) {
            case 5:
                $template = '2';
                break;
            default:
                $template = '1';
                break;
        }

        return _op_tpl('_load_file', OPPP_BASE_DIR . 'templates/elements/file_download/' . $template . '_parent.php', $data, true);
    }

    /**
     * @param array $atts
     * @param string $content
     * @return string
     */
    public function childShortcode($atts, $content)
    {
        $atts = op_urldecode($atts);

        $atts = shortcode_atts(array(
            'title' => '',
            'icon' => '',
            'icon_folder' => 'file_download',
            'file' => '',
            'package' => '',
            'level' => '',
            'new_window' => '',
            'hide_alert' => ''
        ), $atts);

        if (empty($atts['file'])) {
            return '';
        }

        $hideAlert = '';
        $amazon = false;
        $destinationFolder = '';
        $forUrl = '';
        $onlyFileName = '';
        $hideContent = false;
        $hideFiles = false;
        if ((!empty($atts['package']) || !empty($atts['level']))) {
            if (defined('WS_PLUGIN__OPTIMIZEMEMBER_VERSION')) { // is OPM element present and OPM activated?
                $filesDir = $GLOBALS['WS_PLUGIN__']['optimizemember']['c']['files_dir'];
                if (isset($GLOBALS['WS_PLUGIN__']['optimizemember']['o']['filter_wp_query']) && 'all' === $GLOBALS['WS_PLUGIN__']['optimizemember']['o']['filter_wp_query'][0]) {
                    $hideFiles = true;
                }
                $uploads = wp_upload_dir();
                $baseUploadDir = $uploads['basedir'];
                $baseUploadUrl = $uploads['baseurl'];
                $fileNameWithFolder = str_replace($baseUploadUrl, '', $atts['file']);
                $onlyFileName = basename($atts['file']);
                $fullFilePath = $baseUploadDir . '/' . $fileNameWithFolder;
                $attachmentId = op_get_image_id($baseUploadUrl . $fileNameWithFolder);
                // protection needed based on level or package selected
                if (!empty($atts['package'])) { // if package selected, level is ignored
                    $destinationFolder = $filesDir . '/access-optimizemember-ccap-' . $atts['package'];
                    $forUrl = '/access-optimizemember-ccap-' . $atts['package'];
                } else if (!empty($atts['level'])) {
                    $destinationFolder = $filesDir . '/access-optimizemember-level' . $atts['level'];
                    $forUrl = '/access-optimizemember-level' . $atts['level'];
                }
                $amazon = false;
                if (c_ws_plugin__optimizemember_utils_conds::using_amazon_s3_storage()) {
                    $amazon = true;
                }
                if (c_ws_plugin__optimizemember_utils_conds::using_amazon_cf_storage()) {
                    $amazon = true;
                }
                if (!$amazon) {
                    if (!is_dir($destinationFolder)) {
                        mkdir($destinationFolder, 0777, true);
                    }
                    // copying only in admin
                    if (defined('OP_LIVEEDITOR')) {
                        copy($fullFilePath, $destinationFolder . '/' . $onlyFileName);
                        /// below code should be uncommented if we decide to remove the file from uploads folder
                        //rename($fullFilePath, $destinationFolder . '/' . $onlyFileName);
                        // removing image from db, too
                        //if (!empty($attachmentId)) wp_delete_attachment($attachmentId);
                    }
                }
                $protected = true;
                // hide alert?
                if (!empty($atts['hide_alert']) && $atts['hide_alert'] == 'Y') {
                    $hideAlert = '&optimizemember_skip_confirmation=yes';
                }
                // dealing with level or packages
                if (empty($package)) {
                    if (!current_user_can("access_optimizemember_level" . $atts['level'])) {
                        $hideContent = true;
                    }
                } else {
                    if (!current_user_can("access_optimizemember_ccap_" . $atts['package'])) {
                        $hideContent = true;
                    }
                }
            } else {
                if (defined('OP_LIVEEDITOR')) {
                    return '<p>In order to use this element, you have to enable OptimizeMember plugin !!!</p>';
                } else {
                    return '';
                }
            }
        } else {
            $protected = false;
        }
        if ($protected) {
            if (!$amazon) {
                $fileLink = site_url('?optimizemember_file_download=') . $forUrl . '/' .  $onlyFileName . $hideAlert;
            } else {
                /// below code should be uncommented if we decide to remove the file from uploads folder
                // removing file from uploads folder
                //unlink($fullFilePath);
                //if (!empty($attachmentId)) wp_delete_attachment($attachmentId);
                $fileLink = site_url('?optimizemember_file_download=') . '/' .  $onlyFileName . $hideAlert;
            }
        } else {
            $fileLink = $atts['file'];
        }
        $blank = '';
        if (!empty($atts['new_window']) && $atts['new_window'] == 'Y') {
            $blank = ' target="_blank" ';
        }

        $data['blank']          = $blank;
        $data['fileLink']       = $fileLink;
        $data['atts']           = $atts;
        $data['content']        = $content;
        $data['protected']      = $protected;
        $data['hideContent']    = $hideContent;
        $data['hideFiles']      = $hideFiles;

        $style = '2';
        if(isset(self::$temp_val['op_file_download'])){
            $style = self::$temp_val['op_file_download'];
        }

        switch($style) {
            case 5:
                $template = '2';
                break;
            default:
                $template = '1';
                break;
        }

        return _op_tpl('_load_file', OPPP_BASE_DIR . 'templates/elements/file_download/' . $template . '_child.php', $data, true);
    }

    /**
     * Adds new element to asset list
     * @param  array $assets
     * @return array
     */
    public function addToAssetList($assets)
    {
        $assets['core'][$this->parentTag]['settings']  = 'Y';
        // $assets['core'][$this->parentTag]['image']     = OPPP_BASE_URL . 'images/elements/' . $this->parentTag . '/' . $this->parentTag . '.png';
        $assets['core'][$this->parentTag]['base_path'] = OPPP_BASE_URL . 'js/elements/';

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
        if ($tag === $this->parentTag) {
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
        if ($tag === $this->parentTag) {
            $url = OPPP_BASE_URL . 'images/elements/';
        }
        return $url;
    }
}

new OptimizePress_Elements_FileDownload();