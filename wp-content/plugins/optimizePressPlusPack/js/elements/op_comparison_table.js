var op_asset_settings = (function($){
    var features = [];
    var removeTimeout;
    return {
        // help_vids: {
        //     step_1: {
        //         url: 'http://op2-inapp.s3.amazonaws.com/elements-pricing-table.mp4',
        //         width: '600',
        //         height: '341'
        //     },
        //     step_2: {
        //         url: 'http://op2-inapp.s3.amazonaws.com/elements-pricing-table.mp4',
        //         width: '600',
        //         height: '341'
        //     }
        // },
        attributes: {
            step_1: {
                style: {
                    type: 'style-selector',
                    folder: 'previews',
                    addClass: 'op-disable-selected',
                }
            },
            step_2: {
                description: {
                    type: 'microcopy',
                    text: 'comparison_table_info'
                },
                icon_clone: {
                    title: 'small_icon',
                    type: 'image-selector',
                    folder: "icons",
                    default_value: '9999.png',
                    // folder: OptimizePress.OP_ABSOLUTE_URL + 'lib/assets/images/bullet_block/32x32/',
                    selectorClass: 'tabs-clone-icon-container',

                    // this is clone object, so do not display it
                    showOn: { field: 'step_1.style', value: ['-1'] }
                },

                elements: {
                    type: 'container',
                    title: 'features',
                    type: 'container',
                    addClass: 'op-comparison-table-features',
                    attributes: {
                        features: {
                            type: 'multirow',
                            multirow: {
                                attributes: {
                                    feature: {
                                        type: 'input',
                                        events: {
                                            change: function(e){
                                                var $this = $(e.target);
                                                var $multirow = $('#op_container_content_op_assets_addon_op_comparison_table_columns > .multirow-container > .op-multirow');
                                                var $features;

                                                if ($this.attr('data-feature-nr')) {
                                                    features[$this.attr('data-feature-nr')] = $this.val();
                                                } else {
                                                    $this.attr('data-feature-nr', features.length);
                                                    features[features.length] = $this.val();
                                                }

                                                $multirow.each(function () {
                                                    $features = $(this).find('.comparison-table-feature-paragraph');
                                                    $features.each(function (index) {
                                                        $(this).text(features[index]);
                                                    });
                                                });
                                            },
                                            remove: function (e) {
                                                clearTimeout(removeTimeout);
                                                removeTimeout = setTimeout(function () {
                                                    var featureNr = $(e.target).attr('data-feature-nr')
                                                    var featureTxt = $(e.target).val();
                                                    var itemIndex;

                                                    if (features[featureNr] === featureTxt) {

                                                        var $multirow = $('#op_container_content_op_assets_addon_op_comparison_table_columns > .multirow-container > .op-multirow');
                                                        var $features;

                                                        features.splice(featureNr, 1);
                                                        $multirow.each(function () {
                                                            $features = $(this).find('.comparison-table-feature-paragraph');
                                                            $features.eq(featureNr).parent().remove();
                                                        });

                                                        $('.field-id-op_assets_addon_op_comparison_table_elements_features-multirow-container .field-feature').each(function (index) {
                                                            $(this).find('input').attr('data-feature-nr', index);
                                                        });

                                                    }

                                                }, 1);
                                            }
                                        }
                                    },
                                },
                                onAdd: function(e){
                                    var $newInput = $('#op_container_content_op_assets_addon_op_comparison_table_elements').find('.op-multirow:last input');
                                    var newValue = $newInput.val();
                                    var $columns = $('#op_container_content_op_assets_addon_op_comparison_table_columns');

                                    $columns.find('.multirow-container > .op-multirow').each(function () {
                                        $(this).find('.new-row').trigger('click');
                                    });

                                    $newInput.trigger('change').focus();
                                }
                            }
                        },
                    }
                },

                columns: {
                    title: 'columns',
                    type: 'container',
                    addClass: 'op-comparison-table-column',
                    attributes: {
                        columns: {
                            type: 'multirow',
                            multirow: {
                                link_prefix: 'column',
                                attributes: {
                                    type: {
                                        type: 'hidden',
                                        default_value: 'column'
                                    },
                                    title: {
                                        title: 'title',
                                        type: 'input',
                                        default_value: 'Column Title',
                                        events: {
                                            change: function(){
                                                var multi = $(this).closest('.op-multirow');
                                                var cont = multi.parent();
                                                var idx = cont.find('.op-multirow').index(multi);
                                                cont.find('.op-multirow-children li a:eq('+idx+')').text($(this).val());
                                            },
                                            keyup: function(){
                                                $(this).trigger('change');
                                            }
                                        },
                                        trigger_events: 'change'
                                    },
                                    price: {
                                        title: 'price',
                                        type: 'input',
                                        default_value: '0.00'
                                    },
                                    pricing_unit: {
                                        title: 'pricing_unit',
                                        type: 'input',
                                        default_value: '$'
                                    },
                                    pricing_variable: {
                                        title: 'pricing_variable',
                                        type: 'input',
                                        default_value: ''
                                    },
                                    pricing_description: {
                                        title: 'pricing_description',
                                        type: 'input',
                                        default_value: ''
                                    },
                                    order_button_text: {
                                        title: 'order_button_text',
                                        type: 'input'
                                    },
                                    order_button_url: {
                                        title: 'order_button_url',
                                        type: 'input'
                                    },
                                    package_description: {
                                        title: 'package_description',
                                        type: 'wysiwyg'

                                    },
                                    most_popular: {
                                        title: 'most_popular',
                                        type: 'checkbox',
                                        events: {
                                            change: function(){
                                                var input = $(this).parent().parent().next();
                                                if ($(this).is(':checked')) input.show(); else input.hide();
                                            }
                                        }
                                    },
                                    // most_popular_text: {
                                    //     title: 'most_popular_text',
                                    //     type: 'input',
                                    // },
                                    feature_description: {
                                        addClass: 'op_comparison_table_hidden',
                                        title: 'feature_description',
                                        type: 'input',
                                        showOn: { field: 'step_1.style', value: ['-1'] } // we need this because it's used in multirow, but we don't want to show it.
                                    },
                                    feature_icon: {
                                        type: 'image-selector',
                                        addClass: 'comparison_table_hidden_image_selector',

                                        // this is clone object, so do not display it
                                        showOn: { field: 'step_1.style', value: ['-1'] }
                                    },
                                    items: {
                                        title: 'package_features',
                                        addClass: 'comparison_table_features',
                                        type: 'multirow',
                                        multirow: {
                                            remove_row: 'after',
                                            attributes: {
                                                type: {
                                                    type: 'hidden',
                                                    default_value: 'feature'
                                                },
                                                feature_title: {
                                                    type: 'paragraph',
                                                    text: 'Feature',
                                                    addClass: 'comparison-table-feature-column comparison-table-feature-paragraph'
                                                },
                                                feature_icon: {
                                                    type: 'image-selector',
                                                    default_value: '9999.png',
                                                    selectorClass: 'comparison-icon-container ',
                                                    addClass: 'comparison-table-feature-column comparison-table-feature-icon'
                                                },
                                                feature_description: {
                                                    type: 'input',
                                                    addClass: 'comparison-table-feature-column comparison-table-feature-text'
                                                },
                                            },
                                            onAdd: function(e){
                                                var icoContainer = $('#op_assets_addon_op_comparison_table_icon_clone_container');
                                                var icoList = $('#op_assets_addon_op_comparison_table_icon_clone_container').children('.op-asset-dropdown-list').children('ul');

                                                if (icoList.length > 0) {
                                                    var imgFirs = icoContainer.find('.selected-item img').attr('alt');
                                                    // var imgFirs = icoList.children('li').find('img').first().attr('src').toString().split('/').pop();
                                                    $(this).find('.comparison-icon-container > .op-asset-dropdown-list:empty')
                                                        .empty()
                                                        .append(icoList.clone())
                                                        .each(function() {
                                                            // OP_AB.set_selector_value($(this).parent().attr('id'), imgFirs);
                                                            OP_AB.set_selector_value($(this).parent().attr('id'), imgFirs);
                                                        });
                                                }

                                            }
                                        }
                                    },
                                },
                                onAdd: function(e){
                                    if (features.length > 0) {
                                        var $multirow = $('#op_container_content_op_assets_addon_op_comparison_table_columns').find('.op-multirow:last');
                                        var $newRowButton = $multirow.find('.new-row');
                                        var $features;

                                        for (i = 0; i < features.length; i += 1) {
                                            $newRowButton.trigger('click');
                                            $features = $multirow.find('.comparison-table-feature-paragraph');
                                            $features.eq(i).text(features[i]);
                                        }
                                    }
                                }
                            }
                        }
                    }
                },

                guarantee: {
                    type: 'container',
                    title: 'guarantee',
                    type: 'container',
                    addClass: 'op-comparison-table-guarantee',
                    // showOn: {
                    //     field: 'step_1.style',
                    //     value: [ 1, 2, 3, 4 ]
                    // },
                    attributes: {
                        guarantee_info: {
                            type: 'microcopy',
                            text: 'guarantee_info'
                        },
                        guarantee_description: {
                            title: 'guarantee_description',
                            default_value: '',
                            // default_value: 'Your purchase is backed by our 100% money back guarantee.',
                            type: 'input'
                        },
                        guarantee_icon: {
                            title: 'icon',
                            type: 'image-selector',
                            folder: "guarantee",
                            selectorClass: 'comparison-guarantee-icon',
                        }
                    }
                }
            }
        },

        insert_steps: { 2: true },

        customInsert: function(attrs){
            var result = '';
            var total = attrs.columns.length;
            var style = (attrs.style || 1);
            var featuresResult = '';
            var totalFeatures = attrs.features.length;
            var featuresString = '';
            var i = 0;
            var j = 0;
            var columns = [];
            var delimiter = '';

            // result = '[op_comparison_table_features]';
            // result += '[/op_comparison_table_features]';

            for (i = 0; i < totalFeatures; i++) {
                featuresResult += 'feature_' + i + '="' + encodeURIComponent(attrs.features[i].feature) + '" ';

                // ;;;;;;; is delimiter for splitting in php later
                featuresString += delimiter + encodeURIComponent(attrs.features[i].feature);
                delimiter = ';;;;;;;';
                // result += '[op_comparison_table_feature]' + encodeURIComponent(attrs.features[i].feature) + '[/op_comparison_table_feature]';
            }


            // add feature to column (child to child)
            for (i = 0; i < attrs.columns.length; i++) {
                if (attrs.columns[i] === undefined) {
                    continue;
                }
                else if (attrs.columns[i].type == 'column') {
                    columns.push(attrs.columns[i]);
                    columns[columns.length - 1].items = '';
                    j = 0;
                }
                else if (attrs.columns[i].type == 'feature') {
                    columns[columns.length - 1].items += "<li class='op-comparison-table-feature'>";
                        columns[columns.length - 1].items += "<div class='op-comparison-table-feature-cell'>";

                            columns[columns.length - 1].items += "<span class='op-comparison-table-feature-title'>";
                                columns[columns.length - 1].items += encodeURIComponent(features[j]);
                                j += 1;
                            columns[columns.length - 1].items += "</span>";

                            if (attrs.columns[i].feature_icon !== '9999.png') {
                                columns[columns.length - 1].items += "<span class='op-comparison-table-feature-icon-container'>";
                                    columns[columns.length - 1].items += "<img class='op-comparison-table-feature-icon' data-icon='" + attrs.columns[i].feature_icon + "' src='" + window.oppp_path + 'images/elements/op_comparison_table/icons/' + attrs.columns[i].feature_icon + "' />";
                                columns[columns.length - 1].items += "</span>";
                            }

                            if (typeof attrs.columns[i].feature_description !== 'undefined' && attrs.columns[i].feature_description !== '') {
                                columns[columns.length - 1].items += "<span class='op-comparison-table-feature-text'>";
                                    columns[columns.length - 1].items += encodeURIComponent(attrs.columns[i].feature_description || '');
                                columns[columns.length - 1].items += "</span>";
                            }

                        columns[columns.length - 1].items += "</div>";
                    columns[columns.length - 1].items += "</li>";
                }
            }

            // loop columns
            for (i = 0; i < columns.length; i++) {
                if (columns[i] === undefined) {
                    continue;
                }

                // child shortcode
                result += ''
                    + '[op_comparison_table_item'
                    + ' style="' + style + '"'
                    + ' total="' + total + '"'
                    + ' title="' + encodeURIComponent(columns[i].title || '').replace( /"/ig,"'") + '"'
                    + ' price="' + encodeURIComponent(columns[i].price || '') + '"'
                    + ' pricing_unit="' +  encodeURIComponent(columns[i].pricing_unit || '') + '"'
                    + ' pricing_variable="' + encodeURIComponent(columns[i].pricing_variable || '') + '"'
                    + ' pricing_description="' + encodeURIComponent(columns[i].pricing_description || '') + '"'
                    + ' most_popular="' + encodeURIComponent(columns[i].most_popular || '') + '"'
                    // + ' most_popular_text="' + encodeURIComponent(columns[i].most_popular_text) + '"'
                    + ' order_button_text="' + encodeURIComponent(columns[i].order_button_text || '') + '"'
                    + ' order_button_url="' + encodeURIComponent(columns[i].order_button_url || '') + '"'
                    + ' package_description="' + encodeURIComponent(columns[i].package_description || '') + '"'
                    + ' feature_description="' + encodeURIComponent(columns[i].feature_description || '') + '"'
                    + ' features="' + featuresString + '"'
                    + ' items="' + (columns[i].items || '') + '"'
                    + ']'
                    + '[/op_comparison_table_item]';
            };

            // wrap shortcode
            result = '[op_comparison_table guarantee_text="' + encodeURIComponent(attrs.guarantee_description) + '" guarantee_icon="' + attrs.guarantee_icon + '" style="' + style + '" ' + featuresResult + ' features="' + featuresString + '"' + ']' + result + '[/op_comparison_table]';

            // dialog
            OP_AB.insert_content(result);
            $.fancybox.close();

        },

        customSettings: function(attrs, steps) {

            // reset the features, they're being repopulated here
            features = [];

            var style = attrs.attrs.style;
            var children = attrs.op_comparison_table_item || [];
            var $container = $('#op_container_content_op_assets_addon_op_comparison_table_columns');
            var featuresArray = [];
            var featuresLenghth = 0;

            var i = 0;
            var key;
            var currentAttrs = attrs.attrs;
            var $featuresPanel = $('#op_container_content_op_assets_addon_op_comparison_table_elements');
            var $featuresPanelAdd = $featuresPanel.find('.new-row');

            for (key in currentAttrs) {
                // We only want to count actual features, not other attributes.
                if (currentAttrs.hasOwnProperty(key) && key.indexOf('feature_') === 0) {
                    featuresLenghth += 1;
                }
            }

            for (i = 0; i < featuresLenghth; i += 1) {
                featuresArray.push(op_decodeURIComponent(attrs.attrs['feature_' + i] || ''));
                $featuresPanelAdd.trigger('click');
            }

            $featuresPanel.find('.op-multirow').each(function (index) {
                $(this).find('input').val(op_decodeURIComponent(attrs.attrs['feature_' + index] || '')).trigger('change');
            });

            // set the style
            OP_AB.set_selector_value('op_assets_addon_op_comparison_table_style_container', style || '');
            OP_AB.set_selector_value('op_assets_addon_op_comparison_table_guarantee_guarantee_icon_container', attrs.attrs.guarantee_icon || '');
            $('#op_assets_addon_op_comparison_table_guarantee_guarantee_description').val(op_decodeURIComponent(attrs.attrs.guarantee_text || ''));

            // iterate between the columns and set the proper settings
            for (i = 0; i < children.length; i += 1) {

                // add new column
                steps[1].find('#op_container_content_op_assets_addon_op_comparison_table_columns .field-id-op_assets_addon_op_comparison_table_columns_columns .new-row').trigger('click');

                var value  = children[i].attrs;
                var $parent = $container.find('> .multirow-container > .op-multirow:last')
                var input  = $parent.find('input');

                // set input values
                input.filter('[id$="_title"]').val(op_decodeURIComponent(value.title || ''));
                input.filter('[id$="_price"]').val(op_decodeURIComponent(value.price || ''));
                input.filter('[id$="_pricing_unit"]').val(op_decodeURIComponent(value.pricing_unit || ''));
                input.filter('[id$="_pricing_variable"]').val(op_decodeURIComponent(value.pricing_variable || ''));
                input.filter('[id$="_pricing_description"]').val(op_decodeURIComponent(value.pricing_description || ''));
                input.filter('[id$="_order_button_text"]').val(op_decodeURIComponent(value.order_button_text || ''));
                input.filter('[id$="_order_button_url"]').val(op_decodeURIComponent(value.order_button_url || ''));
                // input.filter('[id$="_feature_description"]').val(op_decodeURIComponent(value.feature_description || ''));

                // most popular
                if (value.most_popular == 'Y') {
                    input.filter('[id$="_most_popular"]').trigger('click');
                    // input.filter('[id$="_most_popular_text"]').val(op_decodeURIComponent(value.most_popular_text || ''));
                }

                // set wysiwyg content
                (function(id, content) {
                    if ( ! id) {
                        return;
                    }
                    OP_AB.set_wysiwyg_content(id, op_wpautop(op_decodeURIComponent(content || '')));
                }($parent.find('[id$="_package_description"]').attr('id'), value.package_description));


                // iterate through all the items in the features list and add defaults
                $itemsList = $('<ul>' + (op_decodeURIComponent(value.items) || '') + '</ul>').find('li');

                $parent.find('.op-multirow').each(function (index) {
                    var iconContainerId = $(this).find('.field-feature_icon .comparison-icon-container').attr('id');
                    $(this).find('.field-feature_title').text(featuresArray[index]);
                    OP_AB.set_selector_value(iconContainerId, $itemsList.eq(index).find('img').attr('data-icon') || '9999.png');
                    $(this).find('.field-feature_description input').val($itemsList.eq(index).find('.op-comparison-table-feature-text').text() || '');
                });

            }

        }
    };

})(opjq);