(function($) {
    $(document).ready(function() {
        var $forms = $('form.op-optin-validation');
        $forms.submit(function() {
            $.ajax({
                "url": OPPP.ajaxurl,
                "data": {
                    "action": "oppp-record-conversion",
                    "recordId": OPPP.recordId,
                    "opppNonce": OPPP.nonce
                },
                "type": "POST",
                "async": false
            }).always(function() {
                return true;
            });
        });
    });
}(opjq));