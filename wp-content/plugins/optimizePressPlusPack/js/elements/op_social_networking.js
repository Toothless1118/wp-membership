;var op_asset_settings = (function ($) {
    var style_has_background_color = ['style-1', 'style-3'],
        style_has_icon_and_font_color = ['style-2', 'style-3'],
        facebook_link = ['style-1', 'style-2', 'style-3'],
        twitter_link = ['style-1', 'style-2', 'style-3'],
        google_link = ['style-1', 'style-2', 'style-3'],
        linked_link = ['style-1', 'style-2', 'style-3'],
        youtube_link = ['style-1', 'style-2', 'style-3'],
        instagram_link = ['style-1', 'style-2', 'style-3'],
        snapchat_link = ['style-1', 'style-2', 'style-3'],
        pinterest_link = ['style-1', 'style-2', 'style-3'],
        new_tab = ['style-1', 'style-2', 'style-3'],
        attrs = {
            attributes: {
                help_vids: {
                    step_1: {
                        url: '',
                        width: '600',
                        height: '341'
                    },
                    step_2: {
                        url: '',
                        width: '600',
                        height: '341'
                    }
                },
                step_1: {
                    style: {
                        type: 'style-selector',
                        folder: 'previews',
                        addClass: 'op-disable-selected',
                    }
                },
                step_2: {
                    background_color: {
                        title: 'social_networking_background_color',
                        type: 'color',
                        default_value: '#3e87d3',
                        showOn: {field: 'step_1.style', value: style_has_background_color}
                    },
                    icon_and_font_color_box: {
                        title: 'social_networking_icon_and_font_color_box',
                        type: 'color',
                        default_value: '#fff',
                        showOn: {field: 'step_1.style', value: style_has_icon_and_font_color}
                    },
                    new_tab: {
                        title: 'social_networking_new_tab',
                        value: false,
                        type: 'checkbox'
                    },
                    hide_text: {
                        title: 'social_networking_hide_text',
                        value: false,
                        type: 'checkbox',
                        showOn: {field: 'step_1.style', value: ['style-2']}
                    },
                    facebook_url: {
                        title: 'social_networking_fb',
                        addClass: 'op-social-networking-fb',
                        placeholder: "https://www.facebook.com/yourAccountName",
                        showOn: {field: 'step_1.style', value: facebook_link}
                    },
                    twitter_url: {
                        title: 'social_networking_tw',
                        addClass: 'op-social-networking-tw',
                        placeholder: "https://www.twitter.com/yourAccountName",
                        showOn: {field: 'step_1.style', value: twitter_link}
                    },
                    google_url: {
                        title: 'social_networking_g',
                        addClass: 'op-social-networking-g',
                        placeholder: "https://plus.google.com/yourAccountName",
                        showOn: {field: 'step_1.style', value: google_link}
                    },
                    linkedin_url: {
                        title: 'social_networking_ln',
                        addClass: 'op-social-networking-ln',
                        placeholder: "https://www.linkedin.com/in/yourAccountName",
                        showOn: {field: 'step_1.style', value: linked_link}
                    },
                    instagram_url: {
                        title: 'social_networking_in',
                        addClass: 'op-social-networking-in',
                        placeholder: "https://www.instagram.com/yourAccountName",
                        showOn: {field: 'step_1.style', value: instagram_link}
                    },
                    youtube_url: {
                        title: 'social_networking_yt',
                        addClass: 'op-social-networking-yt',
                        placeholder: "https://www.youtube.com/user/yourAccountName",
                        showOn: {field: 'step_1.style', value: youtube_link}
                    },
                    pinterest_url: {
                        title: 'social_networking_pt',
                        addClass: 'op-social-networking-pt',
                        placeholder: "https://www.pinterest.com/yourAccountName",
                        showOn: {field: 'step_1.style', value: pinterest_link}
                    },
                    snapchat_url: {
                        title: 'social_networking_sc',
                        addClass: 'op-social-networking-sc',
                        placeholder: "https://www.snapchat.com/yourAccountName",
                        showOn: {field: 'step_1.style', value: snapchat_link}
                    }
                }
            },
            insert_steps: {2: true},
            customInsert: function (attrs) {
                if (attrs.facebook_url == '' && attrs.google_url == '' &&
                    attrs.linkedin_url == '' && attrs.instagram_url == '' &&
                    attrs.youtube_url == '' && attrs.twitter_url == '' &&
                    attrs.pinterest_url == '' && attrs.snapchat_url == '' ) {
                    alert('Add at least one social account');
                    return;
                }

                var urlRegex = new RegExp("^(http:\/\/www.|https:\/\/www.|www.|http:\/\/|https:\/\/)");
                var urlPostfix = new RegExp("_url");

                for (var attr in attrs) {
                    if (urlPostfix.test(attr) && attrs[attr] != "" && urlRegex.test(attrs[attr]) === false) {
                        alert("URL you provided for is not in valid format. Please check and correct it.");
                        $("#op_assets_addon_op_social_networking_" + attr).focus();
                        return;
                    }
                }

                var str = '[op_social_networking ';
                for (var i in attrs) {
                    if (attrs.hasOwnProperty(i) && attrs[i]) {
                        str += ' ' + i + '="' + attrs[i].replace(/"/ig, "'") + '"'
                    }
                }
                str += '] ';
                OP_AB.insert_content(str);
                $.fancybox.close();
            },
            customSettings: function (attrs) {
                attrs = attrs.attrs || {};
                for (var i in attrs) {
                    if (attrs.hasOwnProperty(i)) {
                        if (i == 'style') {
                            OP_AB.set_selector_value('op_assets_addon_op_social_networking_style_container', attrs[i]);
                            $('.op-pick-color').val(attrs.background_color || '').css({backgroundColor: attrs.background_color});
                        } else if (i == 'new_tab') {
                            $('#op_assets_addon_op_social_networking_' + i).attr('checked', attrs.new_tab).trigger('change');
                        } else if (i == 'hide_text') {
                            $('#op_assets_addon_op_social_networking_' + i).attr('checked', attrs.hide_text).trigger('change');
                        } else if (i == 'icon_and_font_color') {
                            $('#op_assets_addon_op_social_networking_' + i).attr('checked', attrs.icon_and_font_color_box).trigger('change');
                        } else {
                            $('#op_assets_addon_op_social_networking_' + i).val(attrs[i]);
                            $('op_assets_addon_op_social_networking_background_color').val(attrs.background_color || '').trigger('change');
                            $('#op_assets_addon_op_social_networking_icon_and_font_color_box').val(attrs.icon_and_font_color_box || '').trigger('change');
                        }
                    }
                }
            }
        };
    // attrs.attributes.step_2.fb_lang.default_value = op_fb_comments_asset['options']['lang'];
    // attrs.attributes.step_2.fb_lang.values = op_fb_comments_asset.languages;
    return attrs;
}(opjq));
