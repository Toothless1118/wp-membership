<?php

namespace OpSl\Element;

use OpSl\Util\Element as Util;

class VideoBackground implements ElementInterface
{
    protected $atts;

    public function __construct($atts)
    {
        if (isset($atts['data'])) {
            $this->atts = $atts['data'];
        }
    }

    public function render()
    {
        $markup = '';
        $enqueue_youtube_api = 0;

        if (is_array($this->atts) && count($this->atts) > 0) {

            $videoOverlayStyle = '';

            if (isset($this->atts['video_background_overlay_color']) && !empty($this->atts['video_background_overlay_color'])) {
                $videoOverlayStyle .= 'background-color:' . $this->atts['video_background_overlay_color'] . ';';
            }

            if (isset($this->atts['video_background_overlay_opacity']) && !empty($this->atts['video_background_overlay_opacity'])) {
                $videoOverlayStyle .= 'opacity:' . $this->atts['video_background_overlay_opacity'] / 100 . ';';
            }

            if (isset($this->atts['video_background_overlay_image']) && !empty($this->atts['video_background_overlay_image'])) {
                $videoOverlayStyle .= 'background-image:  url(\'' . $this->atts['video_background_overlay_image'] . '\');';
            }

            if (isset($this->atts['video_background_image_position']) && !empty($this->atts['video_background_image_position'])) {
                switch ($this->atts['video_background_image_position']) {
                    case 'tile':
                        $videoOverlayStyle .= 'background-repeat: repeat;';
                        break;
                    case 'stretch':
                        $videoOverlayStyle .= 'background-size: 100% 100%; background-repeat: no-repeat;';
                        break;
                    case 'center':
                        $videoOverlayStyle .= 'background-position: center center; background-repeat: no-repeat;';
                        break;
                    case 'cover':
                        $videoOverlayStyle .= 'background-size: cover; background-position: center center;';
                        break;
                }
            }

            if ($videoOverlayStyle != '') {
                $videoOverlayStyle = ' style="' . $videoOverlayStyle . '" ';
            }

            $muted = '';
            $iframeMutedClass = '';
            $isMuted = false;

            if (isset($this->atts['video_background_mute']) && true === (bool) $this->atts['video_background_mute']) {
                $muted = ' muted="muted"';
                $iframeMutedClass = ' op-video-background-iframe-mute';
                $isMuted = true;
            }

            /**
             * Should this video stretch to the whole page?
             */
            $isFullpageBackground = false;
            $fullpageBackground = '';
            if (isset($this->atts['video_background_fullpage']) && true === (bool) $this->atts['video_background_fullpage']) {
                $isFullpageBackground = true;
                $fullpageBackground = ' op-row-video-background-fullpage';
                $markup .= '<script type="op-template" class="op-row-video-background-template">';
            }

            $markup .= '<div class="op-row-video-background-wrap' . $fullpageBackground . '">';
            $markup .= '<div class="op-video-background-overlay"' . $videoOverlayStyle . '></div>';

            if (isset($this->atts['video_background_alternative_image']) && !empty($this->atts['video_background_alternative_image'])) {
                $markup .= '<div class="op-row-video-background-alternative-image" style="background-image: url(\'' . $this->atts['video_background_alternative_image'] . '\');"></div>';
            }

            if (isset($this->atts['video_background_type']) && !empty($this->atts['video_background_type']) && $this->atts['video_background_type'] == 'youtube') {

                // Youtube
                if (isset($this->atts['video_background_youtube']) && !empty($this->atts['video_background_youtube'])) {
                    $videoID = Util::youtubeIdFromUrl($this->atts['video_background_youtube']);

                    if (!$videoID) {
                        return array(
                            'markup' => '',
                            'enqueue_youtube_api' => 0
                        );
                    }

                    $video_uid = 'op-videobgiframe-' . $videoID . '-' . time();

                    $markup .= '<div class="op-video-background-iframe-container">';
                        $markup .= '<iframe id="' . $video_uid . '" class="op-video-background-iframe ' . $iframeMutedClass . '" src="https://www.youtube.com/embed/' . $videoID . '?wmode=opaque&showinfo=0&autoplay=1&controls=0&modestbranding=1&loop=1&enablejsapi=1&playlist=' . $videoID . '"></iframe>';
                    $markup .= '</div>';

                    $enqueue_youtube_api = 1;

                }

            } else {

                // URL
                $markup .= '<video class="op-video-background" preload="auto" loop autoplay'. $muted . '>';

                    if (isset($this->atts['video_background_url_webm']) && !empty($this->atts['video_background_url_webm'])) {
                        $markup .= '<source type="video/webm" src="' . $this->atts['video_background_url_webm'] . '">';
                    }

                    if (isset($this->atts['video_background_url_mp4']) && !empty($this->atts['video_background_url_mp4'])) {
                        $markup .= '<source type="video/mp4" src="' . $this->atts['video_background_url_mp4'] . '">';
                    }

                    if (isset($this->atts['video_background_url_ogv']) && !empty($this->atts['video_background_url_ogv'])) {
                        $markup .= '<source type="video/ogg" src="' . $this->atts['video_background_url_ogv'] . '">';
                    }

                $markup .= '</video>';
            }

            $markup .= '</div>';

            if ($isFullpageBackground) {
                $markup .= '</script>';
            }

        }

        return array(
            'markup' => $markup,
            'enqueue_youtube_api' => $enqueue_youtube_api
        );
    }
}