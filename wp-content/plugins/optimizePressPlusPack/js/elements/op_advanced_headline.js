var op_asset_settings = (function($){
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
                effect: {
                    title: 'effect',
                    type: 'select',
                    values: {'type': 'type','type_fast': 'type_fast', 'rotate-1': 'rotate_x_axis', 'rotate-2': 'letters_slide_from_top', 'rotate-3': 'letters_rotate_y_axis', 'loading-bar': 'loading_bar', 'slide': 'slide_from_top', 'clip': 'clip', 'zoom': 'zoom', 'scale': 'scale', 'push': 'push'},
                    default_value: 'type'
                },
                accent: {
                    title: 'accent',
                    type: 'color',
                    showOn: {
                        field: 'step_2.effect',
                        value: ['type']
                    },
                    default_value: '#000000'
                },
                content: {
                    title: 'static_text',
                    default_value: 'Your Static Header part'
                },
                parts: {
                    type: 'multirow',
                    link_suffix: 'animated_text',
                    multirow: {
                        attributes: {
                            content: {
                                title: 'Animated Text',
                                events: {
                                    keypress: function(e) {
                                        var k = e.keyCode || e.which;
                                        if (k == 13) {
                                            $(this).closest('.multirow-container').next().find('a').trigger('click');
                                        }
                                    }
                                }
                            }
                        },
                        onAdd: function(){
                            $('#op_assets_addon_op_advanced_headline_style_container').find('.op-asset-dropdown-list a.selected').trigger('click');
                        }
                    }
                },
                font: {
                    title: 'font_settings',
                    type: 'font'
                },
                align: {
                    title: 'alignment',
                    type: 'radio',
                    values: {'left':'left','center':'center','right':'right'},
                    default_value: 'center'
                },
                headline_tag: {
                    title: 'headline_tag',
                    type: 'select',
                    values: { 'h1': 'h1', 'h2': 'h2', 'h3': 'h3', 'h4': 'h4', 'h5': 'h5', 'h6': 'h6' },
                    default_value: 'h2'
                },
                line_height: {
                    title: 'line_height_px',
                    suffix: ''
                },
                highlight: {
                    title: 'highlight',
                    type: 'color',
                },
                top_margin: {
                    title: 'top_margin',
                    help: 'text_block_top_margin_help',
                    suffix: ''
                },
                bottom_margin: {
                    title: 'bottom_margin',
                    help: 'text_block_bottom_margin_help',
                    suffix: ''
                }
            }
        },
        insert_steps: {2:true},
        customInsert: function(attrs) {
            var content         = op_base64encode(attrs.content) || '',
                style           = attrs.style,
                accent          = attrs.accent,
                effect          = attrs.effect || 'type',
                align           = attrs.align || 'center',
                headline_tag    = attrs.headline_tag || 'h2',
                line_height     = attrs.line_height,
                highlight       = attrs.highlight,
                accent          = attrs.accent,
                top_margin      = attrs.top_margin || 0,
                bottom_margin   = attrs.bottom_margin || 0,
                attributes      = '',
                parts           = '';

            $.each(attrs.font, function(i,v) {
                if (v != '') {
                    attributes += ' font_' + i + '="' + v.replace(/"/ig,"'") + '"';
                }
            });

            $.each(attrs.parts, function(i, v) {
                if (v.content != '') {
                    parts += ' part_' + i + '="' + op_base64encode(v.content.replace(/"/ig, "'")) + '"';
                }
            });

            var output = '[op_advanced_headline style="' + style + '" effect="' + effect + '" align="' + align + '" accent="' + accent
                + '" headline_tag="' + headline_tag + '" line_height="' + line_height + '"' + ' highlight="' + highlight
                + '" top_margin="' + top_margin + '" bottom_margin="' + bottom_margin + '"' + attributes + parts + ']' + content + '[/op_advanced_headline]';

            // console.log('optput', output);
            OP_AB.insert_content(output);
            $.fancybox.close();
        },
        customSettings: function(attrs, steps) {
            var style           = attrs.attrs.style || 1,
                effect          = attrs.attrs.effect || 'type',
                align           = attrs.attrs.align || 'center',
                headline_tag    = attrs.attrs.headline_tag || 'h2',
                line_height     = attrs.attrs.line_height,
                highlight       = attrs.attrs.highlight,
                accent          = attrs.attrs.accent,
                top_margin      = attrs.attrs.top_margin || 0,
                bottom_margin   = attrs.attrs.bottom_margin || 0;

            var prefix          = 'op_assets_addon_op_advanced_headline';

            OP_AB.set_selector_value(prefix + '_style_container', attrs.attrs.style);
            OP_AB.set_font_settings('font', attrs.attrs, prefix + '_font');
            OP_AB.set_color_value(prefix + '_highlight', highlight);
            OP_AB.set_color_value(prefix + '_accent', accent);

            $('#' + prefix + '_top_margin').val(top_margin);
            $('#' + prefix + '_bottom_margin').val(bottom_margin);
            $('#' + prefix + '_line_height').val(line_height);
            $('#' + prefix + '_headline_tag').val(headline_tag);
            $('#' + prefix + '_effect').val(effect);

            $('input[name="' + prefix + '_align[]"]').each(function() {
                if ($(this).val() === align) {
                    $(this).prop('checked', true);
                }
            });

            var add_link = steps[1].find('.field-id-' + prefix + '_parts a.new-row'),
                container = steps[1].find('.field-id-' + prefix + '_parts-multirow-container');

            for (var a = 0; a < $.object_length(attrs.attrs); a += 1) {
                   if (typeof attrs.attrs['part_' + a] != 'undefined') {
                    add_link.trigger('click');
                    var current = container.find('.op-multirow:last');
                    current.find('input[type=text]').val(op_base64decode(attrs.attrs['part_' + a]));
                } else {
                    break;
                }
            }

            $('#' + prefix + '_content').val(op_base64decode(attrs.attrs.content));
        }
    }
}(opjq));
