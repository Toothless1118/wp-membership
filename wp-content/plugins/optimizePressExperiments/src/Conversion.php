<?php

class OptimizePressStats_Conversion
{
    /**
     * @var OptimizePressStats_Repository_Views
     */
    protected $viewsRepository;

    /**
     * @var OptimizePressStats_Repository_Experiments
     */
    protected $experimentsRepository;

    /**
     * Attach methods to hooks and filters
     */
    public function __construct()
    {
        add_action('wp', array($this, 'recordVisitConversion'));

        add_action('wp_ajax_optimizepress-stats-record-conversion', array($this, 'recordOptinConversion'));
        add_action('wp_ajax_nopriv_optimizepress-stats-record-conversion', array($this, 'recordOptinConversion'));

        $this->viewsRepository       = new OptimizePressStats_Repository_Views;
        $this->experimentsRepository = new OptimizePressStats_Repository_Experiments;
    }

    /**
     * Write "visit" conversion data to DB (if all conditions are satisfied).
     * @param  WP $wp
     * @return boolean
     */
    public function recordVisitConversion($wp)
    {
        $postId = get_queried_object_id();

        // Check if experiment exists with this ID as goal_page_id
        $experiment = $this->experimentsRepository->getExperimentForGoalPage($postId);
        if ( ! $experiment) {
            return;
        }

        if (apply_filters('optimizepress_stats/record_visit_conversion', true, $postId, $experiment->id)) {
            // Now we need to find latest entry in the pageviews table for this experiment_id and user_id and record conversion
            $status = $this->viewsRepository->recordVisitConversion($experiment->id, OptimizePressStats_User::getInstance()->getId());
        } else {
            $status = false;
        }

        return $status;
    }

    /**
     * Write conversion data to DB
     * @param  integer $recordId
     * @return boolean
     */
    public function recordOptinConversion($recordId = null)
    {
        if (defined('DOING_AJAX')) {
            check_ajax_referer('optimizepress-stats-record-conversion', 'optimizePressStatsNonce');
        }

        if ( ! isset($_POST['recordId']) && defined('DOING_AJAX')) {
            return;
        } else if (empty($recordId)) {
            $recordId = $_POST['recordId'];
        }

        if (apply_filters('optimizepress_stats/record_optin_conversion', true, $recordId)) {
            // Record the conversion for given record ID
            $status = $this->viewsRepository->recordOptinConversion(intval(sanitize_text_field($recordId)));
        } else {
            $status = false;
        }

        if (defined('DOING_AJAX')) {
            wp_send_json_success();
        }

        return $status;
    }
}

new OptimizePressStats_Conversion;