<?php

class OptimizePress_Modules_SignupForm
{
    /**
     * Initialize hooks and filters.
     */
    public function __construct()
    {
        add_filter('op_signup_form_styles', array($this, 'addStyles'));

        add_action('wp_enqueue_scripts', array($this, 'enqueueStyles'));
    }

    /**
     * Append PlusPack custom styles.
     * @param array $styles
     * @return array
     */
    public function addStyles($styles)
    {
        $styles['oppp1'] = array(
            'title'     => __('PlusPack Style 1', 'oppp'),
            'preview'   => OPPP_BASE_URL . 'images/modules/signup_form/previews/oppp1.png',
        );
        $styles['oppp2'] = array(
            'title'     => __('PlusPack Style 2', 'oppp'),
            'preview'   => OPPP_BASE_URL . 'images/modules/signup_form/previews/oppp2.png',
        );

        return $styles;
    }

    /**
     * Enqueue styles.
     * @return void
     */
    public function enqueueStyles()
    {
        // Different dependencies when OP_SCRIPT_DEBUG is turned on and off (all styles are concatenated into opplus-front-all.css & opplus-back-all for production)
        if (OP_SCRIPT_DEBUG === '') {
            wp_enqueue_style('oppp-signup-form', OPPP_BASE_URL . 'css/modules/signup_form.css', array(), OPPP_VERSION, 'all');
        }
    }
}

new OptimizePress_Modules_SignupForm;
