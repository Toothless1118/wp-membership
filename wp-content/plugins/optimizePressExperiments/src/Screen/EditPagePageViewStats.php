<?php

/**
 * Stats UI for page edit screen
 */
class OptimizePressStats_Screen_EditPagePageViewStats
{
    /**
     * @var OptimizePressStats_Charting_ChartJs
     */
    protected $charting;

    /**
     * @var OptimizePressStats_Repository_Views
     */
    protected $viewsRepository;

    /**
     * Hook into WP and init repositories.
     */
    public function __construct()
    {
        // Check whether to show page stats on edit page form
        if ( ! apply_filters('optimizepress-stats/show-page-stats', true)) {
            return;
        }

        add_action('edit_page_form', array($this, 'showPageStats'), 20);
        add_action('admin_enqueue_scripts', array($this, 'enqueueScripts'));

        add_action('wp_ajax_op-page-stats-get-page', array($this, 'getPageStats'));

        // Loading ChartJS lib and enqueueing colors data
        $this->charting = OptimizePressStats_Charting_Helper::getInstance();

        $this->viewsRepository = new OptimizePressStats_Repository_Views;
    }

    /**
     * Show page stats
     * @param  WP_Post $post
     * @return void
     */
    public function showPageStats($post)
    {
        $startDate  = date('Y-m-d', strtotime('-1 month'));
        $endDate    = date('Y-m-d');

        $screen = get_current_screen();

        if ($screen->id !== 'page') {
            return;
        }

        $data = $this->getPageStats($post->ID);
?>
        <script type="text/javascript">
            var opInitialPageStats = <?php echo json_encode($data); ?>;
        </script>
        <div id="op-stats-edit-page-stats-container" class="postbox">
            <h3 class="hndle ui-sortable-handle">
                <span><?php _e('Pageviews', 'optimizepress-stats'); ?></span>
            </h3>
            <div class="inside">
                <p>
                    <strong><?php _e('Showing stats for:', 'optimizepress-stats'); ?></strong>
                    <input type="text" name="daterange" id="op-page-stats-daterange" value="<?php echo $startDate . ' - ' . $endDate; ?>" data-page-id="<?php echo esc_attr($post->ID); ?>" />
                </p>
                <table style="width: 100%">
                    <thead>
                        <tr>
                            <th><?php _e('Total views', 'optimizepress-stats'); ?></th>
                            <th><?php _e('Unqiue visitors', 'optimizepress-stats'); ?></th>
                            <th><?php _e('Conversions', 'optimizepress-stats'); ?></th>
                            <th><?php _e('Conversion rate', 'optimizepress-stats'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="text-align:center;">
                                <h3 id="op-stats-page-sum-views"><?php echo $data['sum']['views']; ?></h3>
                            </td>
                            <td style="text-align:center;">
                                <h3 id="op-stats-page-sum-unique"><?php echo $data['sum']['unique']; ?></h3>
                            </td>
                            <td style="text-align:center;">
                                <h3 id="op-stats-page-sum-conversions"><?php echo $data['sum']['conversions']; ?></h3>
                            </td>
                            <td style="text-align:center;">
                                <h3 id="op-stats-page-sum-conversion-rate"><?php echo $data['sum']['conversion_rate']; ?>%</h3>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <canvas id="page_views"></canvas>
            </div>
        </div>
<?php
    }

    /**
     * Return formatted page stats for given page ID.
     * @param  integer $pageId if not provided it will be assumed that it is an AJAX request and it will read from GET
     * @return array|void if it is used in AJAX request it will output JSON, otherwise it will return array of data
     */
    public function getPageStats($pageId = null)
    {
        $start  = date('Y-m-d', strtotime('-1 month'));
        $end    = date('Y-m-d');
        $period = 'day';

        if (empty($pageId) && isset($_GET['page_id']) && ! empty($_GET['page_id'])) {
            $pageId = $pageId = sanitize_text_field($_GET['page_id']);
        }

        if (isset($_GET['start_date']) && ! empty($_GET['start_date'])) {
            $start = date('Y-m-d', strtotime(sanitize_text_field($_GET['start_date'])));
        }

        if (isset($_GET['end_date']) && ! empty($_GET['end_date'])) {
            $end = date('Y-m-d', strtotime(sanitize_text_field($_GET['end_date'])));
        }

        if (isset($_GET['period']) && ! empty($_GET['period'])) {
            $period = sanitize_text_field($_GET['period']);
        }

        $stats = $this->viewsRepository->getPageAggregatedViews($pageId, $start, $end);
        $data = $this->charting->formatPageViewDataForChart($stats, $start, $end);

        if (defined('DOING_AJAX') && DOING_AJAX) {
            wp_send_json_success(array('stats' => $data));
        }

        return $data;
    }

    /**
     * Enqueue stats UI script on stats page.
     * @param  string $hook
     * @return void
     */
    public function enqueueScripts($hook)
    {
        if ($hook !== 'post.php' || ! is_le_page()) {
            return;
        }

        wp_enqueue_script('moment', OP_S_BASE_URL . 'js/moment.min.js', false, '2.12.0', true);
        wp_enqueue_script('jquery-daterangepicker', OP_S_BASE_URL . '/js/jquery.daterangepicker' . OP_SCRIPT_DEBUG . '.js', array('moment', 'jquery'), '0.1.0', true);

        wp_enqueue_script('optimizepress-stats-edit-page-stats-ui', OP_S_BASE_URL . 'js/edit-page-stats-ui' . OP_SCRIPT_DEBUG . '.js', array('jquery'), OP_S_VERSION, true);

        wp_enqueue_style('jquery-daterangepicker', OP_S_BASE_URL . 'css//daterangepicker' . OP_SCRIPT_DEBUG . '.css', false, false, false);
    }
}

new OptimizePressStats_Screen_EditPagePageViewStats;