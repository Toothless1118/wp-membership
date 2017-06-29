;(function ($) {

    function setPricingTableStyleHeights() {

        var maxPricingTableHeight;
        var maxNameHeight;
        var maxDescriptionHeight;
        var maxPriceHeight;
        var maxPricingDescriptionHeight;
        var maxFeatureDescriptionHeight;
        var maxFeaturesHeight;

        $('.pricing-table-style4, .pricing-table-style5, .pricing-table-style6, .pricing-table-style7, .pricing-table-style8').each(function () {

            maxPricingTableHeight = 0;
            maxNameHeight = 0;
            maxDescriptionHeight = 0;
            maxPriceHeight = 0;
            maxPricingDescriptionHeight = 0;
            maxFeatureDescriptionHeight = 0;
            maxFeaturesHeight = 0;

            $(this).find('.pricing-table-column').each(function () {

                // .feature-table,
                $(this).find('.price, .name, .description, .pricing-description, .feature-description').height('auto');

                $(this).find('.price').each(function () {
                    maxPriceHeight = $(this).height() > maxPriceHeight ? $(this).height() : maxPriceHeight;
                });

                $(this).find('.name').each(function () {
                    maxNameHeight = $(this).height() > maxNameHeight ? $(this).height() : maxNameHeight;
                });

                $(this).find('.description').each(function () {
                    maxDescriptionHeight = $(this).height() > maxDescriptionHeight ? $(this).height() : maxDescriptionHeight;
                });

                $(this).find('.pricing-description').each(function () {
                    maxPricingDescriptionHeight = $(this).height() > maxPricingDescriptionHeight ? $(this).height() : maxPricingDescriptionHeight;
                });

                $(this).find('.feature-description').each(function () {
                    maxFeatureDescriptionHeight = $(this).height() > maxFeatureDescriptionHeight ? $(this).height() : maxFeatureDescriptionHeight;
                });

                $(this).find('.features').each(function () {
                    maxFeaturesHeight = $(this).height() > maxFeaturesHeight ? $(this).height() : maxFeaturesHeight;
                });

                // $(this).find('.feature-table').each(function () {
                //     maxFeatureTableHeight = $(this).height() > maxFeatureTableHeight ? $(this).height() : maxFeatureTableHeight;
                // });

            });

            $(this).find('.price').height(maxPriceHeight);
            $(this).find('.name').height(maxNameHeight);
            $(this).find('.description').height(maxDescriptionHeight);
            $(this).find('.pricing-description').height(maxPricingDescriptionHeight);
            $(this).find('.feature-description').height(maxFeatureDescriptionHeight);
            $(this).find('.features').height(maxFeaturesHeight);
            // $(this).find('.feature-table').height(maxFeatureTableHeight);

        });
    }

    var pricingTimeout;

    setPricingTableStyleHeights();
    $(window).on('resize', function () {
        clearTimeout(pricingTimeout);
        pricingTimeout = setTimeout(function () {
            setPricingTableStyleHeights();
        }, 100);
    });

    // Expose the function for external use (when element is inserted into LE)
    // OptimizePress.setPricingTableStyleHeights = setPricingTableStyleHeights;

    $(document).on('op.afterLiveEditorParse',function(){
        // We wait to html to be rendered and shown (fadeout fast + fadein faset)
        setTimeout(function () {
            setPricingTableStyleHeights();
        }, 401);
    });

}(opjq));