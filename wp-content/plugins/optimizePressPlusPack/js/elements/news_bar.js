var op_asset_settings = (function($){
    return {
        help_vids: {
            step_1: {
                url: 'http://op2-inapp.s3.amazonaws.com/elements-news-bar.mp4',
                width: '600',
                height: '341'
            },
            step_2: {
                url: 'http://op2-inapp.s3.amazonaws.com/elements-news-bar.mp4',
                width: '600',
                height: '341'
            }
        },
        attributes: {
            step_1: {
                style: {
                    type: 'style-selector',
                    folder: 'previews',
                    addClass: 'op-disable-selected'
                }
            },
            step_2: {
                feature_text: {
                    title: 'feature_text',
                    type: 'input',
                    showOn: { field: 'step_1.style', value: ['1','2','6','7'] }
                },
                color: {
                    title: 'color',
                    type: 'color',
                    default_value: '#004a80'
                },
                feature_font_color: {
                    title: 'feature_font_color',
                    type: 'color',
                    default_value: '#ffffff',
                    addClass: 'end-row'
                },
                main_text: {
                    title: 'main_text',
                    type: 'input'
                },
                main_background: {
                    title: 'main_background',
                    type: 'color',
                    default_value: '#f2f2f2'
                },
                main_font_color: {
                    title: 'main_font_color',
                    type: 'color',
                    default_value: '#444444',
                    addClass: 'end-row'
                }
            },
            step_3: {
                microcopy: {
                    text: 'news_bar_instructions',
                    type: 'microcopy'
                },
                feature_url: {
                    title: 'feature_url',
                    type: 'input'
                },
                feature_width: {
                    title: 'feature_width',
                    type: 'input',
                    default_value: 'auto'
                },
                feature_position: {
                    title: 'feature_position',
                    type: 'select',
                    values: { 'left': 'feature_position_left', 'right': 'feature_position_right' },
                    default_value: 'left'
                }
            }
        },
        insert_steps: {2:{next:'advanced_options'},3:true},
        customInsert: function(attrs){
            var str = '', style = (attrs.style || 1);

            //Generate the parent shortcode
            str = '[news_bar'
                + ' style="'              + style                         + '"'
                + ' color="'              + attrs.color                   + '"'
                + ' feature_font_color="' + attrs.feature_font_color      + '"'
                + ' feature_text="'       + encodeURI(attrs.feature_text) + '"'
                + ' feature_url="'        + encodeURI(attrs.feature_url)  + '"'
                + ' feature_width="'      + attrs.feature_width           + '"'
                + ' feature_position="'   + attrs.feature_position        + '"'
                + ' main_background="'    + attrs.main_background         + '"'
                + ' main_font_color="'    + attrs.main_font_color         + '"'
                + ' main_text="'          + encodeURI(attrs.main_text)    + '"'
                + ']'
                + '[/news_bar]';

            //Insert the shortcode into the page (processed by default.php)
            OP_AB.insert_content(str);
            $.fancybox.close();
        },
        customSettings: function(attrs,steps){
            var html = '',
                style = attrs.attrs.style || 1,
                color = attrs.attrs.color,
                feature_font_color = attrs.attrs.feature_font_color
                feature_text = decodeURI(attrs.attrs.feature_text),
                feature_url = decodeURI(attrs.attrs.feature_url),
                feature_width = attrs.attrs.feature_width,
                feature_position = attrs.attrs.feature_position,
                main_background = attrs.attrs.main_background,
                main_font_color = attrs.attrs.main_font_color,
                main_text = decodeURI(attrs.attrs.main_text);

            //Set the style
            OP_AB.set_selector_value('op_assets_core_news_bar_style_container',(style || ''));

            //Set text fields
            steps[1].find('.field-color').find('input').val(color).next('a').css({ backgroundColor: color });
            steps[1].find('.field-feature_font_color').find('input').val(feature_font_color).next('a').css({ backgroundColor: feature_font_color });
            steps[1].find('.field-feature_text').find('input').val(feature_text);
            steps[1].find('.field-main_background').find('input').val(main_background).next('a').css({ backgroundColor: main_background });
            steps[1].find('.field-main_font_color').find('input').val(main_font_color).next('a').css({ backgroundColor: main_font_color });
            steps[1].find('.field-main_text').find('input').val(main_text);
            steps[2].find('.field-feature_url').find('input').val(feature_url);
            steps[2].find('.field-feature_width').find('input').val(feature_width);
            steps[2].find('.field-feature_position').find('select').val(feature_position);
        }
    };
}(opjq));
