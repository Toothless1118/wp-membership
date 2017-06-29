var op_asset_settings = (function ($) {

    var style_has_main_title = ['1', '4', '5', '6'];
    var style_has_main_subtitle = ['4', '6'];
    var style_has_main_title_color = ['4', '6'];
    var style_has_title = ['1', '2', '3', '4', '5', '6'];
    var style_has_content = ['1', '2', '3', '4', '5', '6'];
    var style_has_company = ['1', '2', '3', '4', '6'];
    var style_has_image = ['1', '3', '4', '5', '6'];
    var style_has_href = ['1', '2'];
    var style_has_button_color = ['1'];
    var style_has_button_text = ['1'];
    var style_has_header_color = ['2'];
    var style_has_background_color = ['3'];
    var style_has_columns = ['4', '5', 6];

    return {
        attributes: {
            step_1: {
                style: {
                    type: 'style-selector',
                    folder: 'previews',
                    addClass: 'op-disable-selected',
                }
            },
            step_2: {
                title: {
                    title: 'testimonial_slider_title',
                    addClass: 'op-testimonial-slider-title',
                    default_value: 'What Our Customers Have to Say',
                    showOn: {
                        field: 'step_1.style',
                        value: style_has_main_title,
                        idprefix: 'op_assets_addon_op_testimonial_slider_'
                    }
                },
                subtitle: {
                    title: 'testimonial_slider_subtitle',
                    addClass: 'op-testimonial-slider-subtitle',
                    showOn: {
                        field: 'step_1.style',
                        value: style_has_main_subtitle,
                        idprefix: 'op_assets_addon_op_testimonial_slider_'
                    }
                },
                title_color: {
                    title: 'testimonial_slider_title_color',
                    type: 'color',
                    default_value: '#329bc9',
                    showOn: {
                        field: 'step_1.style',
                        value: style_has_main_title_color,
                        idprefix: 'op_assets_addon_op_testimonial_slider_'
                    }
                },
                columns: {
                    title: 'testimonial_slider_columns',
                    type: 'select',
                    valueRange: {
                        start: 1,
                        finish: 3,
                        text_suffix: ''
                    },
                    default_value: '1',
                    showOn: {
                        field: 'step_1.style',
                        value: style_has_columns,
                        idprefix: 'op_assets_addon_op_testimonial_slider_'
                    }
                },
                elements: {
                    type: 'multirow',
                    multirow: {
                        attributes: {
                            image: {
                                title: 'image',
                                type: 'media',
                                showOn: {
                                    field: 'step_1.style',
                                    value: style_has_image,
                                    idprefix: 'op_assets_addon_op_testimonial_slider_',
                                    type: 'style-selector'
                                }
                            },
                            title: {
                                title: 'name',
                                showOn: {
                                    field: 'step_1.style',
                                    value: style_has_title,
                                    idprefix: 'op_assets_addon_op_testimonial_slider_'
                                }
                            },
                            company: {
                                addClass: 'op_testimonial_slider_company',
                                title: 'company',
                                showOn: {
                                    field: 'step_1.style',
                                    value: style_has_company,
                                    idprefix: 'op_assets_addon_op_testimonial_slider_'
                                }
                            },
                            content: {
                                title: 'testimonial',
                                type: 'wysiwyg',
                                showOn: {
                                    field: 'step_1.style',
                                    value: style_has_content,
                                    idprefix: 'op_assets_addon_op_testimonial_slider_'
                                }
                            },
                            button_text: {
                                addClass: 'op_testimonial_slider_button_text',
                                title: 'button_text',
                                default_value: 'Read story',
                                showOn: {
                                    field: 'step_1.style',
                                    value: style_has_button_text,
                                    idprefix: 'op_assets_addon_op_testimonial_slider_'
                                }
                            },
                            href: {
                                addClass: 'op_testimonial_slider_href',
                                title: 'link_url',
                                showOn: {
                                    field: 'step_1.style',
                                    value: style_has_href,
                                    idprefix: 'op_assets_addon_op_testimonial_slider_'
                                }
                            },
                            button_color: {
                                title: 'button_color',
                                type: 'color',
                                default_value: '#4881F5',
                                showOn: {
                                    field: 'step_1.style',
                                    value: style_has_button_color,
                                    idprefix: 'op_assets_addon_op_testimonial_slider_'
                                }
                            },
                            header_color: {
                                title: 'header_color',
                                type: 'color',
                                default_value: '#00b7e2',
                                showOn: {
                                    field: 'step_1.style',
                                    value: style_has_header_color,
                                    idprefix: 'op_assets_addon_op_testimonial_slider_'
                                }
                            },
                        },
                        onAdd: function () {
                            $('#op_assets_addon_op_testimonial_slider_style_container').find('.op-asset-dropdown-list a.selected').trigger('click');
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
                font: {
                    title: 'testimonial_slider_font_settings',
                    type: 'font'
                },
                testimonial_title_font: {
                    title: 'testimonial_slider_title_font_settings',
                    type: 'font'
                },
                animation_type: {
                    title: 'animation_type',
                    type: 'select',
                    values: {
                        'default': 'Default',
                        'fade': 'Fade',
                        'slide': 'Slide'
                    },
                    default_value: 'default'
                },
                animation_loop: {
                    title: 'animation_loop',
                    type: 'select',
                    values: {
                        'y': 'Yes',
                        'n': 'No'
                    },
                    default_value: 'n'
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
                },
                background_color: {
                    title: 'slider_background_color',
                    type: 'color',
                    default_value: '#F2F9FF',
                    showOn: {
                        field: 'step_1.style',
                        value: style_has_background_color,
                        // idprefix:'op_assets_addon_op_testimonial_slider_'
                    }
                },
            }
        },

        insert_steps: {
            2: {next: 'advanced_options'},
            3: true
        },

        customInsert: function (attrs) {
            var str = '';
            var attrs_str = '';
            var font_str = '';
            var testimonial_title_font = '';
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
                    subtitle = v.subtitle || '',
                    title_color = title_color || '',
                    a = v.content || '',
                    im = v.image || '',
                    button_color = v.button_color || '',
                    button_text = v.button_text || '',
                    header_color = v.header_color || '',
                    background_color = v.background_color || '',
                    href = v.href || '',
                    company = v.company || '',
                    columns = v.columns || 1;


                str += '[op_testimonial_slide title="' + q.replace(/"/ig, "'") + '" company="' + company + '" image="' + im + '" button_color="' + button_color + '" button_text="' + button_text + '" href="' + href + '" header_color="' + header_color + '" background_color="' + background_color + '" columns="' + columns + '" ]' + a + '[/op_testimonial_slide]';
            }

            /*
             * Advanced Options
             */
            $.each(['animation_type',
                'animation_loop',
                'slideshow_autostart',
                'animation_speed',
                'slideshow_speed',
                'title',
                'subtitle',
                'title_color',
                'background_color',
                'columns',
            ], function (index, key) {
                attrs_str += ' ' + key + '="' + attrs[key].replace(/"/ig, "'") + '"';
            });

            $.each(attrs.font, function (i, v) {
                if (v != '') {
                    font_str += ' font_' + i + '="' + v.replace(/"/ig, "'") + '"';
                }
            });

            $.each(attrs.testimonial_title_font, function (i, v) {
                if (v != '') {
                    testimonial_title_font += ' testimonial_title_font_' + i + '="' + v.replace(/"/ig, "'") + '"';
                }
            });

            str = '[op_testimonial_slider ' + testimonial_title_font + ' ' + font_str + ' style="' + attrs.style + '"' + attrs_str + ']' + str + '[/op_testimonial_slider]';

            OP_AB.insert_content(str);
            $.fancybox.close();
        },

        customSettings: function (attrs, steps) {
            var slides = attrs.op_testimonial_slide || {},
                add_link = steps[1].find('.field-id-op_assets_addon_op_testimonial_slider_elements a.new-row'),
                feature_inputs = steps[1].find('.field-id-op_assets_addon_op_testimonial_slider_elements-multirow-container'),
                background_color_container = steps[1].find('.field-background_color input'),
                title_color_container = steps[1].find('.field-title_color input'),
                style = attrs.attrs.style;


            attrs = attrs.attrs;
            OP_AB.set_selector_value('op_assets_addon_op_testimonial_slider_style_container', (style || ''));
            title_color_container.next('a').css({backgroundColor: attrs.title_color || '#329bc9'});
            attrs.columns = attrs.columns || '1';
            /*
             * Slides
             */
            $.each(slides, function (i, v) {
                add_link.trigger('click');
                var cur = feature_inputs.find('.op-multirow:last');
                var attrs = v.attrs || {};
                var input = cur.find('.field-image input');

                OP_AB.set_uploader_value(input.attr('id'), attrs.image);

                var button_color_container = cur.find('.field-button_color input');
                // var color_container = cur.find('.field-button_color input');
                var header_color_container = cur.find('.field-header_color input');
                title_color_container = cur.find('.field-title_color input');

                button_color_container.val(attrs.button_color || '').next('a').css({backgroundColor: attrs.button_color});
                // color_container.val(attrs.header_color || '').css({backgroundColor: attrs.header_color});
                header_color_container.val(attrs.header_color || '').next('a').css({backgroundColor: attrs.header_color});
                title_color_container.val(attrs.title_color || '').next('a').css({backgroundColor: attrs.title_color});

                cur.find('.field-title input').val(attrs.title || '');
                cur.find('.field-button_text input').val(attrs.button_text || '');
                cur.find('.op_testimonial_slider_href input').val(attrs.href || '');
                cur.find('.op_testimonial_slider_company input').val(attrs.company || '');
                cur.find('.op_testimonial_slider_header_color input').val(attrs.header_color || '');
                OP_AB.set_wysiwyg_content(cur.find('textarea').attr('id'), attrs.content || '');

            });

            /*
             * Advanced Options
             */
            $.each(['animation_type',
                'animation_loop',
                'slideshow_autostart',
                'animation_speed',
                'slideshow_speed',
                'title',
                'subtitle',
                'title_color',
                'background_color',
                'columns'
            ], function (index, key) {
                $('#op_assets_addon_op_testimonial_slider_' + key).val(attrs[key]);
            });

            OP_AB.set_font_settings('font', attrs, 'op_assets_addon_op_testimonial_slider_font');
            OP_AB.set_font_settings('testimonial_title_font', attrs, 'op_assets_addon_op_testimonial_slider_testimonial_title_font');

        }
    };
}(opjq));