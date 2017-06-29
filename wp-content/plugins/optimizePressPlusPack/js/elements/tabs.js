var op_asset_settings = (function($){
    var hasColor = ['5'];
    var hasColorSelect = ['4'];
    var hasIcon = ['3'];
    return {
        help_vids: {
            step_1: {
                url: 'http://op2-inapp.s3.amazonaws.com/elements-tabs.mp4',
                width: '600',
                height: '341'
            },
            step_2: {
                url: 'http://op2-inapp.s3.amazonaws.com/elements-tabs.mp4',
                width: '600',
                height: '341'
            },
            step_3: {
                url: 'http://op2-inapp.s3.amazonaws.com/elements-tabs.mp4',
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
                    events: {
                        change: function(style, container) {
                            // show/hide .field-icon (tabs.css)
                            $(container[1])
                                .removeClass('tabs-icon-container-display')
                                .addClass(hasIcon.indexOf(style) != -1 ? 'tabs-icon-container-display' : '__temp__')
                                .removeClass('__temp__');
                        }
                    }
                }
            },
            step_2: {
                color: {
                    title: 'color',
                    type: 'color',
                    showOn: {field:'step_1.style',value:hasColor}
                },
                color_select: {
                    title: 'color',
                    type: 'select',
                    values: {'':'blue','red':'red','green':'green' },
                    default_value: '',
                    showOn: {field:'step_1.style',value:hasColorSelect}
                },
                icon_clone: {
                    title: 'small_icon',
                    type: 'image-selector',
                    folder: "img",
                    selectorClass: 'tabs-clone-icon-container',
                    showOn: { field: 'step_1.style', value: ['-1'] }        // this is clone object, so do not display it
                },
                tabs: {
                    title: 'tabs_count',
                    type: 'select',
                    valueRange: {start:2,finish:10,text_suffix:'tabs'},
                    default_value: '2',
                    multirow: {
                        link_prefix: 'tab',
                        attributes: {
                            title: {
                                title: 'title',
                                type: 'input',
                                default_value: 'Tab Title',
                                events: {
                                    change: function(){
                                        var multi = $(this).closest('.op-multirow'),
                                            cont = multi.parent(),
                                            idx = cont.find('.op-multirow').index(multi);
                                        cont.find('.op-multirow-tabs li a:eq('+idx+')').text($(this).val());
                                    },
                                    keyup: function(){
                                        $(this).trigger('change');
                                    }
                                },
                                trigger_events: 'change'
                            },
                            content: {
                                title: 'content',
                                type: 'wysiwyg',
                                default_value: 'Tab Content'
                            },
                            icon: {
                                title: 'small_icon',
                                type: 'image-selector',
                                folder: "img",
                                selectorClass: 'tabs-icon-container'
                                //showOn not working on multirow, so using this.attributes.step_1.style.events.change()
                            },
                        },
                        onAdd: function(a,b,c){
                            $(this).find('.op-multirow:last :input[type="text"]').trigger('change');

                            // append image list from icon_clone to new tab element
                            var icoList = $(this).parent().children('.field-id-op_assets_core_tabs_icon_clone').children('#op_assets_core_tabs_icon_clone_container').children('.op-asset-dropdown-list').children('ul');
                            if (icoList.length > 0) {
                                var imgFirs = icoList.children('li').find('img').first().attr('src').toString().split('/').pop();
                                $(this).find('.tabs-icon-container > .op-asset-dropdown-list:empty')
                                    .empty()
                                    .append(icoList.clone())
                                    .each(function() {
                                        OP_AB.set_selector_value($(this).parent().attr('id'), imgFirs);
                                    });
                            }
                        }
                    }
                }
            },
            step_3: {
                microcopy: {
                    text: 'tabs_advanced1',
                    type: 'microcopy'
                },
                microcopy2: {
                    text: 'advanced_warning_2',
                    type: 'microcopy',
                    addClass: 'warning'
                },
                font: {
                    title: 'tabs_font_settings',
                    type: 'font'
                }
            }
        },
        insert_steps: {2:{next:'advanced_options'},3:true},
        customInsert: function(attrs){
            var str = '',
                font_str = '',
                total = attrs.tabs.total;
            $.each(attrs.font,function(i,v){
                if(v != ''){
                    font_str += ' font_'+i+'="'+v.replace(/"/ig,"'")+'"';
                }
            });
            for(var i=0;i<total;i++){
                var title = '', content = '', icon = '';
                if(typeof attrs.tabs.rows[i] != 'undefined'){
                    title = encodeURIComponent(attrs.tabs.rows[i].title);
                    icon = encodeURIComponent(attrs.tabs.rows[i].icon);
                    content = encodeURIComponent(attrs.tabs.rows[i].content);
                }
                str += '[tab title="'+title.replace( /"/ig,"'")+'" icon="'+icon+'"'+font_str+']'+content+'[/tab] ';
            };
            str = '[tabs'
                + ' style="' + (attrs.style || 1) + '"'
                + ' has_icon="' + (hasIcon.indexOf(attrs.style) != -1 ? '1' : '0') + '"'
                + ' color="' + attrs.color + '"'
                + ' color_select="' + attrs.color_select + '"'
                + ' ' + font_str
                + ']'
                + str
                + '[/tabs]';
            OP_AB.insert_content(str);
            $.fancybox.close();
        },
        customSettings: function(attrs,steps){

            var tab = attrs.tab || [],
                container = steps[1].find('.field-id-op_assets_core_tabs_tabs-multirow-container');

            OP_AB.set_selector_value('op_assets_core_tabs_style_container',attrs.attrs.style || 1);
            OP_AB.set_font_settings('font',attrs.attrs,'op_assets_core_tabs_font');
            $('#op_assets_core_tabs_tabs').val(tab.length || 2).trigger('change');
            $('#op_assets_core_tabs_color_select').val(attrs.attrs.color_select || '').trigger('change');
            $('#op_assets_core_tabs_color').val(attrs.attrs.color || '').trigger('change');

            container = container.find('> div');

            $.each(tab,function(i,v){
                v.attrs.title = op_decodeURIComponent(v.attrs.title);
                v.attrs.icon = op_decodeURIComponent(v.attrs.icon);
                v.attrs.content = op_decodeURIComponent(v.attrs.content);

                OP_AB.set_selector_value('op_assets_core_tabs_tabs_' + i + '_icon_container',v.attrs.icon);

                var tmp = container.filter(':eq('+i+')'),
                    id = tmp.find('input[name$="_title"]').val(v.attrs.title).trigger('change').end()
                        .find('textarea').val(OP_AB.unautop(v.attrs.content)).trigger('change').attr('id');
                OP_AB.set_wysiwyg_content(id,v.attrs.content);
            });
        }
    };
}(opjq));
