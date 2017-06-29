var op_asset_settings = (function($){
    var obj = null;

    return {
        attributes: {
            step_1: {
                style: {
                    type: 'style-selector',
                    folder: 'previews',
                    addClass: 'op-disable-selected'
                }
            },
            step_2: {
                days: {
                    title: 'days',
                    default_value: '0',
                },
                hours: {
                    title: 'hours',
                    default_value: '23',
                },
                minutes: {
                    title: 'minutes',
                    default_value: '59',
                },
                seconds: {
                    title: 'seconds',
                    default_value: '59',
                },
                action: {
                    title: 'action_after_expiry',
                    type: 'select',
                    default_value: 'none',
                    values: {
                        'none': OP_AB.translate('none'),
                        'hide': OP_AB.translate('hide'),
                        'redirect': OP_AB.translate('redirect'),
                        'restart_repeat': OP_AB.translate('restart_repeat')
                    },
                    addClass: 'op-dialog-element-dropdown-fullwidth'
                },
                redirect_url: {
                    title: 'redirect_url',
                    default_value: '',
                    showOn: {
                        field: 'step_2.action',
                        value: 'redirect',
                        idprefix: 'op_assets_addon_op_evergreen_countdown_timer_'
                    }
                }
            },
            step_3: {
                countdown_timer_advanced_instructions: {
                    text: 'countdown_timer_advanced_instructions',
                    type: 'microcopy'
                },
                days_text_singular: {
                    title: 'days_text_singular',
                    default_value: 'Day'
                },
                days_text: {
                    title: 'days_text',
                    default_value: 'Days'
                },
                hours_text_singular: {
                    title: 'hours_text_singular',
                    default_value: 'Hour'
                },
                hours_text: {
                    title: 'hours_text',
                    default_value: 'Hours'
                },
                minutes_text_singular: {
                    title: 'minutes_text_singular',
                    default_value: 'Minute'
                },
                minutes_text: {
                    title: 'minutes_text',
                    default_value: 'Minutes'
                },
                seconds_text_singular: {
                    title: 'seconds_text_singular',
                    default_value: 'Second'
                },
                seconds_text: {
                    title: 'seconds_text',
                    default_value: 'Seconds'
                }
            }
        },
        insert_steps: {2:{next:'advanced_options'},3:true},
        customInsert: function(attrs){
            var str = '',
                style = (attrs.style || 1),
                days    = (attrs.days || 0),
                hours   = (attrs.hours || 0),
                minutes = (attrs.minutes || 0),
                seconds = (attrs.seconds || 0),
                days_text_singular = attrs.days_text_singular,
                days_text = attrs.days_text,
                hours_text_singular = attrs.hours_text_singular,
                hours_text = attrs.hours_text,
                minutes_text_singular = attrs.minutes_text_singular,
                minutes_text = attrs.minutes_text,
                seconds_text_singular = attrs.seconds_text_singular,
                seconds_text = attrs.seconds_text,
                action = (attrs.action || 'none'),
                redirect_url = (attrs.redirect_url || ''),
                countdownTimerTimeout,
                countdownTimerLength;

            str = '[op_evergreen_countdown_timer style="' + style + '" days="' + days + '" hours="' + hours + '" minutes="' + minutes + '" seconds="' + seconds + '" days_text="' + days_text + '" days_text_singular="' + days_text_singular + '" hours_text="' + hours_text + '" hours_text_singular="' + hours_text_singular + '" minutes_text="' + minutes_text + '" minutes_text_singular="' + minutes_text_singular + '" seconds_text="' + seconds_text + '" seconds_text_singular="' + seconds_text_singular + '" action="' + action + '" redirect_url="' + redirect_url + '"][/op_evergreen_countdown_timer]';

            //Number of countdown timers already on page
            countdownTimerLength = $('div.countdown-timer').length;

            OP_AB.insert_content(str);
            $.fancybox.close();

            //Init countdown
            countdownTimerTimeout = function(){
                //Find each timer instance
                if ($('div.countdown-timer').length === countdownTimerLength) {
                    setTimeout(countdownTimerTimeout, 1000);
                }
                $('div.countdown-timer').each(function(){
                    //Extract date and time
                    var obj = $(this),
                        data = obj.data('end').split(' '),
                        date = (typeof(data[0])=='undefined' ? '00/00/0000' : data[0].split('/')),
                        time = (typeof(data[1])=='undefined' ? '00:00:00' : data[1].split(':')),
                        isSince = (typeof(obj.data('end'))!='undefined' ? false : true),
                        newDateObj = new Date(date[0], parseInt(date[1])-1, date[2], time[0], time[1], time[2]),
                        labels = [obj.data('years_text'), obj.data('months_text'), 'Weeks', obj.data('days_text'), obj.data('hours_text'), obj.data('minutes_text'), obj.data('seconds_text')],
                        labels1 = [obj.data('years_text_singular'), obj.data('months_text_singular'), 'Week', obj.data('days_text_singular'), obj.data('hours_text_singular'), obj.data('minutes_text_singular'), obj.data('seconds_text_singular')],
                        width = 0,
                        widthOffset = 9;

                    //Download the script if it isn't loaded and initiate countdown
                    $.loadScript(OptimizePress.paths.js + 'jquery/countdown' + OptimizePress.script_debug + '.js' + '?ver=' + OptimizePress.version, function(){
                        //Init countdown
                        obj.countdown({
                            until: newDateObj,
                            format: 'yodhms',
                            labels: labels,
                            labels1: labels1,
                            'timezone': data[data.length-1]
                        });

                        //Get countdown sections and add each width to width variable
                        obj.find('span.countdown_section').each(function(){
                            width += $(this).width() + widthOffset;
                        });

                        //Set width to main obj
                        //obj.width(width + 'px');
                        obj.width('100%');
                    });
                });
            }

            setTimeout(countdownTimerTimeout, 1000);

        },
        customSettings: function(attrs,steps){
            attrs = attrs.attrs;
            // console.log(attrs);
            var style = (attrs.style || 1),
                days    = (attrs.days || 0),
                hours   = (attrs.hours || 23),
                minutes = (attrs.minutes || 59),
                seconds = (attrs.seconds || 59),
                days_text_singular = attrs.days_text_singular,
                days_text = attrs.days_text,
                hours_text_singular = attrs.hours_text_singular,
                hours_text = attrs.hours_text,
                minutes_text_singular = attrs.minutes_text_singular,
                minutes_text = attrs.minutes_text,
                seconds_text_singular = attrs.seconds_text_singular,
                seconds_text = attrs.seconds_text,
                action = (attrs.action || 'none'),
                redirect_url = (attrs.redirect_url || '');

            //Set the style
            OP_AB.set_selector_value('op_assets_addon_op_evergreen_countdown_timer_style_container', style);

            //Set time params
            $('#op_assets_addon_op_evergreen_countdown_timer_days').val(days);
            $('#op_assets_addon_op_evergreen_countdown_timer_hours').val(hours);
            $('#op_assets_addon_op_evergreen_countdown_timer_minutes').val(minutes);
            $('#op_assets_addon_op_evergreen_countdown_timer_seconds').val(seconds);

            //Set the default text for the date
            $('#op_assets_addon_op_evergreen_countdown_timer_days_text_singular').val(days_text_singular);
            $('#op_assets_addon_op_evergreen_countdown_timer_days_text').val(days_text);
            $('#op_assets_addon_op_evergreen_countdown_timer_hours_text_singular').val(hours_text_singular);
            $('#op_assets_addon_op_evergreen_countdown_timer_hours_text').val(hours_text);
            $('#op_assets_addon_op_evergreen_countdown_timer_minutes_text_singular').val(minutes_text_singular);
            $('#op_assets_addon_op_evergreen_countdown_timer_minutes_text').val(minutes_text);
            $('#op_assets_addon_op_evergreen_countdown_timer_seconds_text_singular').val(seconds_text_singular);
            $('#op_assets_addon_op_evergreen_countdown_timer_seconds_text').val(seconds_text);

            //Set action params
            $('#op_assets_addon_op_evergreen_countdown_timer_action').val(action).trigger('change');
            $('#op_assets_addon_op_evergreen_countdown_timer_redirect_url').val(redirect_url);
        }
    };
}(opjq));
