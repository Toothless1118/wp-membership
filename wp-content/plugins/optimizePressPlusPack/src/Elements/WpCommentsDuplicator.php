<?php

class OptimizePress_Wp_Comments_Duplicator
{
    /**
     * Shortcode
     * @var string
     */
    protected $tag = 'op_wp_comments_duplicator';

    /**
     * @var integer
     */
    protected $originalPostId;

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

        /*
         * Shortcodes
         */
        add_shortcode($this->tag, array($this, 'parseShortcode'));
    }

    /**
     * Adds new element to asset list
     * @param  array $assets
     * @return array
     */
    public function addToAssetList($assets)
    {
        // This element is only available in
        // live editor (not on posts)
        if (defined('OP_LIVEEDITOR')) {
            $assets['addon'][$this->tag] = array(
                'title'         => __('Wordpress Comments Duplicator', 'optimizepress-plus-pack'),
                'description'   => __('Add a Wordpress comments block to your page and show the comments from any page on your site. Perfect for launch pages where you want to show the same comments stream on multiple pages.', 'optimizepress-plus-pack'),
                'settings'      => 'Y',
                'image'         => OPPP_BASE_URL . 'images/elements/' . $this->tag . '/' . $this->tag . '.png',
                'base_path'     => OPPP_BASE_URL . 'js/elements/',
            );
        }

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
            'child_tags' => array($this->tag),
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
     * Adds custom translations for JS strings needed by custom element
     * @param  array $strings
     * @return array
     */
    public function addToLangList($strings)
    {
        $strings['comments_page_id'] = __('Comments Page ID', 'optimizepress-plus-pack');
        return $strings;
    }

    /**
     * Parses shortcode
     * @param  array $atts [description]
     * @return string rendered HTML output
     */
    public function parseShortcode($atts)
    {
        /*
         * In Live Editor we'll show only placeholder
         */
        if (defined('OP_LIVEEDITOR')) {
            /*
             * Check for double WP comments
             */
            require_once OP_ASSETS . 'live_editor.php';
            OptimizePress_LiveEditor_Assets::add_check_element('#wp_comments', __('You can only have WordPress comments on the page once.', 'optimizepress-plus-pack'));

            return '<div class="comments-placeholder" id="wp_comments">' . __('WordPress Comments Advanced', 'optimizepress-plus-pack') . '</div>';
        }

        setup_userdata(0);
        if (file_exists(OP_PAGE_DIR . 'comments.php')) {
            $tmp = OP_PAGE_DIR_REL . 'comments.php';
        } else {
            $tmp = '/pages/global/templates/comments.php';
        }
        ob_start();

        /*
         * Lets see if we need to show comments from different post
         */
        global $post;
        if (isset($atts['page']) && $post->ID !== (int) $atts['page'] && null !== $sourcePost = get_post($atts['page'])) {

            $this->originalPostId = $post->ID;

            /*
             * We need to add redirect param
             */
            add_action('comment_form', array($this, 'addCommentFormRedirect'));

            /*
             * Replacing original page URL with current page for pagination links
             */
            add_filter('get_comments_pagenum_link', array($this, 'replacePaginationLink'));

            /*
             * We'll switch "posts"
             */
            $oldPost = $post;
            $post = $sourcePost;

            /*
             * Showing comments
             */
            comments_template($tmp, true);

            /*
             * Now we are reverting back and clearing memory
             */
            $post = $oldPost;
            unset($sourcePost);
            unset($oldPost);

        } else {

            comments_template($tmp, true);
        }

        $output = ob_get_contents();
        ob_end_clean();
        return $output;
    }

    /**
     * Adds redirect_to param with host URL (to avoid returning to original URL after posting)
     * @param int $postId
     * @return  void
     */
    public function addCommentFormRedirect($postId)
    {
        $redirectUrl = get_permalink($this->originalPostId);

        preg_match('#/comment-page-([0-9]+)?/$#', $_SERVER['REQUEST_URI'], $matches);
        if (is_array($matches) && count($matches)) {
            $redirectUrl .= ltrim($matches[0], '/');
        }

        echo '<input type="hidden" name="redirect_to" value="' . esc_url($redirectUrl) . '" />';
    }

    /**
     * Replaces comments pagination links from original URL to host URL
     * @param  string $result
     * @return string
     */
    public function replacePaginationLink($result)
    {
        $hostUrl        = get_permalink($this->originalPostId);
        $originalUrl    = get_permalink();

        return str_replace($originalUrl, $hostUrl, $result);
    }
}

new OptimizePress_Wp_Comments_Duplicator();