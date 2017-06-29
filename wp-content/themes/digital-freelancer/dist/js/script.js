(function($) {
  $(document).ready(function() {
    // remove validation of password confirm when user signup.
    $(".mepr-signup-form").on("submit", function(){
        $(".mepr-password-confirm").val($('.mepr-password').val());
    });

    // show modal once error occured
    if ($('#mepr_errors').length > 0)
        $('#mepr_errors').modal("show");
    if ($('#mepr_errors1').length > 0)
        $('#joinModal').modal("show");
    if ($('#mepr_errors2').length > 0)
        $('#joinPipelineModal').modal("show");
    // smooth animating when click read more button on the homepage
    $("#learn-more").on("click", function(){
        $('html, body').animate({
            scrollTop: $("#sales-letter-section").offset().top
        }, 1000);
    });
    $("#pipeline-learn-more").on("click", function(){
        $('html, body').animate({
            scrollTop: $("#feature-block").offset().top
        }, 1000);
    });
    $("#pipeline-header-features").on("click", function(){
        $('html, body').animate({
            scrollTop: $("#pipeline-features").offset().top
        }, 1000);
    });
    $("#pipeline-header-who").on("click", function(){
        $('html, body').animate({
            scrollTop: $("#pipeline-how-it-works").offset().top
        }, 1000);
    });
    $("#pipeline-header-testimonials").on("click", function(){
        $('html, body').animate({
            scrollTop: $("#pipeline-testimonials").offset().top
        }, 1000);
    });
    $("#pipeline-header-pricing").on("click", function(){
        $('html, body').animate({
            scrollTop: $("#pipeline-price-group").offset().top
        }, 1000);
    });
    // carousel on the freelance chat landing page
    $('.carousel-item').first().addClass('active');
    $('.carousel').carousel({
        interval: 4000
    });
    //$('.carousel').carousel('next');
    //$('.carousel').carousel('prev');
    $('.register-carousel').carousel({
        interval: false
    });

    if($('.mepr-signup-form .carousel-item').first().hasClass('active')){
        $('.first-step').addClass('active-step');
        $('.user-icon').addClass('active-icon');
        $('.second-step').removeClass('active-step');
        $('.card-icon').removeClass('active-icon');
    }else{
        $('.first-step').removeClass('active-step');
        $('.user-icon').removeClass('active-icon');
        $('.second-step').addClass('active-step');
        $('.card-icon').addClass('active-icon');
    }

    $('.chat-next-btn').on('click', function(){
        // posts = $.ajax({
        //     type: 'GET',
        //     url: my_ajax_object.ajaxurl,
        //     async: false,
        //     dataType: 'json',
        //     data: {
        //         'action': 'df_signup_ajax_request',
        //         'username': 'root', 
        //         'password':'root',
        //         'email': 'sss@a.com',
        //         'username': 'efef',
        //         'first_name': 'ddd',
        //         'last_name': 'vvv'
        //     },
        //     done: function(results) {
        //         // uhm, maybe I don't even need this?
        //         JSON.parse(results);
        //         return results;
        //     },
        //     fail: function( jqXHR, textStatus, errorThrown ) {
        //         console.log( 'Could not get posts, server response: ' + textStatus + ': ' + errorThrown );
        //     }
        // }).responseJSON; // <-- this instead of .responseText
        $.ajax({
            url: my_ajax_object.ajax_url,
            async: false,
            data: {
                'action': 'df_signup_ajax_request',
                'username': 'root', 
                'password':'root',
                'email': 'sss@a.com',
                'username': 'efef',
                'first_name': 'ddd',
                'last_name': 'vvv'
            },
            method: 'POST',
            dataType: 'json',
            success: function(res) {
                console.log('success', res);
            },
            error: function(e, s, et){
                console.log('error', e);
                console.log('testStatus', s);
                console.log('errorThrown', et);
            }
        });
        // var ajax_url = wpApiSettings.root + 'mp/v1/members';
        // var username1 = 'root';
        // var password = 'root';
        // var params = {
        //             email: 'a@123.com',
        //             username: 'a@124.com',
        //             first_name: 'a',
        //             last_name: 'b'
        //         }
        // $.ajax({
        //     url : ajax_url,
        //     method: 'POST',
        //     data: params,
        //     beforeSend: function(xhr) {
        //         xhr.setRequestHeader('X-WP-Nonce', wpApiSettings.nonce); // cookie authentication
        //         //xhr.setRequestHeader('Authentication', 'Basic ' + btoa(username1 + ':' + password)); //basic Authentication
        //     },
        //     success: function(data, txtStatus, xhr) {
        //         console.log(data);
        //     },
        //     error: function(error) {
        //         console.log(error);
        //     }
        // });
    });

    // $('#modal-next-btn').on('click', function(){
    //     //$('#mepr_product_id').val('55'); // Basic free plan
    //     $('.first-step').removeClass('active-step');
    //     $('.user-icon').removeClass('active-icon');
    //     $('.second-step').addClass('active-step');
    //     $('.card-icon').addClass('active-icon');
    //     $('.register-carousel').carousel('next');
    // });
    /*
    $('.close').on('click', function(){
        $('.first-step').addClass('active-step');
        $('.user-icon').addClass('active-icon');
        $('.second-step').removeClass('active-step');
        $('.card-icon').removeClass('active-icon');
        $('.register-carousel').carousel(0);
    });
    */
    $('#img-4').css('opacity', '1');
    var vf = 0;
    $('#chatmembercarousel').on('slide.bs.carousel', function (ev) {
      // do something…
      var id = $('.active', ev.target).index();
      $('.userpic').each(function(){
        $(this).css('opacity', '.3');
      });

      $('#img-'+id).css('opacity', '1');

    });
    $('.chatmembercarouselprev').on('click', function(){
        $('#chatmembercarousel').carousel('pause');
        $('#chatmembercarousel').carousel('prev');
        $('#chatmembercarousel').carousel('cycle');
    });
    $('.chatmembercarouselnext').on('click', function(){
        $('#chatmembercarousel').carousel('pause');
        $('#chatmembercarousel').carousel('next');
        $('#chatmembercarousel').carousel('cycle');
    });
    $('.pipecarouselprev').on('click', function(){
        $('#pipelinecarousel').carousel('pause');
        $('#pipelinecarousel').carousel('prev');
        $('#pipelinecarousel').carousel('cycle');
    });
    $('.pipecarouselnext').on('click', function(){
        $('#pipelinecarousel').carousel('pause');
        $('#pipelinecarousel').carousel('next');
        $('#pipelinecarousel').carousel('cycle');
    });
    $('#carouselExampleControls').on('slide.bs.carousel', function (ev) {
      // do something…
      var id = $('.active', ev.target).index();
      $('.userpic').each(function(){
        $(this).css('opacity', '.3');
      });
      $('#img-'+id).css('opacity', '1');
    });

    // registraion pipeline 
    $('#reg_pipeline_light').on('click', function(){
        $('#mepr_product_id').val('139');
    });
    $('#reg_pipeline_pro').on('click', function(){
        $('#mepr_product_id').val('141');
    });
    //$('#reply-title').html('Leave a Comment ');

    // dorin's add remove enter press event
      $('.mepr-signup-form').keypress(function(e){
        if(e.which == 13){
          return false;
        }
      });
      /////////////
    $("#df_thankyou_modal").modal("show");
  });
})(jQuery);