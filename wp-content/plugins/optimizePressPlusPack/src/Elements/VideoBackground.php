<?php

class OptimizePress_Elements_VideoBackground
{
    /**
     * Registering actions and filters
     */
    public function __construct()
    {
        /*
         * Actions
         */
        add_action('op_le_after_row_options', array($this, 'addRowOptions'));

        /*
         * Filters
         */
        add_filter('op_inside_row', array($this, 'renderVideoMarkup'), 10, 1);
        add_filter('op_cacheable_elements', array($this, 'addToCacheableElementsList'));
    }

    /**
     * Adding new form fields
     *
     * @return  void
     */
    public function addRowOptions()
    {
        ?>
        <div class="op-row-video-background-form">
            <h3><?php _e('Background Video', 'optimizepress-plus-pack'); ?></h3>

            <div class="op-video-background-type-container">
                <label class="op-video-background-subtitle"><?php _e('Video Type', 'optimizepress-plus-pack'); ?></label>
                <select class="op-video-background-type select" name="op[row][addon][video_select_background_type]">
                    <option disabled selected>Select Video Type</option>
                    <option value="youtube">Youtube</option>
                    <option value="url">URL</option>
                </select>
                <div class="op-hidden">
                    <?php op_text_field('op[row][addon][video_background_type]', ''); ?>
                </div>
            </div>

            <div class="op-video-background-type-youtube">

                <label class="op-video-background-subtitle"><?php _e('YouTube Video URL', 'optimizepress-plus-pack'); ?></label>
                <?php op_text_field('op[row][addon][video_background_youtube]', ''); ?>

                <div class="op-le-row-options-columns">
                    <div class="op-le-row-options-column">
                        <label class="op-video-background-subtitle"><?php _e('Video width (px)', 'optimizepress-plus-pack'); ?></label>
                        <?php op_text_field('op[row][addon][video_background_youtube_width]', ''); ?>
                    </div>
                    <div class="op-le-row-options-column">
                        <label class="op-video-background-subtitle"><?php _e('Video height (px)', 'optimizepress-plus-pack'); ?></label>
                        <?php op_text_field('op[row][addon][video_background_youtube_height]', ''); ?>
                    </div>
                </div>
                <p class="op-warning-message"><?php _e('Correct video width and hight are required to accurately set the aspect ratio of the video.', 'optimizepress-plus-pack'); ?></p>

            </div>

            <div class="op-video-background-type-url">
                <label class="op-video-background-subtitle"><?php _e('URL (.mp4)', 'optimizepress-plus-pack'); ?></label>
                <?php op_text_field('op[row][addon][video_background_url_mp4]', ''); ?>

                <label class="op-video-background-subtitle"><?php _e('URL (.webm)', 'optimizepress-plus-pack'); ?></label>
                <p class="op-micro-copy"><?php _e('Not required but recommended to ensure compatibility with most browsers', 'optimizepress-plus-pack');?></p>
                <?php op_text_field('op[row][addon][video_background_url_webm]', ''); ?>

                <label class="op-video-background-subtitle"><?php _e('URL (.ogv)', 'optimizepress-plus-pack');?></label>
                <p class="op-micro-copy"><?php _e('Not required but recommended to ensure compatibility with most browsers', 'optimizepress-plus-pack');?></p>
                <?php op_text_field('op[row][addon][video_background_url_ogv]', ''); ?>

                <label class="op-video-background-subtitle"><?php _e('Video vertical alignment', 'optimizepress-plus-pack'); ?></label>
                <select class="op-video-vertical-align" name="op[row][addon][video_select_vertical_align]">
                    <option value="top">Top</option>
                    <option value="middle">Middle</option>
                    <option value="bottom">Bottom</option>
                </select>
                <div class="op-hidden">
                    <?php op_text_field('op[row][addon][video_background_vertical_align]', ''); ?>
                </div>
            </div>


            <div class="op-le-row-options-columns">
                <div class="op-le-row-options-column">
                    <label class="op-video-background-subtitle"><?php _e('Video overlay color', 'optimizepress-plus-pack'); ?></label>
                    <?php op_color_picker('op[row][addon][video_background_overlay_color]', '','op_row_addon_video_background_overlay_color'); ?>
                </div>

                <div class="op-le-row-options-column">
                    <div class="field-slider">
                        <label class="op-video-background-subtitle"><?php _e('Video overlay opacity', 'optimizepress-plus-pack'); ?></label>
                        <?php op_slider_picker('op[row][addon][video_background_overlay_opacity]', 0, 'op_row_addon_video_background_overlay_opacity', 0, 100, '%'); ?>
                    </div>
                </div>
            </div>

            <div class="op-le-row-options-columns">
                <div class="op-le-row-options-column">
                    <label class="op-video-background-subtitle"><?php _e('Video overlay image', 'optimizepress-plus-pack');?></label>
                    <p class="op-micro-copy"><?php _e('Choose an image to use as the video overlay image', 'optimizepress-plus-pack');?></p>
                    <?php op_upload_field('op[row][addon][video_background_overlay_image]'); ?>
                </div>
                <div class="op-le-row-options-column">
                    <div class="op-video-background-image-position-container">
                        <label class="op-video-background-subtitle"><?php _e('Video overlay image position', 'optimizepress-plus-pack');?></label>
                        <select class="op-video-background-image-position select" name="op[row][addon][video_select_background_image_position]">
                            <option value="tile">Tile</option>
                            <option value="cover">Cover</option>
                            <option value="stretch">Stretch</option>
                            <option value="center">Center</option>
                        </select>
                        <div class="op-hidden">
                            <?php op_text_field('op[row][addon][video_background_image_position]', ''); ?>
                        </div>
                    </div>
                </div>
            </div>

            <label class="op-video-background-subtitle"><?php _e('Alternative image for mobile devices ', 'optimizepress-plus-pack');?></label>
            <p class="op-micro-copy"><?php _e('Choose an image that will be shown on mobile devices instead of video', 'optimizepress-plus-pack');?></p>
            <?php op_upload_field('op[row][addon][video_background_alternative_image]'); ?>

            <label class="op-video-background-subtitle"><?php _e('Mute video', 'optimizepress-plus-pack');?></label>
            <?php op_checkbox_field('op[row][addon][video_background_mute]', 1); ?>

            <label class="op-video-background-subtitle"><?php _e('Full page video', 'optimizepress-plus-pack');?></label>
            <p class="op-micro-copy op-video-background-micro-copy"><?php _e('Make this video stretch to the whole page instead to this row', 'optimizepress-plus-pack');?></p>
            <?php op_checkbox_field('op[row][addon][video_background_fullpage]', 1); ?>
        </div>
        <?php
    }

    /**
     * Renders markup for video element
     * @param  stdClass $options
     * @return string
     */
    public function renderVideoMarkup($options)
    {
        /*
         * We need to remove dummy filter that we set to avoid object to string conversion error
         */
        remove_filter('op_inside_row', '__return_empty_string', 111);

        /*
         * We are not showing video in LE, only simple img preview
         */
        if (defined('OP_LIVEEDITOR')) {


            // If OP_SCRIPT_DEBUG is .min we use the opplus-back-all.min.js where this script is concatenated
            if (OP_SCRIPT_DEBUG === '') {
                wp_enqueue_style('video-background', OPPP_BASE_URL . 'css/elements/video_background_admin' . OP_SCRIPT_DEBUG . '.css', array(), OPPP_VERSION, 'all');

                wp_enqueue_script('video-background', OPPP_BASE_URL . 'js/elements/video_background_admin' . OP_SCRIPT_DEBUG . '.js', array(OP_SN . '-noconflict-js'), OPPP_VERSION, true);
            }

            $video_type_set = isset($options->addon->video_background_type) && !empty($options->addon->video_background_type);
            $youtube_url_set = isset($options->addon->video_background_type) && $options->addon->video_background_type == 'youtube' && !empty($options->addon->video_background_youtube);
            $html5_url_set = isset($options->addon->video_background_type) && $options->addon->video_background_type == 'url' && (!empty($options->addon->video_background_url_mp4) || !empty($options->addon->video_background_url_webm) || !empty($options->addon->video_background_url_ogv));

            if ($video_type_set && ($youtube_url_set || $html5_url_set)) {
                return '<div class="op-row-video-background-wrap-preview"></div>';
            }

            return '';
        }

        /* Stop rendering of element if row doesn't have video background selected */
        if (!$this->checkIfBackgroundVideoExistInRow($options)){
            return "";
        }

        /*
         * Cache busting
         */
        if (function_exists('wp_using_ext_object_cache')) {
            $extCache = wp_using_ext_object_cache();
            if (true === $extCache) {
                wp_using_ext_object_cache(false);
            }
        }

        $uid = 'vbg_' .md5(serialize($options));

        if (false === $data = get_transient('el_' . $uid)) {

            $data = op_sl_parse('video_background', $options->addon);

            if (false === is_array($data) && empty($data)) {
                return;
            }

            set_transient('el_' . $uid, $data, OP_SL_ELEMENT_CACHE_LIFETIME);
        }

        if (OP_SCRIPT_DEBUG === '') {
            wp_enqueue_script(OP_SN . '-video-background', OPPP_BASE_URL . 'js/elements/init_video_background' . OP_SCRIPT_DEBUG . '.js', array(OP_SN . '-noconflict-js'), OPPP_VERSION, true);

            wp_enqueue_style(OP_SN . '-video-background', OPPP_BASE_URL . 'css/elements/video_background' . OP_SCRIPT_DEBUG . '.css', array(), OPPP_VERSION, 'all');
        }


        if (isset($data['enqueue_youtube_api']) && $data['enqueue_youtube_api'] == 1) {
            if (OP_SCRIPT_DEBUG === '') {
                wp_enqueue_script(OP_SN . '-youtube-api', '//www.youtube.com/iframe_api', array(OP_SN . '-noconflict-js'), OPPP_VERSION, true);
            } else {
                wp_enqueue_script(OP_SN . '-youtube-api', '//www.youtube.com/iframe_api', array(OP_SN . 'plus-pack-js-front-all'), OPPP_VERSION, true);
            }
        }

        /*
         * Cache busting
         */
        if (function_exists('wp_using_ext_object_cache')) {
            wp_using_ext_object_cache($extCache);
        }

        if (isset($data['markup']) && !empty($data['markup'])) {
            return $data['markup'];
        } else {
             return '';
        }
    }

    /**
     * Add element key and name to cacheable elements list.
     * Filter is read in OP Helper Tools plugin and uses it to compile a list of elements whose transient cache can be cleared.
     * @param array $elements
     * @return array
     */
    public function addToCacheableElementsList($elements)
    {
        $elements['el_vbg_'] = __('Video Background', 'optimizepress-plus-pack');

        return $elements;
    }

    /**
     * Checks if user selected video background for row and returns true if has
     * Object should have at least property video_background_youtube or non empty value of mp4, webm & ogv
     *
     * @param $options object
     * @return bool
     */
    protected function checkIfBackgroundVideoExistInRow($options)
    {
        if ( ! isset($options) || ! isset($options->addon)) {
            return false;
        }

        if ( isset($options->addon->video_background_youtube) || !empty($options->addon->video_background_youtube)) {
            return true;
        }

        if (( ! isset($options->addon->video_background_url_mp4) || empty($options->addon->video_background_url_mp4))
            && ( ! isset($options->addon->video_background_url_webm) || empty($options->addon->video_background_url_webm))
            && ( ! isset($options->addon->video_background_url_ogv) || empty($options->addon->video_background_url_ogv)) ) {
            return false;
        }

        return true;
    }
}

new OptimizePress_Elements_VideoBackground();
