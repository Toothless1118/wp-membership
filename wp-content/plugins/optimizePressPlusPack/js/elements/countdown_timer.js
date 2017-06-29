var op_asset_settings = (function($){
    var obj = null,
        date = new Date(),
        date_utc = new Date(date.getUTCFullYear(), date.getUTCMonth(), date.getUTCDate(),  date.getUTCHours(), date.getUTCMinutes(), date.getUTCSeconds()),
        currentTimezone = date_utc.getTimezoneOffset();
        currentTimezone = (currentTimezone/60) * -1,
        year = date_utc.getFullYear(),
        month = (date_utc.getMonth() + 1),
        day = date_utc.getDate(),
        hour = date_utc.getHours(),
        minute = date_utc.getMinutes(),
        second = date_utc.getSeconds(),
        /*month = ((date_utc.getMonth() + 1)<10 ? '0' + (date_utc.getMonth() + 1) : date_utc.getMonth() + 1),
        day = (date_utc.getDate() < 10 ? '0' + date_utc.getDate() : date_utc.getDate()),
        hour = (date_utc.getHours() < 10 ? '0' + date_utc.getHours() : date_utc.getHours()),
        minute = (date_utc.getMinutes() < 10 ? '0' + date_utc.getMinutes() : date_utc.getMinutes()),
        second = (date_utc.getSeconds() < 10 ? '0' + date_utc.getSeconds() : date_utc.getSeconds()),*/
        gmt = '',
        date_str = '';

        if (currentTimezone!==0) {
            /*var isNegative = (currentTimezone<0 ? true : false),
                tzPos = (isNegative ? currentTimezone*-1 : currentTimezone),
                prepend = (tzPos<10 ? '0' : '');

            gmt = (isNegative ? '-' : '+').toString() + prepend.toString() + tzPos.toString() + '00'.toString();*/
            gmt = currentTimezone > 0 ? '+' : '';
            gmt += currentTimezone;
        }

        date_str = year.toString() + '/' + month.toString() + '/' + day.toString() + ' ' + hour.toString() + ':' + minute.toString() + ':' + second.toString() + ' GMT ' + gmt.toString();

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
                microcopy: {
                    text: 'countdown_timer_instructions',
                    type: 'microcopy'
                },
                end_date: {
                    title: 'date',
                    default_value: date_str.toString()
                },
                microcopy2: {
                    text: 'redirect_url_instructions',
                    type: 'microcopy'
                },
                redirect_url: {
                    title: 'redirect_url',
                    default_value: ''
                }
            },
            step_3: {
                countdown_timer_advanced_instructions: {
                    text: 'countdown_timer_advanced_instructions',
                    type: 'microcopy'
                },
                years_text_singular: {
                    title: 'years_text_singular',
                    default_value: 'Year',
                    showOn: { field: 'step_1.style', value: [ -1 ] }
                },
                years_text: {
                    title: 'years_text',
                    default_value: 'Years',
                    showOn: { field: 'step_1.style', value: [ -1 ] }
                },
                months_text_singular: {
                    title: 'months_text_singular',
                    default_value: 'Month',
                    showOn: { field: 'step_1.style', value: [ -1 ] }
                },
                months_text: {
                    title: 'months_text',
                    default_value: 'Months',
                    showOn: { field: 'step_1.style', value: [ -1 ] }
                },
                weeks_text_singular: {
                    title: 'weeks_text_singular',
                    default_value: 'Week',
                    showOn: { field: 'step_1.style', value: [ -1 ] }
                },
                weeks_text: {
                    title: 'weeks_text',
                    default_value: 'Weeks',
                    showOn: { field: 'step_1.style', value: [ -1 ] }
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
                end_date = (attrs.end_date || ''),
                redirect_url = (attrs.redirect_url || ''),
                years_text_singular = attrs.years_text_singular,
                years_text = attrs.years_text,
                months_text_singular = attrs.months_text_singular,
                months_text = attrs.months_text,
                weeks_text_singular = attrs.weeks_text_singular,
                weeks_text = attrs.weeks_text,
                days_text_singular = attrs.days_text_singular,
                days_text = attrs.days_text,
                hours_text_singular = attrs.hours_text_singular,
                hours_text = attrs.hours_text,
                minutes_text_singular = attrs.minutes_text_singular,
                minutes_text = attrs.minutes_text,
                seconds_text_singular = attrs.seconds_text_singular,
                seconds_text = attrs.seconds_text,
                countdownTimerTimeout,
                countdownTimerLength;

            str = '[countdown_timer'
                + ' style="'                 + style                 + '"'
                + ' end_date="'              + end_date              + '"'
                + ' redirect_url="'          + redirect_url          + '"'
                + ' years_text="'            + years_text            + '"'
                + ' years_text_singular="'   + years_text_singular   + '"'
                + ' months_text="'           + months_text           + '"'
                + ' months_text_singular="'  + months_text_singular  + '"'
                + ' days_text="'             + days_text             + '"'
                + ' days_text_singular="'    + days_text_singular    + '"'
                + ' hours_text="'            + hours_text            + '"'
                + ' hours_text_singular="'   + hours_text_singular   + '"'
                + ' minutes_text="'          + minutes_text          + '"'
                + ' minutes_text_singular="' + minutes_text_singular + '"'
                + ' seconds_text="'          + seconds_text          + '"'
                + ' seconds_text_singular="' + seconds_text_singular + '"'
                + ']'
                + '[/countdown_timer]';

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
                        labels = [
                            obj.data('years_text')   === undefined ? 'Years'   : obj.data('months_text'),
                            obj.data('months_text')  === undefined ? 'Months'  : obj.data('months_text'),
                            obj.data('weeks_text')   === undefined ? 'Weeks'   : obj.data('weeks_text'),
                            obj.data('days_text')    === undefined ? 'Days'    : obj.data('days_text'),
                            obj.data('hours_text')   === undefined ? 'Hours'   : obj.data('hours_text'),
                            obj.data('minutes_text') === undefined ? 'Minutes' : obj.data('minutes_text'),
                            obj.data('seconds_text') === undefined ? 'Seconds' : obj.data('seconds_text')
                        ],
                        labels1 = [
                            obj.data('years_text_singular')   === undefined ? 'Year'   : obj.data('months_text_singular'),
                            obj.data('months_text_singular')  === undefined ? 'Month'  : obj.data('months_text_singular'),
                            obj.data('weeks_text_singular')   === undefined ? 'Week'   : obj.data('weeks_text_singular'),
                            obj.data('days_text_singular')    === undefined ? 'Day'    : obj.data('days_text_singular'),
                            obj.data('hours_text_singular')   === undefined ? 'Hour'   : obj.data('hours_text_singular'),
                            obj.data('minutes_text_singular') === undefined ? 'Minute' : obj.data('minutes_text_singular'),
                            obj.data('seconds_text_singular') === undefined ? 'Second' : obj.data('seconds_text_singular')
                        ],
                        format = obj.data('format') || 'yodhms',
                        width = 0,
                        widthOffset = 9;

                    for (var i = 0; i < labels.length; i++) {
                        if (labels[i].replace(/\s+/g, '') == '') {
                            labels[i] = '&nbsp;';
                        }
                    }
                    for (var i = 0; i < labels1.length; i++) {
                        if (labels1[i].replace(/\s+/g, '') == '') {
                            labels1[i] = '&nbsp;';
                        }
                    }

                    //Download the script if it isn't loaded and initiate countdown
                    $.loadScript(OptimizePress.paths.js + 'jquery/countdown' + OptimizePress.script_debug + '.js' + '?ver=' + OptimizePress.version, function(){
                        //Init countdown
                        obj.countdown({
                            until: newDateObj,
                            format: format,
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
            };

            setTimeout(countdownTimerTimeout, 1000);

        },
        customSettings: function(attrs,steps){
           attrs = attrs.attrs;
            var style = (attrs.style || 1),
                end_date = (attrs.end_date || ''),
                redirect_url = (attrs.redirect_url || ''),
                years_text_singular = attrs.years_text_singular,
                years_text = attrs.years_text,
                months_text_singular = attrs.months_text_singular,
                months_text = attrs.months_text,
                weeks_text_singular = attrs.weeks_text_singular,
                weeks_text = attrs.weeks_text,
                days_text_singular = attrs.days_text_singular,
                days_text = attrs.days_text,
                hours_text_singular = attrs.hours_text_singular,
                hours_text = attrs.hours_text,
                minutes_text_singular = attrs.minutes_text_singular,
                minutes_text = attrs.minutes_text,
                seconds_text_singular = attrs.seconds_text_singular,
                seconds_text = attrs.seconds_text;

            //Set the style
            OP_AB.set_selector_value('op_assets_core_countdown_timer_style_container', style);

            //Set the date
            $('#op_assets_core_countdown_timer_end_date').val(end_date);

            //Set redirect URL
            $('#op_assets_core_countdown_timer_redirect_url').val(redirect_url);

            //Set the default text for the date
            $('#op_assets_core_countdown_timer_years_text_singular').val(years_text_singular);
            $('#op_assets_core_countdown_timer_years_text').val(years_text);
            $('#op_assets_core_countdown_timer_months_text_singular').val(months_text_singular);
            $('#op_assets_core_countdown_timer_months_text').val(months_text);
            $('#op_assets_core_countdown_timer_weeks_text_singular').val(weeks_text_singular);
            $('#op_assets_core_countdown_timer_weeks_text').val(weeks_text);
            $('#op_assets_core_countdown_timer_days_text_singular').val(days_text_singular);
            $('#op_assets_core_countdown_timer_days_text').val(days_text);
            $('#op_assets_core_countdown_timer_hours_text_singular').val(hours_text_singular);
            $('#op_assets_core_countdown_timer_hours_text').val(hours_text);
            $('#op_assets_core_countdown_timer_minutes_text_singular').val(minutes_text_singular);
            $('#op_assets_core_countdown_timer_minutes_text').val(minutes_text);
            $('#op_assets_core_countdown_timer_seconds_text_singular').val(seconds_text_singular);
            $('#op_assets_core_countdown_timer_seconds_text').val(seconds_text);
        }
    };
}(opjq));
