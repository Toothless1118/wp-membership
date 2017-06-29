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
                margin: {
                    title: 'scroll_margin',
                    default_value: 0
                },
                element: {
                    title: 'scroll_element',
                    default_value: ''
                },
                hover: {
                    title: 'scroll_hover',
                    default_value: ''
                },
                padding: {
                    title: 'scroll_padding',
                    default_value: 0
                },
                speed: {
                    title: 'scroll_speed',
                    default_value: 800
                },
                effect: {
                    title: 'scroll_effect',
                    type: 'select',
                    values: { 'cancel': 'effect_cancel', 'swing': 'effect_swing', 'none': 'effect_none' },
                    default_value: 'swing'
                },
                animation: {
                    title: 'animation',
                    type: 'select',
                    values: { 'flash': 'animation_flash', 'bounce': 'animation_bounce', 'bounceIn': 'animation_bounceIn', 'bounceOut': 'animation_bounceOut', 'fadeIn': 'animation_fadeIn', 'fadeInUp': 'animation_fadeInUp', 'fadeInDown': 'animation_fadeInDown', 'fadeOut': 'animation_fadeOut', 'fadeOutUp': 'animation_fadeOutUp', 'fadeOutDown': 'animation_fadeOutDown', 'rotateIn': 'animation_rotateIn', 'rotateOut': 'animation_rotateOut' },
                    default_value: 'bounce'
                }
            }
        },
        insert_steps: {2:true},
        customInsert: function(attrs) {
            var content   = '',
                style     = attrs.style,
                margin    = attrs.margin * 1 || 0,
                element   = encodeURIComponent(attrs.element || ''),
                hover     = encodeURIComponent(attrs.hover || ''),
                padding   = attrs.padding * 1 || 0,
                speed     = Math.abs(attrs.speed * 1 || 0),
                effect    = attrs.effect,
                animation = attrs.animation;

            var output = ''
                + '[op_scroll_enhancer' + ' style="' + style + '" margin="' + margin + '" element="' + element + '" hover="' + hover + '" padding="' + padding + '" speed="' + speed + '" effect="' + effect + '" animation="' + animation + '"]'
                + content
                + '[/op_scroll_enhancer]';

            OP_AB.insert_content(output);
            $.fancybox.close();
        },
        customSettings: function(attrs, steps) {
            var style     = attrs.attrs.style,
                margin    = attrs.attrs.margin * 1 || 0,
                element   = decodeURIComponent(attrs.attrs.element || ''),
                hover     = decodeURIComponent(attrs.attrs.hover || ''),
                padding   = attrs.attrs.padding * 1 || 0,
                speed     = Math.abs(attrs.attrs.speed * 1 || 0),
                effect    = attrs.attrs.effect,
                animation = attrs.attrs.animation;

            var prefix  = 'op_assets_addon_op_scroll_enhancer';

            OP_AB.set_selector_value(prefix + '_style_container', attrs.attrs.style);

            $('#' + prefix + '_margin').val(margin);
            $('#' + prefix + '_element').val(element);
            $('#' + prefix + '_hover').val(hover);
            $('#' + prefix + '_padding').val(padding);
            $('#' + prefix + '_speed').val(speed);
            $('#' + prefix + '_effect').val(effect);
            $('#' + prefix + '_animation').val(animation);
        }
    }
}(opjq));
