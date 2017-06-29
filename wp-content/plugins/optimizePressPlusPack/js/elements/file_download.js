var op_asset_settings = (function($){
    var style_has_icon = ['1', '2', '3', '4'];
    return {
        help_vids: {
            step_1: {
                url: 'http://op2-inapp.s3.amazonaws.com/elements-file-download.mp4',
                width: '600',
                height: '341'
            },
            step_2: {
                url: 'http://op2-inapp.s3.amazonaws.com/elements-file-download.mp4',
                width: '600',
                height: '341'
            }
        },
        attributes: {
            step_1: {
                style: {
                    type: 'style-selector',
                    folder: 'previews',
                    addClass: 'op-disable-selected',
                }
            },
            step_2: {
                elements: {
                    type: 'multirow',
                    multirow: {
                        attributes: {
                            icon: {
                                title: 'icon',
                                type: 'image-selector',
                                folder: 'icons',
                                selectorClass: 'icon-view-64',
                                showOn: {
                                    field: 'step_1.style',
                                    value: style_has_icon,
                                    idprefix:'op_assets_core_file_download_'
                                }
                            },
                            file: {
                                title: 'file',
                                type: 'media',
                                required: true,
                            },
                            title: {
                                title: 'file_name',
                                required: true,

                            },
                            new_window: {
                                title: 'new_window',
                                type: 'checkbox'
                            },
                            content: {
                                title: 'file_description',
                                type: 'textarea',
                                format: 'br'
                            }
                        },
                        onAdd: function(steps){
                            var $selected_style = steps[0].find('.op-asset-dropdown-list .selected');

                            $selected_style.trigger('click');
                        }
                    }
                }
            }
        },
        insert_steps: {2:true},
        customInsert: function(attrs){
            var str = '',
                style = '',
                field = style == 'image' ? 'image' : 'icon',
                attrs_str = '';

            for(var i in attrs.elements){
                if (!attrs.elements.hasOwnProperty(i)) {
                    continue;
                }
                var v = attrs.elements[i],
                    q = encodeURIComponent(v.title) || '',
                    a = encodeURIComponent(v.content) || '',
                    file = v.file || '',
                    pack = v.packages || '',
                    level = v.level || '',
                    ic = v[field] || '',
                    bg = v.bg_color || '',
                    new_window = v.new_window || '';
                str += '[op_file_download_item title="'+q.replace( /"/ig,"'")+'" '+field+'="'+ic+'" file="'+file+'" package="'+pack+'" level="'+level+'" new_window="' +  new_window + '"]'+a+'[/op_file_download_item]';
            }
            str = '[file_download style="'+attrs.style+'"]'+str+'[/file_download]';
            OP_AB.insert_content(str);
            $.fancybox.close();
        },
        customSettings: function(attrs,steps){
            var features = attrs.download || attrs.op_file_download_item || {},
                add_link = steps[1].find('.field-id-op_assets_core_file_download_elements a.new-row'),
                feature_inputs = steps[1].find('.field-id-op_assets_core_file_download_elements-multirow-container'),
                style = attrs.attrs.style;
            attrs = attrs.attrs;
            OP_AB.set_selector_value('op_assets_core_file_download_style_container',(style || ''));
            OP_AB.set_font_settings('font',attrs,'op_assets_core_file_download_font');
            OP_AB.set_font_settings('content_font',attrs,'op_assets_core_file_download_content_font');
            $.each(features,function(i,v){
                add_link.trigger('click');
                var cur = feature_inputs.find('.op-multirow:last'),
                    attrs = v.attrs || {},
                    //input = (cur.find('input:eq(0)').is(':hidden') ? cur.find('input:eq(0)').parents('.field-bg_color').next('.field-image').find('input') : cur.find('input:eq(0)')),
                    uploader = cur.find('input:eq(0)'),
                    selectLevel = cur.find('select:eq(0)'),
                    selectPackage = cur.find('select:eq(1)');

                $('#' + selectPackage.attr('id')).val(attrs.package || '');
                $('#' + selectLevel.attr('id')).val(attrs.level || '');
                OP_AB.set_selector_value(cur.find('.op-asset-dropdown').attr('id'),(attrs.icon || ''));
                OP_AB.set_uploader_value(uploader.attr('id'), attrs.file);
                cur.find('input:eq(1)').val(op_decodeURIComponent(attrs.title));
                cur.find('textarea').val(op_decodeURIComponent(attrs.content));
                cur.find('input[type="checkbox"]').attr('checked',((attrs.new_window || 'N') == 'Y'));
            });
        }
    };
}(opjq));