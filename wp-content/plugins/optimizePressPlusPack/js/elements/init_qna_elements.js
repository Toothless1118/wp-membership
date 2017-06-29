(function ($) {
    window.OptimizePress.initQNAElements = function(){
        $( ".op-fancy-faq-wrap" ).each(function(index, element) {
            $(element).accordion({
                collapsible: true,
                heightStyle: "content",
                animate: 300,
            });
        });

        $( ".op-faq-s2-wrap" ).each(function(index, element) {
            $(element).accordion({
                active: false,
                collapsible: true,
                heightStyle: "content",
                animate: 300,
                header: '> div.faq-item > h3',
                beforeActivate: function( event, ui ) {
                    $(ui.newHeader).parent('.faq-item').toggleClass('ui-wrap-active');
                    $(ui.oldHeader).parent('.faq-item').toggleClass('ui-wrap-active');
                }
            });
        });

        $( ".op-faq-s3-wrap" ).each(function(index, element) {
            $(element).accordion({
                active: false,
                collapsible: true,
                heightStyle: "content",
                animate: 300,
                header: '> div.faq-item > h3',
                beforeActivate: function( event, ui ) {
                    $(ui.newHeader).find('.glyphicon').toggleClass('glyphicon-menu-down').toggleClass('glyphicon-remove').css({'color':'inherit'});
                    $(ui.oldHeader).find('.glyphicon').toggleClass('glyphicon-menu-down').toggleClass('glyphicon-remove').css({'color':'#e0e5e8'});
                    $(ui.newHeader).parent('.faq-item').toggleClass('ui-wrap-active');
                    $(ui.oldHeader).parent('.faq-item').toggleClass('ui-wrap-active');
                }
            });
        });

        $( ".op-faq-s4-wrap" ).each(function(index, element) {
            $(element).accordion({
                active: false,
                collapsible: true,
                heightStyle: "content",
                animate: 300,
                header: '> div.faq-item > h3',
                icons: { "header": "glyphicon glyphicon-menu-down", "activeHeader": "glyphicon glyphicon-menu-up" },
                beforeActivate: function( event, ui ) {
                    $(ui.newHeader).parent('.faq-item').toggleClass('ui-wrap-active');
                    $(ui.oldHeader).parent('.faq-item').toggleClass('ui-wrap-active');
                }
            });
        });

        $( ".op-faq-s5-wrap" ).each(function(index, element) {
            $(element).accordion({
                active: true,
                collapsible: true,
                heightStyle: "content",
                animate: 300,
                header: '> div.faq-item > h3',
                beforeActivate: function( event, ui ) {
                    $(ui.newHeader).find('.glyphicon').toggleClass('glyphicon-triangle-bottom').toggleClass('glyphicon-triangle-right');
                    $(ui.oldHeader).find('.glyphicon').toggleClass('glyphicon-triangle-bottom').toggleClass('glyphicon-triangle-right');
                    $(ui.newHeader).parent('.faq-item').toggleClass('ui-wrap-active');
                    $(ui.oldHeader).parent('.faq-item').toggleClass('ui-wrap-active');

                    // The accordion believes a panel is being opened
                    if (ui.newHeader[0]) {
                        var currHeader  = ui.newHeader;
                        var currContent = currHeader.next('.ui-accordion-content');
                        // The accordion believes a panel is being closed
                    } else {
                        var currHeader  = ui.oldHeader;
                        var currContent = currHeader.next('.ui-accordion-content');
                    }
                    // Since we've changed the default behavior, this detects the actual status
                    var isPanelSelected = currHeader.attr('aria-selected') == 'true';

                    // Toggle the panel's header
                    currHeader.toggleClass('ui-corner-all',isPanelSelected).toggleClass('accordion-header-active ui-state-active ui-corner-top',!isPanelSelected).attr('aria-selected',((!isPanelSelected).toString()));

                    // Toggle the panel's icon
                    currHeader.children('.ui-icon').toggleClass('ui-icon-triangle-1-e',isPanelSelected).toggleClass('ui-icon-triangle-1-s',!isPanelSelected);

                    // Toggle the panel's content
                    currContent.toggleClass('accordion-content-active',!isPanelSelected)
                    if (isPanelSelected) { currContent.slideUp(); }  else { currContent.slideDown(); }

                    return false; // Cancel the default action
                }
            });
        });
    }
    window.OptimizePress.initQNAElements();
} (opjq));