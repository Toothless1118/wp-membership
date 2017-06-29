if (typeof OPAnimations !== 'undefined' && OPAnimations.elements) {
    var waypoints = OPAnimations.elements.map(function(item) {
        var $element = opjq('.' + item.selector);

        if(item.effect == 'bounceInDown'){
            // Stop the effect if user scrolls too far
            $element.waypoint({
                handler: function(direction) {
                    $element.removeClass('animated ' + item.effect);
                },
                offset: $element.height()
            });

            // Stop the effect if user scrolls back up, and the element is no longer visible
            $element.waypoint({
                handler: function(direction) {
                    $element.removeClass('animated ' + item.effect);
                },
                offset: window.innerHeight - $element.height()
            });
        }

        if(item.effect == 'bounceInUp'){
            $element.waypoint({
                handler: function(direction) {
                    $element.removeClass('animated ' + item.effect);
                },
                offset: -$element.height()
            });
        }

        // Based on scroll direction and
        // the height of the element in relation to height viewport
        // we determine when should the
        // animation be triggered.
        var offsetValue = '';
        if (item.direction === 'up') {
            offsetValue = ($element.height() > opjq(window).height()) === false ? 0 : -$element.height();
        } else {
            offsetValue = ($element.height() > opjq(window).height()) === false ? 'bottom-in-view' : 0;
        }

        $element.waypoint({
            handler: function(direction) {
                if (direction === item.direction) {
                    $element.addClass('animated ' + item.effect).removeClass('to-be-animated');
                }
            },
            offset: offsetValue
        });
    });
}