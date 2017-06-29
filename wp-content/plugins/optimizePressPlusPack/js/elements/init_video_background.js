(function ($) {

    if ($('.op-row-video-background-wrap').length > 0 || $('.op-row-video-background-template').length > 0) {

        var $rowVideoWraps;
        var loadAttempt = [];

        function resizeVideo() {

            $rowVideoWraps.each(function(i) {

                var $this = $(this);
                var isIframe = $this.find('iframe').length > 0;
                var $video = isIframe ? $this.find('iframe') : $this.find('video');
                var $videoOverlay = $this.find('.op-video-background-overlay');
                var containerHeight = $this.parents('.row').outerHeight();
                var containerWidth = $this.parents('.row').outerWidth();
                var originalWidth;
                var originalHeight;
                var aspectRatio;
                var adjustSize = 1;
                var videoTopPosition = 0;
                var verticalAlign = 'top';

                // @todo videobackground css not loaded on first insert into page
                $video.on('loadeddata', resizeVideo);

                // If fullpage video, set the height and width to the body size
                if ($this.hasClass('op-row-video-background-fullpage')) {
                    containerHeight = window.innerHeight;
                    containerWidth = window.innerWidth;
                }

                if (typeof loadAttempt[i] === 'undefined') {
                    loadAttempt[i] = 0;
                }

                if (isIframe) {

                    originalWidth = $video.attr('width') || 560;
                    originalHeight = $video.attr('height') || 315;

                } else {

                    // There's actually no video in this row
                    if (typeof $video[0] === 'undefined') {
                        return;
                    }

                    originalWidth = $video[0].videoWidth;
                    originalHeight = $video[0].videoHeight;


                    // If video isn't loaded, videoHeight and videoWidth are 0
                    if (!originalWidth || !originalHeight) {
                        return;
                    }
                }

                aspectRatio = originalHeight / originalWidth;

                $video.width(containerWidth);
                $video.height(containerWidth * aspectRatio);

                // If video doesn't take up the full row height, we adjust it
                if ($video.height() < containerHeight) {
                    adjustSize = containerHeight / $video.height();
                    $video.width($video.width() * adjustSize);
                    $video.height($video.height() * adjustSize);
                }

                // If video doesn't take up full row width, we adjust it
                if ($video.width() < containerWidth) {
                    adjustSize = containerWidth / $video.width();
                    $video.width($video.width() * adjustSize);
                    $video.height($video.height() * adjustSize);
                }

                if ($video.width() > containerWidth) {
                    $video.css({ 'margin-left': -(($video.width() - containerWidth) / 2) })
                } else {
                    $video.css({ 'margin-left': 0 });
                }

                $videoOverlay.width($video.width());
                $videoOverlay.height($video.height());

                // Video vertical alignment
                verticalAlign = $video.attr('data-vertical-align');

                if (verticalAlign === 'bottom') {
                    $video.css({ top: 'auto', bottom: 0 });
                } else if (verticalAlign === 'middle') {
                    videoTopPosition = ($video.parent().height() - $video.height()) / 2;
                    $video.css({ top: videoTopPosition + 'px' });
                } else {
                    $video.css({ top: 0, bottom: 'auto' });
                }

                // Video elemnets are by default transparent, so we show them only after they're loaded.
                $this.css({ opacity: 1 });

            });
        }

        /**
         * Initializes the resize event
         */
        function initVideoResizeHandler() {

            var resizeTimeout;

            $rowVideoWraps = $('.op-row-video-background-wrap');

            // This load event listener is no longer needed
            $(window).off('load', initVideoResizeHandler);

            resizeVideo();

            $(window).on('resize', function () {
                clearTimeout(resizeTimeout);
                resizeTimeout = setTimeout(resizeVideo, 60);
            });

        }

        $(document).ready(function () {

            $('.op-row-video-background-template').each(function () {
                $('body').append($(this).html());

                // Only the first bg is really shown
                return false;
            });

            // We're only loading video background on non-mobile devices
            if (navigator.userAgent.match(/(Android|iPod|iPhone|iPad|IEMobile|Opera Mini)/)) {
                $('.op-row-video-background-wrap').each(function () {
                    $(this).find('.op-video-background-overlay, .op-video-background, .op-video-background-iframe-container').remove();
                    $(this).css({ opacity: 1 }).find('.op-row-video-background-alternative-image').addClass('op-row-video-background-alternative-image--shown');
                });
            } else {
                $(window).on('load', initVideoResizeHandler);
            }

        });

    }

} (opjq));


/**
 * Youtube handling
 * This doesn't work if placed into anonymous function
 */
function opMuteYoutubeVideo(event) {
    event.target.mute();
};

function onYouTubeIframeAPIReady() {
    var $ = opjq;
    var videoTimeout;

    var initYTPlayer = function () {
        $('.op-video-background-iframe').each(function () {
            var $iframe = $(this);

            new YT.Player($iframe.attr('id'), {
                events: {
                    onStateChange: function (event) {

                        if ($iframe.hasClass('op-video-background-iframe-mute')) {
                            opMuteYoutubeVideo(event);
                        }

                        // When Youtube's loop is used, a brief black blink appears at the end of the video.
                        // To circumvent it, we manually seek the video to its start.
                        // Status 1 is play. Check is necessary to avoid werid cuts when buffering is happening
                        if (event.data === 1 ) {
                            videoTimeout = setTimeout(function () {
                                clearTimeout(videoTimeout);
                                event.target.seekTo(0);
                            }, (event.target.getDuration() - event.target.getCurrentTime()) * 1000);
                        } else {
                            clearTimeout(videoTimeout);
                        }

                        $iframe.parent().css({ opacity: 1 });
                    }
                }
            });
        });
    };

    var triggerInitYTPlayer = function () {
        if ($.isReady) {
            initYTPlayer();
        } else {
            setTimeout(function () {
                triggerInitYTPlayer();
            }, 200);
        }
    };

    triggerInitYTPlayer();

};