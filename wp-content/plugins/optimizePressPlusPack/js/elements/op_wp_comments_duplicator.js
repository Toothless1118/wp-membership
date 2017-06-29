var op_asset_settings = (function($) {
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
                page: {
                    title: 'comments_page_id'
                }
            }
        },
        insert_steps: {
            2: true
        }
    };
}(opjq));