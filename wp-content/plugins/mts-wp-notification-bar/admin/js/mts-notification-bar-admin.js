(function( $ ) {
	'use strict';

	$(function() {

		// Tabs
		$(document).on('click', '.mtsnb-tabs-nav a', function(e){
			e.preventDefault();
			var $this = $(this),
				target = $this.attr('href');
			if ( !$this.hasClass('active') ) {
				$this.parent().parent().find('a.active').removeClass('active');
				$this.addClass('active');
				$this.parent().parent().next().children().siblings().removeClass('active');
				$this.parent().parent().next().find( target ).addClass('active');
				$this.parent().parent().prev().val( target.replace('#tab-',''));
			}
		});

		// Display conditions manager
		$(document).on('click', '.condition-checkbox', function(e){
			var $this = $(this),
				panel = '#'+$this.attr('id')+'-panel',
				disable = $this.data('disable');
			if ( !$this.hasClass('disabled') ) {
				if ( $this.hasClass('active') ) {
					$this.removeClass('active');
					$this.find('input').val('');
					$(panel).removeClass('active');
					if ( disable ) {
						$('#condition-'+disable).removeClass('disabled');
					}
				} else {
					$this.addClass('active');
					$(panel).addClass('active');
					$this.find('input').val('active');
					if ( disable ) {
						$('#condition-'+disable).addClass('disabled');
					}
				}
			}
		});

		// Checkbox toggles
		$(document).on('change', '.mtsnb-checkbox-toggle', function(e) {
			var $this = $(this),
				targetSelector = '[data-checkbox="'+$this.attr('id')+'"]';

			$( targetSelector ).toggleClass('active');
		});

		// Preview Bar Button
		$(document).on('click', '#preview-bar', function(e){
			e.preventDefault();
			$('.mtsnb').remove();
			$('body').css({ "padding-top": "0", "padding-bottom": "0" }).removeClass('has-mtsnb');
			var form_data = $('form#post').serialize();
			var data = {
                action: 'preview_bar',
                form_data: form_data,
            };

            $.post( ajaxurl, data, function(response) {

                if ( response ) {
                    $('body').prepend( response );
                }
            }).done(function(result){
            	$( document ).trigger('mtsnbPreviewLoaded');
            });

		});

		$( document ).on( 'mtsnbPreviewLoaded', function( event ) {

			var barHeight, mtsnbSlider = false, mtsnbSliderContainer, stageOuterHeight, newStageOuterHeight;
			if ( $('.mtsnb').length > 0 ) {
				barHeight = $('.mtsnb').outerHeight();
				var cssProperty =  $('.mtsnb').hasClass('mtsnb-bottom') ? 'padding-bottom' : 'padding-top';
				$('body').css(cssProperty, barHeight).addClass('has-mtsnb');

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
					//autoplayTimeout: 5000,
	    			//autoplayHoverPause: true,
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

			// Counter
			if ( $('.mtsnb-countdown-type').length > 0 ) {

				var coutTill = $('.mtsnb-clock-till').val();
				var clock = $('.mtsnb-clock').FlipClock( coutTill, {
					clockFace: 'DailyCounter',
					countdown: true
				});
			}
		});

		// Color option
		$('.mtsnb-color-picker').wpColorPicker();

		// Referral/Custom URL options
		$('select.mtsnb-multi-text').tagsinput({
			confirmKeys: [13, 32],
			trimValue: true
		});

		// Multi select options
		$('select.mtsnb-multi-select').select2();

		// Icon select options
		$('select.mtsnb-icon-select').select2({
	        formatResult: function(state) {
	            if (!state.id) return state.text; // optgroup
	            return '<i class="fa fa-' + state.id + '"></i>&nbsp;&nbsp;' + state.text;
	        },
	        formatSelection: function(state) {
	            if (!state.id) return state.text; // optgroup
	            return '<i class="fa fa-' + state.id + '"></i>&nbsp;&nbsp;' + state.text;
	        },
	        escapeMarkup: function(m) { return m; }
	    });

		// Select which shows/hides options based on its value
		function mtsnbShowHodeChildOptions( el ) {
	        var $this = $(el),
				tempValue = $this.val(),
				targetSelector = '[data-parent-select-id="'+$this.attr('id')+'"]',
				activeSelector = '[data-parent-select-value*="'+tempValue+'"]';

			$( targetSelector ).removeClass('active');

	        if ( tempValue && activeSelector ) {

	            $( targetSelector+activeSelector ).addClass('active');
	        }
	    }

	    $('.mtsnb-has-child-opt select').each(function() {
            mtsnbShowHodeChildOptions( $(this) );
        });

		$(document).on('change', '.mtsnb-has-child-opt select', function(e) {
			mtsnbShowHodeChildOptions( $(this) );
		});

		// Datepickers
		var mtsnbDateToday = new Date();
		$( '.mtsnb-datepicker' ).datepicker({
	        dateFormat: 'mm/dd/yy',
	        minDate: mtsnbDateToday,
	    });
	    $('.mtsnb-condition-datepicker').datepicker({
	        dateFormat: 'mm/dd/yy',
	    });
	    $('#ui-datepicker-div').wrap('<div class="mts-datepicker"></div>');

		// Timepicker
		$('.mtsnb-timepicker').timepicker();

		// Social icons sorter
		$('#mtsnb-social-div-tbody').sortable();
		$('#mtsnb-b-social-div-tbody').sortable();

		var x = $('#mtsnb-social-div-tbody tr').length;
		var xb = $('#mtsnb-b-social-div-tbody tr').length;

		$('#mtsnb-social-add-platform').click( function(e) {

			var social_type = '<select id="mtsnb_fields_social_type" name="mtsnb_fields[social]['+(x+1)+'][type]">';

			$.each( adminVars.social_icons, function( key, value ) {
				social_type += '<option value="' + key + '">' + value + '</option>';
			});

			social_type += '</select>';

			var social_url = '<input class="form-control" id="mtsnb_fields_social_url" name="mtsnb_fields[social]['+(x+1)+'][url]" type="text" value="" placeholder="http://example.com"/>';

			var remove = '<button class="mtsnb-social-remove-platform button"><i class="fa fa-close"></i> ' + adminVars.remove + '</button>';

			var move = '<i class="mtsnb-move fa fa-arrows"></i>';

			var new_platform = '<tr class="text-center"><td>' + move + '</td><td>' + social_type + '</td><td>' + social_url + '</td><td>' + remove + '</td></tr>';

			$('#mtsnb-social-div-tbody').append( new_platform );

			x++;

			return false;
		});

		$('#mtsnb-b-social-add-platform').click( function(e) {

			var social_type_b = '<select id="mtsnb_fields_b_social_type" name="mtsnb_fields[b_social]['+(xb+1)+'][type]">';

			$.each( adminVars.social_icons, function( key, value ) {
				social_type_b += '<option value="' + key + '">' + value + '</option>';
			});

			social_type_b += '</select>';

			var social_url_b = '<input class="form-control" id="mtsnb_fields_b_social_url" name="mtsnb_fields[b_social]['+(xb+1)+'][url]" type="text" value="" placeholder="http://example.com"/>';

			var remove_b = '<button class="mtsnb-social-remove-platform button"><i class="fa fa-close"></i> ' + adminVars.remove + '</button>';

			var move = '<i class="mtsnb-move fa fa-arrows"></i>';

			var new_platform_b = '<tr class="text-center"><td>' + move + '</td><td>' + social_type_b + '</td><td>' + social_url_b + '</td><td>' + remove_b + '</td></tr>';

			$('#mtsnb-b-social-div-tbody').append( new_platform_b );

			xb++;

			return false;
		});

		$('body').on('click', '.mtsnb-social-remove-platform', function(e){

			$(this).parent('td').parent('tr').remove();

			return false;
		});

		// Add UTM tag
		$(document).on( 'click', '.mtsnb-add-utm-tag', function(e) {

			e.preventDefault();

			var $this = $(this),
				$newItemLabelField = $this.parent().find('.mtsnb-add-utm-tag-input'),
				newItemLabel = $newItemLabelField.val(),
				$count = $this.parent().prev().children().length,
				optKey = 'utm';

			if ( '' === newItemLabel ) {

				$newItemLabelField.focus();

			} else {

				if ( $this.hasClass('notutm-button') ) optKey = 'notutm';

				var newItem = '<label class="mtsnb-utm-label"><span class="utm-text">'+newItemLabel+' = </span><input type="hidden" name="mtsnb_fields[conditions]['+optKey+'][tags]['+($count+1)+'][name]" id="mtsnb_fields_conditions_'+optKey+'_tags_'+($count+1)+'_name" value="'+newItemLabel+'" /><input type="text" name="mtsnb_fields[conditions]['+optKey+'][tags]['+($count+1)+'][value]" id="mtsnb_fields_conditions_'+optKey+'_tags_'+($count+1)+'_value" value="" /><span class="mtsnb-remove-utm-tag"><i class="fa fa-close"></i></span></label>';

				$this.parent().prev().append( newItem );
				$newItemLabelField.val('');
				$this.prop( "disabled", true );
			}
		});
		// Enable/Disable "Add" button
		$(document).on('keyup', '.mtsnb-add-utm-tag-input', function (event) {
			var $this = $(this),
				val = $this.val(),
				$btn = $this.parent().parent().find('.mtsnb-add-utm-tag');

			if ( '' === val ) {

				$btn.prop( "disabled", true );

			} else {

				$btn.prop( "disabled", false );
			}
		});
		// Remove UTM tag
		$(document).on( 'click', '.mtsnb-remove-utm-tag', function(e) {

			e.preventDefault();

			$(this).parent().remove();
		});

		$(document).on( 'click', '.mtsnb-enable-split-test', function(e) {

			e.preventDefault();

			var $this = $(this);

			if ( $this.hasClass('active') ) {
				$('.mtsnb-b-option').val('');
				$this.text(adminVars.enable_test).removeClass('active');
				$('#b-sub-tabs-wrap').removeClass('active');
				$('#mtsnb-test-stats-a').removeClass('active');
				$('.mtsnb-reset-split-test').removeClass('show');
				
			} else {
				$('.mtsnb-b-option').val('1');
				$this.text(adminVars.disable_test).addClass('active');
				$('#b-sub-tabs-wrap').addClass('active');
				$('#mtsnb-test-stats-a').addClass('active');
				$('.mtsnb-reset-split-test').addClass('show');
			}
		});

		$(document).on( 'click', '.mtsnb-reset-split-test', function(e) {

			e.preventDefault();

			var $this = $(this),
				bar_id = $this.attr('data-bar-id');

			$this.prop( "disabled", true );

			var data = {
				'action': 'mtsnb_reset_ab_stats',
				'bar_id': bar_id
			};
			
			$.post( ajaxurl, data, function( response ) {

				$('#mtsnb-test-stats-a').html( response );
				$('#mtsnb-test-stats-b').html( response );

				$this.prop( "disabled", false );
			});
		});
		

		
		$('#tab-newsletter, #tab-newsletter-b').each(function() {
			var $this = $(this),
				b_prefix = '';

			if ( $this.attr("id") == 'tab-newsletter-b' ) { b_prefix = 'b_'; }

			var $mtsnb_fields_MailChimp_api_key = $this.find('#mtsnb_fields_'+b_prefix+'MailChimp_api_key'),
				$mtsnb_fields_MailChimp_list = $this.find('#mtsnb_fields_'+b_prefix+'MailChimp_list'),
				$mtsnb_fields_aweber_code = $this.find('#mtsnb_fields_'+b_prefix+'aweber_code'),
				$mtsnb_fields_aweber_list = $this.find('#mtsnb_fields_'+b_prefix+'aweber_list'),
				$mtsnb_fields_aweber_consumer_key = $this.find('#mtsnb_fields_'+b_prefix+'aweber_consumer_key'),
				$mtsnb_fields_aweber_consumer_secret = $this.find('#mtsnb_fields_'+b_prefix+'aweber_consumer_secret'),
				$mtsnb_fields_aweber_access_key = $this.find('#mtsnb_fields_'+b_prefix+'aweber_access_key'),
				$mtsnb_fields_aweber_access_secret = $this.find('#mtsnb_fields_'+b_prefix+'aweber_access_secret'),
				$mtsnb_fields_getresponse_api_key = $this.find('#mtsnb_fields_'+b_prefix+'getresponse_api_key'),
				$mtsnb_fields_getresponse_campaign = $this.find('#mtsnb_fields_'+b_prefix+'getresponse_campaign'),
				$mtsnb_fields_campaignmonitor_api_key = $this.find('#mtsnb_fields_'+b_prefix+'campaignmonitor_api_key'),
				$mtsnb_fields_campaignmonitor_list = $this.find('#mtsnb_fields_'+b_prefix+'campaignmonitor_list'),
				$mtsnb_fields_campaignmonitor_client = $this.find('#mtsnb_fields_'+b_prefix+'campaignmonitor_client'),
				$mtsnb_fields_madmimi_api_key = $this.find('#mtsnb_fields_'+b_prefix+'madmimi_api_key'),
				$mtsnb_fields_madmimi_username = $this.find('#mtsnb_fields_'+b_prefix+'madmimi_username'),
				$mtsnb_fields_madmimi_list = $this.find('#mtsnb_fields_'+b_prefix+'madmimi_list');


            // Get all MailChimp Lists
			$mtsnb_fields_MailChimp_api_key.keyup(function(){

				// Do nothing if we are already retrieve the lists
				if ($('#mtsnb-MailChimp-get-lists-'+b_prefix+'spinner').length != 0) {
					return;
				}

				$('<i id="mtsnb-MailChimp-get-lists-'+b_prefix+'spinner" class="fa fa-spinner fa-spin"></i>').insertAfter($mtsnb_fields_MailChimp_list);

				var data = {
					'action': 'mtsnb_get_mailchimp_lists',
					'api_key': $mtsnb_fields_MailChimp_api_key.val()
				};
				
				$.post( ajaxurl, data, function(response) {
					$('#mtsnb-MailChimp-get-lists-'+b_prefix+'spinner').remove();
					$mtsnb_fields_MailChimp_list.html(response);
				});
			});

			// Get Aweber Lists
			$('.mtsnb-aweber-connect').click(function(){
				$mtsnb_fields_aweber_code.parent('div').parent('div').removeClass('hidden');
				$mtsnb_fields_aweber_code.removeClass('hidden');
			});

			$mtsnb_fields_aweber_code.keyup(function (){

				// Do nothing if we are already retrieve the lists
				if ($('#mtsnb-aweber-get-lists-'+b_prefix+'spinner').length != 0) {
					return;
				}

				// Do nothing if the user did not input a code
				if ($mtsnb_fields_aweber_code.val() == '') {
					return;
				}

				$mtsnb_fields_aweber_list.html('');

				$('<i id="mtsnb-aweber-get-lists-'+b_prefix+'spinner" class="fa fa-spinner fa-spin"></i>').insertAfter($mtsnb_fields_aweber_list);

				var data = {
					'action': 'mtsnb_get_aweber_lists',
					'consumer_key': $mtsnb_fields_aweber_consumer_key.val(),
					'consumer_secret': $mtsnb_fields_aweber_consumer_secret.val(),
					'access_key': $mtsnb_fields_aweber_access_key.val(),
					'access_secret': $mtsnb_fields_aweber_access_secret.val(),
					'code': $mtsnb_fields_aweber_code.val(),
				};
				
				$.post( ajaxurl, data, function(response) {

					response = $.parseJSON(response);

					$('#mtsnb-aweber-get-lists-'+b_prefix+'spinner').remove();
					$mtsnb_fields_aweber_list.html(response.html);
					$mtsnb_fields_aweber_consumer_key.val(response.consumer_key);
					$mtsnb_fields_aweber_consumer_secret.val(response.consumer_secret);
					$mtsnb_fields_aweber_access_key.val(response.access_key);
					$mtsnb_fields_aweber_access_secret.val(response.access_secret);

					if (response.consumer_key != '') {
						$mtsnb_fields_aweber_code.addClass('hidden');
					}

				});
			});

			// Get all Get Response Lists
			$mtsnb_fields_getresponse_api_key.keyup(function(){

				// Do nothing if we are already retrieve the lists
				if ($('#mtsnb-getresponse-get-lists-'+b_prefix+'spinner').length != 0) {
					return;
				}

				$('<i id="mtsnb-getresponse-get-lists-'+b_prefix+'spinner" class="fa fa-spinner fa-spin"></i>').insertAfter($mtsnb_fields_getresponse_campaign);

				var data = {
					'action': 'mtsnb_get_getresponse_lists',
					'api_key': $mtsnb_fields_getresponse_api_key.val()
				};
				
				$.post( ajaxurl, data, function(response) {
					$('#mtsnb-getresponse-get-lists-'+b_prefix+'spinner').remove();
					$mtsnb_fields_getresponse_campaign.html(response);
				});
			});

			// Get all Campaign Monitor Clients and Lists
			$mtsnb_fields_campaignmonitor_api_key.keyup(function(){

				// Do nothing if we are already retrieve the lists
				if ($('.mtsnb-campaignmonitor-get-lists-'+b_prefix+'spinner').length != 0) {
					return;
				}

				$('<i class="mtsnb-campaignmonitor-get-lists-'+b_prefix+'spinner fa fa-spinner fa-spin"></i>').insertAfter($mtsnb_fields_campaignmonitor_list);
				$('<i class="mtsnb-campaignmonitor-get-lists-'+b_prefix+'spinner fa fa-spinner fa-spin"></i>').insertAfter($mtsnb_fields_campaignmonitor_client);

				var data = {
					'action': 'mtsnb_get_campaignmonitor_lists',
					'api_key': $mtsnb_fields_campaignmonitor_api_key.val(),
				};
				
				$.post( ajaxurl, data, function(response) {

					response = $.parseJSON(response);

					$('.mtsnb-campaignmonitor-get-lists-'+b_prefix+'spinner').remove();
					$mtsnb_fields_campaignmonitor_client.html(response.clients);
					$mtsnb_fields_campaignmonitor_list.html(response.lists);
				});
			});

			// Update lists for Campaign Monitor
			$mtsnb_fields_campaignmonitor_client.change(function(){

				// Do nothing if we are already retrieve the lists
				if ($('.mtsnb-campaignmonitor-get-lists-'+b_prefix+'spinner').length != 0) {
					return;
				}

				$('<i class="mtsnb-campaignmonitor-get-lists-'+b_prefix+'spinner fa fa-spinner fa-spin"></i>').insertAfter($mtsnb_fields_campaignmonitor_list);

				var data = {
					'action': 'mtsnb_update_campaignmonitor_lists',
					'api_key': $mtsnb_fields_campaignmonitor_api_key.val(),
					'client_id': $(this).val(),
				};
				
				$.post( ajaxurl, data, function(response) {
					$('.mtsnb-campaignmonitor-get-lists-'+b_prefix+'spinner').remove();
					$mtsnb_fields_campaignmonitor_list.html(response);
				});
			});

			// Get all Mad Mimi Lists
			$mtsnb_fields_madmimi_api_key.keyup(function(){

				// Do nothing if we are already retrieve the lists
				if ($('#mtsnb-madmimi-get-lists-'+b_prefix+'spinner').length != 0) {
					return;
				}

				$('<i id="mtsnb-madmimi-get-lists-'+b_prefix+'spinner" class="fa fa-spinner fa-spin"></i>').insertAfter($mtsnb_fields_madmimi_list);

				var data = {
					'action': 'mtsnb_get_madmimi_lists',
					'api_key': $mtsnb_fields_madmimi_api_key.val(),
					'username': $mtsnb_fields_madmimi_username.val(),
					'list': $mtsnb_fields_madmimi_list.val()
				};
				
				$.post( ajaxurl, data, function(response) {
					$('#mtsnb-madmimi-get-lists-'+b_prefix+'spinner').remove();
					$mtsnb_fields_madmimi_list.html(response);
				});
			});

			// Async Functions
			setTimeout(function() {

				// MailChimp
				$('<i id="mtsnb-MailChimp-get-lists-'+b_prefix+'spinner" class="fa fa-spinner fa-spin"></i>').insertAfter($mtsnb_fields_MailChimp_list);

				var data = {
					'action': 'mtsnb_get_mailchimp_lists',
					'api_key': $mtsnb_fields_MailChimp_api_key.val(),
					'list': $mtsnb_fields_MailChimp_list.attr('data-list'),
				};
				
				$.post( ajaxurl, data, function(response) {
					$('#mtsnb-MailChimp-get-lists-'+b_prefix+'spinner').remove();
					$mtsnb_fields_MailChimp_list.html(response);
				});

				// Aweber
				$mtsnb_fields_aweber_list.html('');

				$('<i id="mtsnb-aweber-get-lists-'+b_prefix+'spinner" class="fa fa-spinner fa-spin"></i>').insertAfter($mtsnb_fields_aweber_list);

				var data = {
					'action': 'mtsnb_get_aweber_lists',
					'consumer_key': $mtsnb_fields_aweber_consumer_key.val(),
					'consumer_secret': $mtsnb_fields_aweber_consumer_secret.val(),
					'access_key': $mtsnb_fields_aweber_access_key.val(),
					'access_secret': $mtsnb_fields_aweber_access_secret.val(),
					'code': $mtsnb_fields_aweber_code.val(),
					'list': $mtsnb_fields_aweber_list.attr('data-list'),
				};
				
				$.post( ajaxurl, data, function(response) {

					response = $.parseJSON(response);

					$('#mtsnb-aweber-get-lists-'+b_prefix+'spinner').remove();
					$mtsnb_fields_aweber_list.html(response.html);

					$mtsnb_fields_aweber_consumer_key.val(response.consumer_key);
					$mtsnb_fields_aweber_consumer_secret.val(response.consumer_secret);
					$mtsnb_fields_aweber_access_key.val(response.access_key);
					$mtsnb_fields_aweber_access_secret.val(response.access_secret);

					if (response.consumer_key != '') {
						$mtsnb_fields_aweber_code.addClass('hidden');
					}

				});

				// Get Response
				$('<i id="mtsnb-getresponse-get-lists-'+b_prefix+'spinner" class="fa fa-spinner fa-spin"></i>').insertAfter($mtsnb_fields_getresponse_campaign);

				var data = {
					'action': 'mtsnb_get_getresponse_lists',
					'api_key': $mtsnb_fields_getresponse_api_key.val(),
					'campaign': $mtsnb_fields_getresponse_campaign.attr('data-list')
				};
				
				$.post( ajaxurl, data, function(response) {
					$('#mtsnb-getresponse-get-lists-'+b_prefix+'spinner').remove();
					$mtsnb_fields_getresponse_campaign.html(response);
				});

				// Campaign Monitor
				$('<i class="mtsnb-campaignmonitor-get-lists-'+b_prefix+'spinner fa fa-spinner fa-spin"></i>').insertAfter($mtsnb_fields_campaignmonitor_list);
				$('<i class="mtsnb-campaignmonitor-get-lists-'+b_prefix+'spinner fa fa-spinner fa-spin"></i>').insertAfter($mtsnb_fields_campaignmonitor_client);

				var data = {
					'action': 'mtsnb_get_campaignmonitor_lists',
					'api_key': $mtsnb_fields_campaignmonitor_api_key.val(),
					'client': $mtsnb_fields_campaignmonitor_client.attr('data-client'),
					'list': $mtsnb_fields_campaignmonitor_list.attr('data-list'),
				};
				
				$.post( ajaxurl, data, function(response) {

					response = $.parseJSON(response);

					$('.mtsnb-campaignmonitor-get-lists-'+b_prefix+'spinner').remove();
					$mtsnb_fields_campaignmonitor_client.html(response.clients);
					$mtsnb_fields_campaignmonitor_list.html(response.lists);

				});

				// Mad Mimi
				$('<i id="mtsnb-madmimi-get-lists-'+b_prefix+'spinner" class="fa fa-spinner fa-spin"></i>').insertAfter($mtsnb_fields_madmimi_list);

				var data = {
					'action': 'mtsnb_get_madmimi_lists',
					'api_key': $mtsnb_fields_madmimi_api_key.val(),
					'username': $mtsnb_fields_madmimi_username.val(),
					'list': $mtsnb_fields_madmimi_list.attr('data-list')
				};
				
				$.post( ajaxurl, data, function(response) {
					$('#mtsnb-madmimi-get-lists-'+b_prefix+'spinner').remove();
					$mtsnb_fields_madmimi_list.html(response);
				});

			} ,1);

		});
		
		function format_colors_dropdown(state) {
			var palette = '<div class="single-palette"><table class="color-palette"><tbody><tr>';
			$.each($(state.element).data('colors'), function(index, val) {
				 palette += '<td style="background-color: '+val+'">&nbsp;</td>';
			});
			palette += '</tr></tbody></table></div>';
			return state.text + palette;
		}
		
		$('.mtsnb-colors-select').select2({
			placeholder: "Select predefined color set",
			allowClear: true,
			formatResult: format_colors_dropdown,
			escapeMarkup: function(m) { return m; },
			minimumResultsForSearch: 10
		}).change(function(event) {
			var $el = $(this).find(':selected');
			if (!$el.val()) return;
			
			var target = $el.data('target');
			$.each($el.data('colors'), function(index, val) {
				$('#'+target+'_'+index+'_color').iris('color', val);
			});
		});

		var slideraVal = $('.mtsnb-ab-slider-a-option').val();
		var sliderbVal = $('.mtsnb-ab-slider-b-option').val();

		$('.mtsnb-ab-slider').slider();

		$('.mtsnb-ab-slider-a').slider({
			value: slideraVal,
			min: 1,
			max: 99,
			slide: function(event, ui) {
				$('.mtsnb-a-slider-num').text(ui.value);
				$('.mtsnb-ab-slider-a-option').val(ui.value);

				$('.mtsnb-ab-slider-b').slider( "option", "value", 100 - ui.value );
				$('.mtsnb-b-slider-num').text( 100 - ui.value);
				$('.mtsnb-ab-slider-b-option').val( 100 - ui.value);
			}
		});

		$('.mtsnb-ab-slider-b').slider({
			value: sliderbVal,
			min: 1,
			max: 99,
			slide: function(event, ui) {
				$('.mtsnb-b-slider-num').text(ui.value);
				$('.mtsnb-ab-slider-b-option').val(ui.value);

				$('.mtsnb-ab-slider-a').slider( "option", "value", 100 - ui.value );
				$('.mtsnb-a-slider-num').text( 100 - ui.value);
				$('.mtsnb-ab-slider-a-option').val( 100 - ui.value);
			}
		});
	});

})( jQuery );
