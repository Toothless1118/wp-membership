;(function ($) {

    function setComparisonTableStyleHeights() {

        var maxPriceHeight;
        var maxTitleHeight;
        var maxDescriptionHeight;
        var maxPackageDescriptionHeight;
        var maxHeaderHeight;
        var featuresLength;
        var i = 0;


        $('.op-comparison-table-style1, .op-comparison-table-style2, .op-comparison-table-style3, .op-comparison-table-style4, .op-comparison-table-style5').each(function () {

            maxTitleHeight = 0;
            maxPriceHeight = 0;
            maxVariableHeight = 0;
            maxDescriptionHeight = 0;
            maxHeaderHeight = 0;
            maxPackageDescriptionHeight = 0;

            // $(this).find('.op-comparison-table-features-column .op-comparison-table-feature')

            $(this).find('.op-comparison-table-features-column, .op-comparison-table-column').each(function () {

                // .feature-table,
                $(this).find('.op-comparison-table-title, .op-comparison-table-price, .op-comparison-table-variable, .op-comparison-table-description, .op-comparison-table-btn-container, .op-comparison-table-package-description').height('auto');

                $(this).find('.op-comparison-table-title').each(function () {
                    maxTitleHeight = $(this).height() > maxTitleHeight ? $(this).height() : maxTitleHeight;
                });

                $(this).find('.op-comparison-table-price').each(function () {
                    maxPriceHeight = $(this).height() > maxPriceHeight ? $(this).height() : maxPriceHeight;
                });

                $(this).find('.op-comparison-table-variable').each(function () {
                    maxVariableHeight = $(this).height() > maxVariableHeight ? $(this).height() : maxVariableHeight;
                });

                $(this).find('.op-comparison-table-description').each(function () {
                    maxDescriptionHeight = $(this).height() > maxDescriptionHeight ? $(this).height() : maxDescriptionHeight;
                });

                $(this).find('.op-comparison-table-package-description').each(function () {
                    maxPackageDescriptionHeight = $(this).height() > maxPackageDescriptionHeight ? $(this).height() : maxPackageDescriptionHeight;
                });

            });

            $(this).find('.op-comparison-table-title').height(maxTitleHeight);
            $(this).find('.op-comparison-table-price').height(maxPriceHeight);
            $(this).find('.op-comparison-table-variable').height(maxVariableHeight);
            $(this).find('.op-comparison-table-description').height(maxDescriptionHeight);
            $(this).find('.op-comparison-table-package-description').height(maxPackageDescriptionHeight);

            // $('.op-comparison-table-style1, .op-comparison-table-style2, .op-comparison-table-style3, .op-comparison-table-style4').each(function () {
                // $(this).find('.op-comparison-table-header')
                $(this).find('.op-comparison-table-header').height('auto');
                $(this).find('.op-comparison-table-header').each(function () {
                    maxHeaderHeight = $(this).height() > maxHeaderHeight ? $(this).height() : maxHeaderHeight;
                });
            // });

            $(this).find('.op-comparison-table-header').height(maxHeaderHeight);

            featuresLength = $(this).find('.op-comparison-table-features-column').find('.op-comparison-table-feature').length;
            featuresRow = [];

            $(this).find('.op-comparison-table-features-column, .op-comparison-table-column').each(function () {
                $(this).find('.op-comparison-table-feature').each(function (index) {
                    if (typeof featuresRow[index] === 'undefined') {
                        featuresRow[index] = 0;
                    }

                    if ($(this).height() > featuresRow[index]) {
                        featuresRow[index] = $(this).height();
                    }
                });
            });

            $(this).find('.op-comparison-table-features-column, .op-comparison-table-column').each(function () {
                $(this).find('.op-comparison-table-feature').each(function (index) {
                    $(this).height(featuresRow[index]);
                });
            });


        });
    }

    var pricingTimeout;

    setComparisonTableStyleHeights();

    $(window).on('resize', function () {
        clearTimeout(pricingTimeout);
        pricingTimeout = setTimeout(function () {
            setComparisonTableStyleHeights();
        }, 100);
    });

    // Expose the function for external use (when element is inserted into LE)
    // OptimizePress.setComparisonTableStyleHeights = setComparisonTableStyleHeights;

    $(document).on('op.afterLiveEditorParse',function(){
        // We wait to html to be rendered and shown (fadeout fast + fadein faset)
        setTimeout(function () {
            setComparisonTableStyleHeights();
        }, 401);
    });

    // When liveeditor is opened in fancybox, contents
    // are not loaded entirely before the
    // setComparisonTableStyleHeights
    // is called, so we've added
    // this event to ensure
    // heights are set
    // properly
    $(window).on('load', function () {
        setComparisonTableStyleHeights();
    });

    var allComparisonTables = $(".op-comparison-table");
    $.each(allComparisonTables, function(index, value){
        var fadeAttribute = value.parentElement.parentElement.getAttribute("data-fade");
        if (typeof fadeAttribute != "undefined" || fadeAttribute != '') {
            setTimeout(function () {
                setComparisonTableStyleHeights();
            }, 1000 * (parseInt(fadeAttribute) + 1));
        }
    });

}(opjq));