(function( $ ) {

	'use strict';

	$(function() {

		var barHeight, mtsnbSlider = false, mtsnbSliderContainer, stageOuterHeight, newStageOuterHeight;

		// Show notification bar
		if ( $('.mtsnb').length > 0 ) {
			barHeight = $('.mtsnb').outerHeight();
			var cssProperty =  $('.mtsnb').hasClass('mtsnb-bottom') ? 'padding-bottom' : 'padding-top';
			if ( $('.mtsnb').hasClass('mtsnb-shown') ) {
				$('body').css(cssProperty, barHeight);
			}
			$('body').addClass('has-mtsnb');

			var mtsnbAnimation        = $('.mtsnb').attr('data-bar-animation');
			var mtsnbContentAnimation = $('.mtsnb').attr('data-bar-content-animation');
		
			if ( '' !== mtsnbAnimation ) {

				$('.mtsnb').removeClass('mtsnb-invisible').addClass( 'mtsnb-animated '+mtsnbAnimation );
			}
			if ( '' !== mtsnbContentAnimation ) {
				$('.mtsnb-content').addClass('mtsnb-content-hidden');
			}
			if ( '' !== mtsnbAnimation ) {
				$('.mtsnb').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function() {
					$('.mtsnb').removeClass( 'mtsnb-animated '+mtsnbAnimation );
					if ( '' !== mtsnbContentAnimation ) {
						$('.mtsnb-content').removeClass('mtsnb-content-hidden').addClass( 'mtsnb-animated '+mtsnbContentAnimation );
					}
				});
			} else {
				if ( '' !== mtsnbContentAnimation ) {
					$('.mtsnb-content').removeClass('mtsnb-content-hidden').addClass( 'mtsnb-animated '+mtsnbContentAnimation );
				}
			}
		}

		// Slider
	    if ( $('.mtsnb-slider').length > 0 ) {

	    	mtsnbSlider = $('.mtsnb-slider');
			mtsnbSliderContainer = mtsnbSlider.closest('.mtsnb-slider-container');
				
			mtsnbSlider.owlCarousel({
				items: 1,
				loop: true,
				nav: false,
				dots: false,
				onInitialized: function(){
					mtsnbSliderContainer.removeClass('loading');
					stageOuterHeight = parseInt( $('.owl-height').css('height'), 10 );
				},
				onChange: function(){
					stageOuterHeight = parseInt( $('.owl-height').css('height'), 10 );
				},
				autoplay: true,
				autoHeight: true,
				margin: 10,
			});

			mtsnbSlider.on('changed.owl.carousel', function(event) {
		        var currentIndex = event.item.index;
		        var newStageOuterHeight = mtsnbSlider.find('.owl-stage').children().eq( currentIndex ).height();
		        var cssProperty =  $('.mtsnb').hasClass('mtsnb-bottom') ? 'padding-bottom' : 'padding-top';
		        if ( $('.mtsnb').hasClass('mtsnb-shown') ) {
					$('body').css(cssProperty, parseInt( $('body').css(cssProperty) ) - stageOuterHeight + newStageOuterHeight );
		        } else {
		        	$('body').css(cssProperty, '0' );
		        }
		    });
		}

		// Hide Button
		$(document).on('click', '.mtsnb-hide', function(e) {

			e.preventDefault();

			var $this = $(this);
			var cssProperty =  $('.mtsnb').hasClass('mtsnb-bottom') ? 'padding-bottom' : 'padding-top';

			if ( !$this.hasClass('active') ) {
				$this.closest('.mtsnb').removeClass('mtsnb-shown').addClass('mtsnb-hidden');
				$('body').css(cssProperty, 0);
			}

			if ( mtsnbSlider ) {
				mtsnbSlider.trigger('stop.owl.autoplay');
			}

			var bar_id = $('.mtsnb').attr('data-mtsnb-id');
			if ( $('.mtsnb').hasClass('mtsnb-remember-state') ) {

				$.cookie('mtsnb_state_'+bar_id, 'closed', { path: '/' });

			} else {

				$.cookie('mtsnb_state_'+bar_id, '', { path: '/' });
			}
		});

		// Show Button
		$(document).on('click', '.mtsnb-show', function(e) {

			e.preventDefault();

			var $this = $(this);
			var cssProperty =  $('.mtsnb').hasClass('mtsnb-bottom') ? 'padding-bottom' : 'padding-top';
			if ( !$this.hasClass('active') ) {
				barHeight = $('.mtsnb').outerHeight();
				$this.closest('.mtsnb').removeClass('mtsnb-hidden').addClass('mtsnb-shown');
				$('body').css(cssProperty, barHeight);
				if ( $('.mtsnb').hasClass('mtsnb-bottom') && ( $(window).scrollTop() + $(window).height() == $(document).height() ) )  {
					$("html, body").animate({ scrollTop: $(window).scrollTop()+barHeight }, 300);
				}
			}

			if ( mtsnbSlider ) {
				setTimeout(function (){
					mtsnbSlider.trigger('play.owl.autoplay', [5000] );
				}, 5000);
			}

			var bar_id = $('.mtsnb').attr('data-mtsnb-id');
			if ( $('.mtsnb').hasClass('mtsnb-remember-state') ) {

				$.cookie('mtsnb_state_'+bar_id, 'opened', { path: '/' });

			} else {

				$.cookie('mtsnb_state_'+bar_id, '', { path: '/' });
			}
		});

		// Cookie - how many times user has seen specific bar
		if ( $('.mtsnb').length > 0 ) {

			$('.mtsnb').each(function() {
				var bar_id = $(this).attr('data-mtsnb-id');
				var mtsnbSeen = $.cookie('mtsnb_seen_'+bar_id);

				if ( !mtsnbSeen ) {

					$.cookie('mtsnb_seen_'+bar_id, '1', { expires: parseInt(mtsnb_data.cookies_expiry), path: '/' });

				} else {

					mtsnbSeen = parseInt( mtsnbSeen );
					$.cookie('mtsnb_seen_'+bar_id, ++mtsnbSeen, { expires: parseInt(mtsnb_data.cookies_expiry), path: '/' });
				}

				// Record Impression
				var ab_variation = $(this).find('.mtsnb-content').attr('data-mtsnb-variation');
				$.post( mtsnb_data.ajaxurl, {
					action: 'mtsnb_add_impression',
					bar_id: bar_id,
					ab_variation: ab_variation
				});
			});
		}

		// Cookie - show bar after x visits
		if ( $('.mtsnb-delayed').length > 0 ) {

			$('.mtsnb-delayed').each(function() {
				var bar_id = $(this).attr('data-mtsnb-id');
				var number = $(this).attr('data-mtsnb-after');
				var emtsnb  = $.cookie('mtsnb_'+bar_id+'_after');
					
				if ( !emtsnb ) {

					$.cookie('mtsnb_'+bar_id+'_after', number-1, { expires: parseInt(mtsnb_data.cookies_expiry), path: '/' });

				} else {

					emtsnb = parseInt( emtsnb );
					if ( 0 < emtsnb ) {
						$.cookie('mtsnb_'+bar_id+'_after', --emtsnb, { expires: parseInt(mtsnb_data.cookies_expiry), path: '/' });
					}
				}
			});
		}

		// Record Click
		$(document).on('click', '.mtsnb-container', function(event) {

			// Link or submit
			if ( $(event.target).closest('a').length || $(event.target).hasClass('mtsnb-submit') ) {

				var bar_id = $(event.target).closest('.mtsnb').attr('data-mtsnb-id'),
					ab_variation = $(event.target).closest('.mtsnb-content').attr('data-mtsnb-variation');

				$.post( mtsnb_data.ajaxurl, {
					action: 'mtsnb_add_click',
					bar_id: bar_id,
					ab_variation: ab_variation
				});
			}
		});

		// Video popup
		if ( $('.mtsnb-popup-type').length > 0 ) {

			$('.mtsnb-popup-youtube, .mtsnb-popup-vimeo').magnificPopup({
				disableOn: 700,
				type: 'iframe',
				mainClass: 'mfp-fade',
				removalDelay: 160,
				preloader: false,
				fixedContentPos: false
			});
		}

		// Email Signup Form
	    if ( $('#mtsnb-newsletter-type').length > 0 ) {

		    $('#mtsnb-newsletter').submit(function(event){

			    if ($('#mtsnb-newsletter-type').html() == 'aweber' ||
			    	$('#mtsnb-newsletter-type').html() == 'MailChimp' ||
			    	$('#mtsnb-newsletter-type').html() == 'getresponse' ||
			    	$('#mtsnb-newsletter-type').html() == 'campaignmonitor' ||
			    	$('#mtsnb-newsletter-type').html() == 'madmimi') {

				    event.preventDefault();

				    $('<i style="margin-left: 10px;" class="mtsnb-submit-spinner fa fa-spinner fa-spin"></i>').insertAfter('.mtsnb-submit');

					var data = {
						'action': 'mtsnb_add_email',
						'bar_id': $('.mtsnb').attr('data-mtsnb-id'),
						'type': $('#mtsnb-newsletter-type').html(),
						'email': $('#mtsnb-email').val(),
						'first_name': $('#mtsnb-first-name').val(),
						'last_name': $('#mtsnb-last-name').val(),
						'ab_variation': $(this).closest('.mtsnb-content').attr('data-mtsnb-variation'),
					};

					$.post(mtsnb_data.ajaxurl, data, function(response) {
						response = $.parseJSON(response);
						$('.mtsnb-submit-spinner').remove();
						$('.mtsnb-message').html('<i class="fa fa-' + response.status + '"></i> ' + response.message);
						$('.mtsnb-message').css('margin-top', '10px');
					});

			    }
			});
	    }

	    // Counter
	    if ( $('.mtsnb-countdown-type,.mtsnb-countdown-b-type').length > 0 ) {

	    	var coutTill = $('.mtsnb-clock-till').val();
		    var clock = $('.mtsnb-clock').FlipClock( coutTill, {
		    	clockFace: 'DailyCounter',
				countdown: true
			});
		}
	});

})( jQuery );
