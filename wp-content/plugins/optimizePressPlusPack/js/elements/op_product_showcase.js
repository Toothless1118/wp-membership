var op_asset_settings = (function($) {

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
                instruction_text: {
                    title: 'instruction_text',
                    addClass: 'op-testimonial-slider-title',
                    default_value: 'Click and drag the image to zoom in',
                },
                elements: {
                    type: 'multirow',
                    multirow: {
                        attributes: {
                            thumbnail: {
                                title: 'product_showcase_thumbnail',
                                type: 'media',
                                // required: true,
                                addClass: 'product-showcase-thumbnail',
                            },
                            thumbnail_width: {
                                type: 'hidden',
                                addClass: 'product-showcase-thumbnail-width'
                            },
                            thumbnail_height: {
                                type: 'hidden',
                                addClass: 'product-showcase-thumbnail-height'
                            },
                            thumbnail_id: {
                                type: 'hidden',
                                addClass: 'product-showcase-thumbnail-id'
                            },
                            image: {
                                title: 'product_showcase_image',
                                type: 'media',
                                required: true,
                                addClass: 'product-showcase-image',
                                events: {
                                    change: function (e) {
                                        setTimeout(function () {
                                            var $target = $(e.target);
                                            var $img = $target.next('.file-preview').find('.preview-image img');
                                            var imgId;
                                            var $hiddenInputs = $target.parent().parent().nextUntil('.product-showcase-image-id');
                                            var $thumbnailInputs = $target.parent().parent().prevUntil('.product-showcase-thumbnail');

                                            // width
                                            if ($img.attr('data-width')) {
                                                $hiddenInputs.eq(0).find('input').val($img.attr('data-width'));
                                            }

                                            // height
                                            if ($img.attr('data-height')) {
                                                $hiddenInputs.eq(1).find('input').val($img.attr('data-height'));
                                            }

                                            // id
                                            // We get thumbnail source, width and height from image id,
                                            // so that user doesn't have to upload the thumbnail separately.
                                            if ($img.attr('data-id')) {
                                                imgId = $img.attr('data-id');
                                                $hiddenInputs.eq(1).next().find('input').val(imgId);
                                                op_show_loading();

                                                $.post(OptimizePress.ajaxurl, {

                                                    action: OptimizePress.SN + '-get-image-thumbnail',
                                                    _wpnonce: $('#op_le_wpnonce').val(),
                                                    page_id: $('#page_id').val(),
                                                    image_id: imgId

                                                }, function (resp) {

                                                    var result = JSON.parse(resp);
                                                    var src = result[0];
                                                    var imgWidth = result[1];
                                                    var imgHeight = result[2];
                                                    var $imageInput;

                                                    // thumbnail id
                                                    $thumbnailInputs.eq(0).find('input').val(imgId);

                                                    // thumbnail height
                                                    $thumbnailInputs.eq(1).find('input').val(imgHeight);

                                                    // thumbnail width
                                                    $thumbnailInputs.eq(2).find('input').val(imgWidth);

                                                    // thumbnail src
                                                    $imageInput = $thumbnailInputs.eq(2).prev().find('input');
                                                    OP_AB.set_uploader_value($imageInput.attr('id'), src);
                                                    op_hide_loading();

                                                });
                                            }
                                        }, 100);
                                    }
                                }
                            },
                            image_width: {
                                type: 'hidden',
                                addClass: 'product-showcase-image-width'
                            },
                            image_height: {
                                type: 'hidden',
                                addClass: 'product-showcase-image-height'
                            },
                            image_id: {
                                type: 'hidden',
                                addClass: 'product-showcase-image-id'
                            },
                            description: {
                                title: 'product_showcase_image_description',
                            },
                        },
                        onAdd: function() {
                            $productShowcaseMultirow = $('#op_asset_browser_slide3').find('.field-id-op_assets_addon_op_product_showcase_elements-multirow-container .op-multirow');
                            if ($productShowcaseMultirow.length === 1) {
                                $productShowcaseMultirow.find('.field-selected_image .checkbox-container input').trigger('click');
                            }
                        }
                    }
                }
            },
            step_3: {
                microcopy: {
                    text: 'product_showcase_advanced',
                    type: 'microcopy'
                },
                general_options: {
                    title: 'general_options',
                    type: 'container',
                    // default_value: true,
                    attributes: {
                        element_size: {
                            title: 'product_showcase_element_size',
                            type: 'select',
                            values: {
                                '30': '30%',
                                '50': '50%',
                                '70': '70%',
                                '100': '100%'
                            },
                            default_value: '100'
                        },
                        element_border: {
                            title: 'product_showcase_element_border_color',
                            type: 'color',
                            default_value: '',
                        },
                    }
                },
                image_options: {
                    title: 'image_options',
                    type: 'container',
                    attributes: {
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
                        animation_speed: {
                            title: 'animation_speed',
                            default_value: 300
                        },
                    }
                },
                thumbnail_options: {
                    title: 'thumbnail_options',
                    type: 'container',
                    attributes: {
                        thumbnail_size: {
                            title: 'thumbnail_size',
                            type: 'select',
                            values: {
                                'verysmall': 'Very Small',
                                'small': 'Small',
                                'medium': 'Medium',
                                'large': 'Large'
                            },
                            default_value: 'medium'
                        },
                        selected_image_border_color: {
                            title: 'product_showcase_thumb_border_color',
                            type: 'color',
                            default_value: '#aed3ef',
                        },
                    }
                }
            }
        },

        insert_steps: {
            2: { next: 'advanced_options' },
            3: true
        },

        customInsert: function(attrs) {
            var str = '';
            var attrs_str = '';
            var elements = attrs.elements;
            var image;
            var thumbnail;
            var selected_image;
            var description;
            var i;

            /*
             * Checking if at least one slide exists
             */
            if (elements.length === 0) {
                alert('Please add at least one image');
                return;
            }

            attrs_str += ' instruction_text="' + encodeURIComponent(attrs.instruction_text) + '"';

            /*
             * Advanced Options
             */
            $.each([
                'animation_type',
                'animation_speed',
                'thumbnail_size',
                'selected_image_border_color',
                'element_size',
                'element_border',
            ], function(index, key) {
                attrs_str += ' ' + key + '="' + attrs[key].replace(/"/ig,"'") + '"';
            });

            /*
             * Slides
             */
            for (i in elements) {
                if (!elements.hasOwnProperty(i)) {
                    continue;
                }

                image = encodeURIComponent(elements[i].image) || '';
                thumbnail = encodeURIComponent(elements[i].thumbnail) || '';
                // selected_image = encodeURIComponent(elements[i].selected_image) || '';
                description = encodeURIComponent(elements[i].description) || '';
                image_width = encodeURIComponent(elements[i].image_width) || '';
                image_height = encodeURIComponent(elements[i].image_height) || '';
                image_id = encodeURIComponent(elements[i].image_id) || '';
                thumbnail_width = encodeURIComponent(elements[i].thumbnail_width) || '';
                thumbnail_height = encodeURIComponent(elements[i].thumbnail_height) || '';
                thumbnail_id = encodeURIComponent(elements[i].thumbnail_id) || '';

                str += '[op_product_showcase_child description="' + description + '" image="' + image + '" thumbnail="' + thumbnail + '" thumbnail_width="' + thumbnail_width + '" thumbnail_height="' + thumbnail_height + '" thumbnail_id="' + thumbnail_id + '" image_width="' + image_width + '" image_height="' + image_height + '" image_id="' + image_id + '"]' + '' + '[/op_product_showcase_child]';
            }

            str = '[op_product_showcase style="' + attrs.style + '"' + attrs_str + ']' + str + '[/op_product_showcase]';

            OP_AB.insert_content(str);
            $.fancybox.close();
        },

        customSettings: function(attrs, steps) {

            var children = attrs.op_product_showcase_child || [];
            var attrs = attrs.attrs;
            var style = attrs.style;
            var $add_child = steps[1].find('.field-id-op_assets_addon_op_product_showcase_elements .new-row');
            var $multirows = steps[1].find('.field-id-op_assets_addon_op_product_showcase_elements-multirow-container');
            var selectedImageIndex = 0;

            OP_AB.set_selector_value('op_assets_addon_op_product_showcase_style_container', (style || ''));

            /*
             * Slides
             */
            $.each(children, function(index, value) {

                $add_child.trigger('click');

                var $currentChild = $multirows.find('.op-multirow:last');
                var childAttrs = value.attrs || {};
                var $imageInput = $currentChild.find('.product-showcase-image input');
                var $thumbnailInput = $currentChild.find('.product-showcase-thumbnail input');

                OP_AB.set_uploader_value($imageInput.attr('id'), op_decodeURIComponent(childAttrs.image));
                OP_AB.set_uploader_value($thumbnailInput.attr('id'), op_decodeURIComponent(childAttrs.thumbnail));

                $currentChild.find('.field-description input').val(op_decodeURIComponent(childAttrs.description));

                $currentChild.find('.product-showcase-thumbnail-width input').val(op_decodeURIComponent(childAttrs.thumbnail_width));
                $currentChild.find('.product-showcase-thumbnail-height input').val(op_decodeURIComponent(childAttrs.thumbnail_height));
                $currentChild.find('.product-showcase-thumbnail-id input').val(op_decodeURIComponent(childAttrs.thumbnail_id));

                $currentChild.find('.product-showcase-image-width input').val(op_decodeURIComponent(childAttrs.image_width));
                $currentChild.find('.product-showcase-image-height input').val(op_decodeURIComponent(childAttrs.image_height));
                $currentChild.find('.product-showcase-image-id input').val(op_decodeURIComponent(childAttrs.image_id));

                // if (childAttrs.selected_image === 'Y') {
                //     selectedImageIndex = index;
                // }

            });

            /*
             * Advanced Options
             */
            $.each([
                'animation_type',
                'animation_speed',
            ], function(index, key) {
                $('#op_assets_addon_op_product_showcase_image_options_' + key).val(attrs[key]);
            });

            $('#op_assets_addon_op_product_showcase_thumbnail_options_thumbnail_size').val(attrs['thumbnail_size']);
            $('#op_assets_addon_op_product_showcase_general_options_element_size').val(attrs['element_size']);

            $('#op_assets_addon_op_product_showcase_thumbnail_options_selected_image_border_color').val(attrs.selected_image_border_color || '#aed3ef').next('a').css({ backgroundColor: attrs.selected_image_border_color || '#aed3ef' });

            $('#op_assets_addon_op_product_showcase_general_options_element_border').val(attrs.element_border || '').next('a').css({ backgroundColor: attrs.element_border || '' });
        }
    };
}(opjq));