(function($) {

    $(document).ready(function(event) {
        _init();
    });

    /**
     * Initialize
     * @return {Void}
     */
    var _init = function() {
        $('body')
            .on('click', '.op-scroll-enhancer a', _click)
            .on('click', '.op-scroll-enhancer a', _dblclick);
    }

    /**
     * Animate scroll on click event
     * @param  {Object} event
     * @return {Boolean}
     */
    var _click = function(event) {
        var obj     = $(this),
            element = obj.attr('data-element') + '' || '',
            effect  = obj.attr('data-effect')  + '' || 'swing',
            speed   = obj.attr('data-speed')   * 1  || 800,
            padding = obj.attr('data-padding') * 1  || 0;

        if (effect == 'cancel') {
            return false;
        }

        if (effect == 'none') {
            effect = 'swing';
            speed  = 0;
        }

        element = decodeURIComponent(element);

        var scrollobj = $(false);
        if (scrollobj.length == 0) {
            try {
                scrollobj = $(element);
            }
            catch (err) {
                // pass
            }
        }
        if (scrollobj.length == 0) {
            try {
                scrollobj = $('[name="' + element + '"');
            }
            catch (err) {
                // pass
            }
        }
        if (scrollobj.length == 0) {
            try {
                scrollobj = $('#' + element);
            }
            catch (err) {
                // pass
            }
        }
        if (scrollobj.length == 0) {
            scrollobj = $(this).closest('.element-container').next();
        }
        if (scrollobj.length == 0) {
            scrollobj = $(this).closest('.element-container');
        }
        if (scrollobj.length == 0) {
            scrollobj = $(this).closest('.op-scroll-enhancer');
        }
        if (scrollobj.length == 0) {
            scrollobj = $(this);
        }

        if (scrollobj.length != 0) {
            $(scrollobj).first().animatescroll({
                easing: effect,
                scrollSpeed: speed,
                padding: padding
            });
        }

        return false;
    }

    /**
     * Disable double click event (do not open dialog in LE)
     * @param  {Object} event
     * @return {Boolean}
     */
    var _dblclick = function(event) {
        return false;
    }

})(opjq);
