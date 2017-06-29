<?php

class OptimizePressStats_Pageview
{
    /**
     * @var OptimizePressStats_Repository_Views
     */
    protected $viewsRepository;

    /**
     * Attach methods to hooks and filters
     */
    public function __construct()
    {
        // Priority "20" is set so A/B testing can attach itself earlier on
        add_action('pre_get_posts', array($this, 'registerPageview'), 20, 1);

        $this->viewsRepository = new OptimizePressStats_Repository_Views;
    }

    /**
     * Register page view
     * @param  WP_Query $query
     * @return WP_Query
     */
    public function registerPageview($query)
    {
        // Fetch experiment
        $experiment = OptimizePressStats_Experiment::getInstance();

        if ($query->is_main_query() && $query->is_page() && !$query->is_admin() && is_le_page($experiment->getOriginalPageId()) && OptimizePressStats_Request::isValid()) {
            $experimentId       = $experiment->getExperimentId();
            $originalPageId     = $experiment->getOriginalPageId();
            $variationPageId    = $experiment->getVariationPageId();
            $user               = OptimizePressStats_User::getInstance();

            // Check whether to register pageview depending on given experimentId. This is the point
            // to disable basic pageviews without experiments (experimentId will be 0).
            if ( ! apply_filters('optimizepress-stats/register-pageview', true, $experimentId)) {
                return;
            }

            $recordId = $this->viewsRepository->recordPageview(
                $experimentId,
                $originalPageId,
                $this->getPageTemplateId($variationPageId),
                $user->getId(),
                $user->getCountry(),
                $variationPageId,
                current_time('mysql')
            );

            wp_enqueue_script('optimizepress-stats', OP_S_BASE_URL . 'js/conversion' . OP_SCRIPT_DEBUG . '.js', array('jquery'), OP_S_VERSION);
            wp_localize_script('optimizepress-stats', 'OptimizePressStats', array(
                'ajaxurl'   => admin_url('admin-ajax.php'),
                'nonce'     => wp_create_nonce('optimizepress-stats-record-conversion'),
                'recordId'  => $recordId,
            ));
        }
    }

    /**
     * Return page template ID
     * @param  integer $pageId
     * @return mixed
     */
    protected function getPageTemplateId($pageId)
    {
        $templateId = get_post_meta($pageId, '_optimizepress_template_id', true);

        return ! empty($templateId) ? $templateId : null;
    }
}

new OptimizePressStats_Pageview();