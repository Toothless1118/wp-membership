opjq(document).ready(function ($) {

    $(window).on('op_row_addon_update', function (e, row) {

        var $row = $(row);
        var dataStyles = JSON.parse(atob($row.attr('data-style')));
        var addons = dataStyles.addon;

        $row.find('.op-row-video-background-wrap-preview').remove();
        if (addons && addons.video_background_type && addons.video_background_type !== '') {
            if ((addons.video_background_type == 'url' && (addons.video_background_url_mp4 != '' || addons.video_background_url_ogv != '' || addons.video_background_url_webm != '')) || (addons.video_background_type == 'youtube' && addons.video_background_youtube != '')) {
                    $row.prepend('<div class="op-row-video-background-wrap-preview"></div>');
            }
        }

    });

    $('body').on('change', '.op-video-background-type', function () {
        var $currentForm = $(this).parentsUntil('.op-row-video-background-form').parent();

        // Set the hidden textarea to current value
        $(this).next().find('input').val($(this).val());

        $currentForm.find('.op-video-background-type--shown').removeClass('op-video-background-type--shown');
        $currentForm.find('.op-video-background-type-' + $(this).val()).addClass('op-video-background-type--shown');

        return false;
    });

    $('body').on('change', '.op-video-background-image-position', function () {

        // Set the hidden textarea to current value
        $(this).next().find('input').val($(this).val());

        return false;
    });

    $('body').on('change', '.op-video-vertical-align', function () {

        // Set the hidden textarea to current value
        $(this).next().find('input').val($(this).val());

        return false;
    });

    $('body').on('click', '.edit-row', function () {
        var $backgroundVideoType = $('.op-video-background-type');
        var initialVideoType = $backgroundVideoType.next().find('input').val();
        var $backgroundImagePosition = $('.op-video-background-image-position');
        var initialBackgroundImagePosition = $backgroundImagePosition.next().find('input').val();
        var $verticalAlign = $('.op-video-vertical-align');
        var initialVerticalAlign = $verticalAlign.next().find('input').val();

        $('.op-video-background-type--shown').removeClass('op-video-background-type--shown');
        $backgroundVideoType.val('Select Video Type');

        if (initialVideoType) {
            $backgroundVideoType.val(initialVideoType);
            $('.op-video-background-type-' + initialVideoType).addClass('op-video-background-type--shown');
        }

        if (initialBackgroundImagePosition) {
            $backgroundImagePosition.val(initialBackgroundImagePosition);
        } else {
            $backgroundImagePosition.val('tile');
        }

        if (initialVerticalAlign) {
            $verticalAlign.val(initialVerticalAlign);
        } else {
            $verticalAlign.val('top');
        }
    });

});