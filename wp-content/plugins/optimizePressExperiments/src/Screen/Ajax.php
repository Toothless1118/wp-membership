<?php

class OptimizePressStats_Screen_Ajax
{
    /**
     * @var OptimizePressStats_Charting_ChartJs
     */
    protected $charting;

    /**
     * @var OptimizePressStats_Repository_Experiments
     */
    protected $experimentsRepository;

    /**
     * @var OptimizePressStats_Repository_Variants
     */
    protected $variantsRepository;

    /**
     * @var OptimizePressStats_Repository_Views
     */
    protected $viewsRepository;

    /**
     * Init hooks and repos.
     */
    public function __construct()
    {
        add_action('wp_ajax_op-stats-get-page', array($this, 'getPageStats'));
        add_action('wp_ajax_op-experiments-get-page', array($this, 'getPage'));
        add_action('wp_ajax_op-experiments-page-list', array($this, 'getPageList'));
        add_action('wp_ajax_op-experiments-clone-page', array($this, 'clonePage'));
        add_action('wp_ajax_op-experiments-switch-status', array($this, 'switchStatus'));
        add_action('wp_ajax_op-experiments-get-experiment', array($this, 'getExperiment'));
        add_action('wp_ajax_op-experiments-save-experiment', array($this, 'saveExperiment'));
        add_action('wp_ajax_op-stats-get-experiment', array($this, 'getExperimentWithStats'));
        add_action('wp_ajax_op-stats-get-experiment-stats', array($this, 'getExperimentStats'));
        add_action('wp_ajax_op-experiments-remove-experiment', array($this, 'removeExperiment'));

        // Loading ChartJS lib and enqueueing colors data
        $this->charting = OptimizePressStats_Charting_Helper::getInstance();

        $this->viewsRepository          = new OptimizePressStats_Repository_Views;
        $this->variantsRepository       = new OptimizePressStats_Repository_Variants;
        $this->experimentsRepository    = new OptimizePressStats_Repository_Experiments;
    }

    /**
     * Switch experiment status.
     * @return void
     */
    public function switchStatus()
    {
        if (isset($_POST['experiment_id']) && isset($_POST['status'])) {
            $this->experimentsRepository->switchExperimentStatus(
                sanitize_text_field($_POST['experiment_id']),
                sanitize_text_field($_POST['status'])
            );

            wp_send_json_success();
        }

        wp_send_json_error();
    }

    /**
     * Clone LE page.
     * @return void
     */
    public function clonePage()
    {
        require_once OP_ADMIN . 'clone_page.php';

        $oldId = sanitize_text_field($_GET['page_id']);
        $newId = OptimizePress_Admin_ClonePage::getInstance()->clonePage($oldId);

        wp_send_json_success(array(
            'id' => $newId,
            'title' => get_the_title($newId),
        ));
    }

    /**
     * Return filtered page list (using "term" from GET to search through the list).
     * @return void
     */
    public function getPageList()
    {
        $experimentId = isset($_GET['experiment_id']) ? sanitize_text_field($_GET['experiment_id']) : 0;

        if ( ! isset($_GET['no-filter'])) {
            $pages = get_pages(array(
                'exclude' => $this->variantsRepository->getAllTakenPageIds($experimentId),
                'meta_key' => '_optimizepress_pagebuilder',
                'meta_value' => 'Y',
                'hierarchical' => 0,
            ));
        } else {
            $pages = get_pages(array(
                'exclude' => $this->experimentsRepository->getAllTakenGoalPostIds($experimentId),
                'hierarchical' => 0,
            ));
        }

        $data = array();

        if ($pages) {
            foreach ($pages as $page) {
                $data[] = array(
                    'id' => $page->ID,
                    'text' => $page->post_title,
                );
            }
        }

        wp_send_json_success($data);
    }

    /**
     * Return page data through AJAX response.
     * @return void
     */
    public function getPage()
    {
        $pageId = sanitize_text_field($_GET['page_id']);

        wp_send_json_success(array(
            'id'    => $pageId,
            'title' => get_the_title($pageId),
        ));
    }

    /**
     * Remove experiment, its variants and view stats.
     * @param  integer $experimentId
     * @return boolean               In case of AJAX request it will return status in JSON format.
     */
    public function removeExperiment($experimentId = null)
    {
        if (defined('DOING_AJAX') && DOING_AJAX) {
            $experimentId = sanitize_text_field($_GET['experiment_id']);
        }

        $this->viewsRepository->deleteExperimentViews($experimentId);
        $this->experimentsRepository->deleteExperiment($experimentId);

        if (defined('DOING_AJAX') && DOING_AJAX) {
            wp_send_json_success();
        }

        return true;
    }

    /**
     * Return experiment object.
     * @param  integer $experimentId
     * @return object In case of AJAX request it will output data in JSON format.
     */
    public function getExperiment($experimentId = null)
    {
        if (defined('DOING_AJAX') && DOING_AJAX) {
            $experimentId = sanitize_text_field($_GET['experiment_id']);
        }

        $experiment = $this->experimentsRepository->getExperiment($experimentId);
        $variations = $this->variantsRepository->getVariantsForExperiment($experimentId, false);

        if ( ! $experiment || ! $variations) {
            if (defined('DOING_AJAX')) {
                wp_send_json_error(array('code' => 404, 'message' => __('Missing experiment or its variations.')));
            }

            return;
        }

        // Add page titles
        $experiment->page_title = get_the_title($experiment->page_id);
        $experiment->goal_page_title = empty($experiment->goal_page_id) ? '' : get_the_title($experiment->goal_page_id);
        foreach ($variations as $variation) {
            $variation->page_title = get_the_title($variation->page_id);
        }

        $data = array(
            'experiment'    => $experiment,
            'variations'    => $variations,
        );

        if (defined('DOING_AJAX')) {
            wp_send_json_success($data);
        }

        return $data;
    }

        /**
     * Save experiment data.
     * @return void
     */
    public function saveExperiment()
    {
        $originalPageId = sanitize_text_field($_POST['original_page_id']);

        if ( ! isset($_POST['experiment_id']) || 0 === (int) $_POST['experiment_id']) {
            // Create new experiment
            $experimentId = $this->experimentsRepository->createExperiment(
                $originalPageId,
                sanitize_text_field($_POST['experiment_name']),
                '',
                sanitize_text_field($_POST['goal_type']),
                sanitize_text_field($_POST['goal_page_id']),
                sanitize_text_field($_POST['status']),
                sanitize_text_field($_POST['start_date']),
                sanitize_text_field($_POST['end_date'])
            );

            // Save variations
            if ($experimentId && isset($_POST['variations']) && is_array($_POST['variations'])) {
                // Add variants
                $this->createVariantsForExperiment($experimentId, $originalPageId);

                wp_send_json_success(array(
                    'experiment_id'         => $experimentId,
                    'original_page_url'     => get_the_permalink($originalPageId),
                    'original_page_title'   => get_the_title($originalPageId),
                ));
            }

            wp_send_json_error();
        } else {
            $experimentId = sanitize_text_field($_POST['experiment_id']);

            // Update existing experiment
            $this->experimentsRepository->updateExperiment(
                $experimentId,
                $originalPageId,
                sanitize_text_field($_POST['experiment_name']),
                '',
                sanitize_text_field($_POST['goal_type']),
                sanitize_text_field($_POST['goal_page_id']),
                sanitize_text_field($_POST['status']),
                sanitize_text_field($_POST['start_date']),
                sanitize_text_field($_POST['end_date'])
            );

            // Clean existing variants
            $this->variantsRepository->deleteVariantsForExperiment($experimentId);

            // Add variants
            $this->createVariantsForExperiment($experimentId, $originalPageId);

            wp_send_json_success(array(
                'experiment_id'         => $experimentId,
                'original_page_url'     => get_the_permalink($originalPageId),
                'original_page_title'   => get_the_title($originalPageId),
            ));
        }
    }

    /**
     * Return formatted experiments stats per day for given experiment ID in between given dates.
     * @param  integer $experimentId
     * @param  string $stat
     * @param  string $start        expects date in Y-m-d format
     * @param  string $end          expects date in Y-m-d format
     * @return array
     */
    public function getExperimentStats($experimentId, $stat = 'conversions', $start = null, $end = null)
    {
        if (defined('DOING_AJAX') && DOING_AJAX) {
            $experimentId = sanitize_text_field($_GET['experiment_id']);
        }

        if (defined('DOING_AJAX') && DOING_AJAX) {
            if (isset($_GET['stat'])) {
                $stat = sanitize_text_field($_GET['stat']);
            } else {
                $stat = 'conversions';
            }
        }

        if (isset($_GET['start_date']) && ! empty($_GET['start_date'])) {
            $start = date('Y-m-d', strtotime(sanitize_text_field($_GET['start_date'])));
        } else if (null === $start) {
            $start = date('Y-m-d', strtotime('-1 month'));
        }

        if (isset($_GET['end_date']) && ! empty($_GET['end_date'])) {
            $end = date('Y-m-d', strtotime(sanitize_text_field($_GET['end_date'])));
        } else if (null === $end) {
            $end = date('Y-m-d');
        }

        $variants = $this->variantsRepository->getVariantsForExperiment($experimentId, true);
        $variants = wp_list_pluck($variants, 'name', 'page_id');

        $stats  = $this->viewsRepository->getExperimentViews($experimentId, $start, $end);
        $data   = $this->charting->formatExperimentDataForChart($stats, $start, $end);

        // Sorting stats according to variants order
        $data['stats']   = $this->charting->manuallySort($data['stats'], array_keys($variants));

        $return = array(
            'labels'    => $data['labels'],
            'datasets'  => array()
        );

        $borderColor        = $this->charting->getColors('1.0');
        $backgroundColor    = $this->charting->getColors('0.2');
        $colorIndex         = 0;

        foreach ($data['stats'] as $id => $variant) {
            $return['datasets'][] = array(
                'label'             => $variants[$id],
                'backgroundColor'   => $backgroundColor[$colorIndex],
                'borderColor'       => $borderColor[$colorIndex],
                'data'              => array_values($variant[$stat]),
                'lineTension'       => 0
            );

            $colorIndex += 1;
        }

        if (defined('DOING_AJAX') && DOING_AJAX) {
            wp_send_json_success($return);
        }

        return $return;
    }

    /**
     * Return experiment object.
     * @param  integer $experimentId
     * @return object In case of AJAX request it will output data in JSON format.
     */
    public function getExperimentWithStats($experimentId = null)
    {
        if (defined('DOING_AJAX') && DOING_AJAX) {
            $experimentId = sanitize_text_field($_GET['experiment_id']);
        }

        $stats      = $this->viewsRepository->getExperimentViewsSum($experimentId);
        $experiment = $this->experimentsRepository->getExperiment($experimentId);
        $variations = $this->variantsRepository->getVariantsForExperiment($experimentId, false);

        if ( ! $experiment || ! $variations) {
            if (defined('DOING_AJAX')) {
                wp_send_json_error(array('code' => 404, 'message' => __('Missing experiment or its variations.')));
            }

            return;
        }

        $variationStats = array(
            'labels' => array(__('Original', 'optimizepress-stats')),
            'views' => array(isset($stats[$experiment->page_id]) ? $stats[$experiment->page_id]->views : 0),
            'unique' => array(isset($stats[$experiment->page_id]) ? $stats[$experiment->page_id]->unique : 0),
            'conversions' => array(isset($stats[$experiment->page_id]) ? $stats[$experiment->page_id]->conversions : 0),
        );

        // Add page titles
        $experiment->page_title = get_the_title($experiment->page_id);
        $experiment->goal_page_title = empty($experiment->goal_page_id) ? '' : get_the_title($experiment->goal_page_id);
        foreach ($variations as $variation) {
            $variation->page_title = get_the_title($variation->page_id);

            if (isset($stats[$variation->page_id])) {
                $variationStats['labels'][]         = $variation->name;
                $variationStats['views'][]          = $stats[$variation->page_id]->views;
                $variationStats['unique'][]         = $stats[$variation->page_id]->unique;
                $variationStats['conversions'][]    = $stats[$variation->page_id]->conversions;
            }
        }

        $data = array(
            'experiment'    => $experiment,
            'variations'    => $variations,
            'stats'         => $variationStats,
        );

        if (defined('DOING_AJAX')) {
            wp_send_json_success($data);
        }

        return $data;
    }

    /**
     * Return stats for given page.
     * @return array If this is used in AJAX request then it will output results as a JSON string
     */
    public function getPageStats()
    {
        if ( ! isset($_GET['page_id'])) {
            if (defined('DOING_AJAX') && DOING_AJAX) {
                wp_send_json_error(array('code' => '412', 'message' => __('Missing page ID param.', 'optimizepress-stats')));
            }

            return;
        }

        $start  = date('Y-m-d', strtotime('-1 month'));
        $end    = date('Y-m-d');
        $period = 'day';

        // Override default values from AJAX request
        if (isset($_GET['start_date']) && ! empty($_GET['start_date'])) {
            $start = date('Y-m-d', strtotime(sanitize_text_field($_GET['start_date'])));
        }

        if (isset($_GET['end_date']) && ! empty($_GET['end_date'])) {
            $end = date('Y-m-d', strtotime(sanitize_text_field($_GET['end_date'])));
        }

        if (isset($_GET['period']) && ! empty($_GET['period'])) {
            $period = sanitize_text_field($_GET['period']);
        }

        $pageId = sanitize_text_field($_GET['page_id']);

        // Get page details
        $page = array(
            'title'     => get_the_title($pageId),
            'view_link' => get_the_permalink($pageId),
            'edit_link' => get_edit_post_link($pageId),
        );

        $stats = $this->viewsRepository->getPageAggregatedViews($pageId, $start, $end);

        $data = array(
            'page'  => $page,
            'stats' => $this->charting->formatPageViewDataForChart($stats, $start, $end),
        );

        if (defined('DOING_AJAX') && DOING_AJAX) {
            wp_send_json_success($data);
        }

        return $data;
    }

    /**
     * Create variants from POST data for given experiment ID.
     * @param  integer $experimentId
     * @param  integer $originalPageId
     * @return void
     */
    protected function createVariantsForExperiment($experimentId, $originalPageId)
    {
        // Save original as one of the variations
        $this->variantsRepository->createVariant(
            $experimentId,
            $originalPageId,
            $originalPageId,
            __('Original', 'optimizepress-stats')
        );

        // Traverse through variations array
        foreach ($_POST['variations'] as $variation) {
            $this->variantsRepository->createVariant(
                $experimentId,
                $variation['id'],
                $originalPageId,
                $variation['label']
            );
        }
    }
}

new OptimizePressStats_Screen_Ajax;