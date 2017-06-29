var op_asset_settings = (function($) {
    var style_has_title     = ['1', '3'],
        style_has_content   = ['1', '3'],
        style_has_bg_color  = ['1'],
        style_has_image     = ['1', '2', '3'],
        style_has_href      = ['1', '2', '3'],
        image_sizes         = ['500px X 300px', '960px X 540px', '960px X 540px'],
        image_size_string   = '',
        current_style       = 0;

    function replace_image_label() {
        var $labels = $('#op_asset_browser_slide3 .op_slide_image > label');
        image_size_string = $labels.html() || '';
        $labels.html(image_size_string.replace(/\((.+px)?/, '(' + image_sizes[current_style]));
    }

    return {
        attributes: {
            step_1: {
                style: {
                    type: 'style-selector',
                    folder: 'previews',
                    addClass: 'op-disable-selected',
                    events: {
                        change: function(value) {
                            current_style = value - 1;
                            replace_image_label();
                        }
                    }
                }
            },
            step_2: {
                elements: {
                    type: 'multirow',
                    multirow: {
                        attributes: {
                            image: {
                                title: 'slider_image_size',
                                type: 'media',
                                addClass: 'op_slide_image',
                                showOn: {
                                    field: 'step_1.style',
                                    value: style_has_image,
                                    idprefix:'op_assets_addon_op_slider_',
                                    type:'style-selector'
                                }
                            },
                            bg_color: {
                                title: 'bg_color',
                                type: 'color',
                                default_value: '',
                                showOn: {
                                    field: 'step_1.style',
                                    value: style_has_bg_color,
                                    idprefix:'op_assets_addon_op_slider_'
                                }
                            },
                            title: {
                                title: 'title',
                                showOn: {
                                    field: 'step_1.style',
                                    value: style_has_title,
                                    idprefix:'op_assets_addon_op_slider_'
                                }
                            },
                            content: {
                                title: 'content',
                                type: 'wysiwyg',
                                showOn: {
                                    field: 'step_1.style',
                                    value: style_has_content,
                                    idprefix:'op_assets_addon_op_slider_'
                                }
                            },
                            href: {
                                addClass: 'op_slider_href',
                                title: 'link_url',
                                showOn: {
                                    field: 'step_1.style',
                                    value: style_has_href,
                                    idprefix:'op_assets_addon_op_slider_'
                                }
                            }
                        },
                        onAdd: function() {
                            $('#op_assets_addon_op_slider_style_container').find('.op-asset-dropdown-list a.selected').trigger('click');
                            replace_image_label();
                        }
                    }
                }
            },
            step_3: {
                microcopy: {
                    text: 'slider_advanced',
                    type: 'microcopy'
                },
                microcopy2: {
                    text: 'advanced_warning_2',
                    type: 'microcopy',
                    addClass: 'warning'
                },
                animation_type: {
                    title: 'animation_type',
                    type: 'select',
                    values: {
                        'fade': 'Fade',
                        'slide': 'Slide'
                    },
                    default_value: 'slide'
                },
                animation_loop: {
                    title: 'animation_loop',
                    type: 'select',
                    values: {
                        'y': 'Yes',
                        'n': 'No'
                    },
                    default_value: 'y'
                },
                slideshow_sizing: {
                    title: 'slideshow_sizing',
                    type: 'select',
                    values: {
                        'normal': 'Normal',
                        'stretch': 'Stretch'
                    },
                    default_value: 'normal'
                },
                slideshow_autostart: {
                    title: 'slideshow_autostart',
                    type: 'select',
                    values: {
                        'y': 'Yes',
                        'n': 'No'
                    },
                    default_value: 'y'
                },
                slideshow_speed: {
                    title: 'slideshow_speed',
                    default_value: 7000
                },
                animation_speed: {
                    title: 'animation_speed',
                    default_value: 700
                }
                // font: {
                //     title: 'title_font_styling',
                //     type: 'font'
                // },
                // content_font: {
                //     title: 'content_font_styling',
                //     type: 'font'
                // }
            }
        },
        insert_steps: {2:{next:'advanced_options'},3:true},
        customInsert: function(attrs) {
            var str = '',
                attrs_str = '';

            /*
             * Checking if at least one slide exists
             */
            if (attrs.elements.length === 0) {
                alert('Add at least one slide');
                return;
            }

            /*
             * Slides
             */
            for (var i in attrs.elements) {
                if (!attrs.elements.hasOwnProperty(i)) {
                    continue;
                }
                var v = attrs.elements[i],
                    q = v.title || '',
                    a = v.content || '',
                    im = v.image || '',
                    bg = v.bg_color || '',
                    href = v.href || '';
                str += '[op_slide title="' + q.replace(/"/ig, "'") + '" image="' + im + '" bg_color="' + bg + '" href="' + href + '"]' + a + '[/op_slide]';
            }

            /*
             * Advanced Options
             */
            $.each(['animation_type', 'animation_loop', 'slideshow_sizing', 'slideshow_autostart', 'animation_speed', 'slideshow_speed'], function(index, key) {
                attrs_str += ' ' + key + '="' + attrs[key].replace(/"/ig,"'") + '"';
            });

            // $.each(['font', 'content_font'], function(i,v) {
            //     $.each(attrs[v], function(i2,v2) {
            //         if(v2 != ''){
            //             attrs_str += ' '+v+'_'+i2+'="'+v2.replace(/"/ig,"'")+'"';
            //         }
            //     });
            // });

            str = '[op_slider style="' + attrs.style + '"' + attrs_str + ']' + str + '[/op_slider]';
            OP_AB.insert_content(str);
            $.fancybox.close();
        },
        customSettings: function(attrs,steps) {
            var slides = attrs.op_slide || {},
                add_link = steps[1].find('.field-id-op_assets_addon_op_slider_elements a.new-row'),
                feature_inputs = steps[1].find('.field-id-op_assets_addon_op_slider_elements-multirow-container'),
                style = attrs.attrs.style;

            attrs = attrs.attrs;
            OP_AB.set_selector_value('op_assets_addon_op_slider_style_container',(style || ''));
            // OP_AB.set_font_settings('font',attrs,'op_assets_addon_op_slider_font');
            // OP_AB.set_font_settings('content_font',attrs,'op_assets_addon_op_slider_content_font');

            /*
             * Slides
             */
            $.each(slides,function(i,v){
                add_link.trigger('click');
                var cur = feature_inputs.find('.op-multirow:last'),
                    attrs = v.attrs || {},
                    input = cur.find('.field-image input');
                OP_AB.set_uploader_value(input.attr('id'), attrs.image);
                var color_container = cur.find('.field-bg_color input');
                color_container.val(attrs.bg_color || '').next('a').css({ backgroundColor: attrs.bg_color });
                cur.find('.field-title input').val(attrs.title || '');
                cur.find('.op_slider_href input').val(attrs.href || '');
                OP_AB.set_wysiwyg_content(cur.find('textarea').attr('id'),attrs.content || '');
            });

            /*
             * Advanced Options
             */
            $.each(['animation_type', 'animation_loop', 'slideshow_sizing', 'slideshow_autostart', 'animation_speed', 'slideshow_speed'], function(index, key) {
                $('#op_assets_addon_op_slider_' + key).val(attrs[key]);
            });
        }
    };
}(opjq));