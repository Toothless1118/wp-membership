(function ($) {
  // this identifies your website in the createToken call below
  Stripe.setPublishableKey(MeprStripeGateway.public_key);

  $(document).ready(function() {
    if ($("#df_checkout_flag").val() == '0'){
      $(".add-new-card-section").css('display', 'none');
    }
    if($("#current_card").val() != "add-new-card") {
      $("#df_checkout_flag").val('0');
    }
    $("#current_card").on('change', function(){
      if($(this).val() == "add-new-card") {
        $('.card-number').prop('disabled', false);
        $('.card-number').val('');
        $('.cc-exp').prop('disabled', false);
        $('.cc-exp').val('');
        $('.card-cvc').prop('disabled', false);
        $('.card-cvc').val('');
        $("#df_checkout_flag").val('1');
        $(".add-new-card-section").css('display', 'flex');
      } else {
        $("#df_checkout_flag").val('0');
        $(".add-new-card-section").css('display', 'none');
      }
      // if ($("#df_checkout_flag").val() == '0'){
      //   $('.card-number').prop('disabled', false);
      //   $('.card-number').val('');
      //   $('.cc-exp').prop('disabled', false);
      //   $('.cc-exp').val('');
      //   $('.card-cvc').prop('disabled', false);
      //   $('.card-cvc').val('');
      //   $("#df_checkout_flag").val('1');
      //   $(".add-new-card-section").css('display', 'flex');
      //   //$("#current_card").prop('disabled', true);
      // } else {
      //   $("#df_checkout_flag").val('0');
      //   $(".add-new-card-section").css('display', 'none');
      //   //$("#current_card").prop('disabled', false);
      // }
    });
    $('.new-card').on('click', function(){
      if ($("#df_checkout_flag").val() == '0'){
        $('.card-number').prop('disabled', false);
        $('.card-number').val('');
        $('.cc-exp').prop('disabled', false);
        $('.cc-exp').val('');
        $('.card-cvc').prop('disabled', false);
        $('.card-cvc').val('');
        $("#df_checkout_flag").val('1');
        $(".add-new-card-section").css('display', 'flex');
        //$("#current_card").prop('disabled', true);
      } else {
        $("#df_checkout_flag").val('0');
        $(".add-new-card-section").css('display', 'none');
        //$("#current_card").prop('disabled', false);
      }
    });
    $('body').on('mepr-checkout-submit', function(e, payment_form) {
      e.preventDefault();
      if ($("#df_checkout_flag").val() == '1'){
        var exp = $(payment_form).find('.cc-exp').payment('cardExpiryVal');

        var tok_args = {
          name: $(payment_form).find('.card-name').val(),
          number: $(payment_form).find('.card-number').val(),
          cvc: $(payment_form).find('.card-cvc').val(),
          exp_month: exp.month,
          exp_year: exp.year
        };

        // Send address if it's there
        if( $(payment_form).find('.card-address-1').length != 0 ) { tok_args['address_line1'] = $(payment_form).find('.card-address-1').val(); }
        if( $(payment_form).find('.card-address-2').length != 0 ) { tok_args['address_line2'] = $(payment_form).find('.card-address-2').val(); }
        if( $(payment_form).find('.card-city').length != 0 ) { tok_args['address_city'] = $(payment_form).find('.card-city').val(); }
        if( $(payment_form).find('.card-state').length != 0 ) { tok_args['address_state'] = $(payment_form).find('.card-state').val(); }
        if( $(payment_form).find('.card-zip').length != 0 ) { tok_args['address_zip'] = $(payment_form).find('.card-zip').val(); }
        if( $(payment_form).find('.card-country').length != 0 ) { tok_args['address_country'] = $(payment_form).find('.card-country').val(); }

        // createToken returns immediately - the supplied callback submits the form if there are no errors
        Stripe.createToken( tok_args, function(status, response) {
          //console.info('message', response);
          if(response.error) {
            // re-enable the submit button
            $(payment_form).find('.mepr-submit').prop('disabled', false);
            // show the errors on the form
            $(payment_form).find('.mepr-stripe-errors').html(response.error.message);
            $(payment_form).find('.mepr-stripe-errors').addClass('mepr_error');
            // hide the spinning gif bro
            $(payment_form).find('.mepr-loading-gif').hide();
          } else {
            $(payment_form).find('.mepr-stripe-errors').removeClass('mepr_error');
            // token contains id, last4, and card type
            var token = response['id'];

            // Don't do anything if there's already a token, if it is
            // present chances are the form has already been submitted
            if(!$(payment_form).hasClass('mepr-payment-submitted')) {
              $(payment_form).addClass('mepr-payment-submitted');

              // insert the token into the form so it gets submitted to the server
              payment_form.append('<input type="hidden" class="mepr-stripe-token" name="stripe_token" value="' + token + '" />');

              // and submit
              payment_form.get(0).submit();
            }
          }
        });

        return false; // submit from callback
      } else {
        payment_form.get(0).submit();
        return false; // submit from callback
      }
    });
  });
})(jQuery);

