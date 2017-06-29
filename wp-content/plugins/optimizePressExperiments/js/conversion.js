(function($) {
    $(document).ready(function() {
        var $forms = $('form.op-optin-validation');
        OptimizePress._validationDeferreds = OptimizePress._validationDeferreds || [];

        /*
         * Record page conversion
         */
         function submitPageConversion(deferred) {
            $forms.submit(function() {
                op_show_loading();
                $.ajax({
                    "url":  OptimizePressStats.ajaxurl,
                    "type": "POST",
                    "data": {
                        "action": "optimizepress-stats-record-conversion",
                        "recordId": OptimizePressStats.recordId,
                        "optimizePressStatsNonce": OptimizePressStats.nonce
                    }
                }).always(function() {
                    deferred.resolve(true);
                });
                return false;
            });
         }

         OptimizePress._validationDeferreds.push(submitPageConversion);
    });
}(opjq));