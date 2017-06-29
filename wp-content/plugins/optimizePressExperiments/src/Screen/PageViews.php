<?php

class OptimizePressStats_Screen_PageViews
{
    /**
     * @var OptimizePressStats_Repository_Views
     */
    protected $viewsRepository;

    /**
     * @var integer
     */
    protected $itemsPerPage = 20;

    /**
     * Init hooks and repositories.
     */
    public function __construct()
    {
        add_action('admin_enqueue_scripts', array($this, 'enqueueScripts'));
        add_action('print_media_templates', array($this, 'printTemplates'));
        add_action('optimizepress_stats/statistics_page', array($this, 'renderPageViewsPage'));

        add_action('wp_ajax_op-stats-get-pages', array($this, 'getPageviews'));

        $this->viewsRepository = new OptimizePressStats_Repository_Views;
    }

    /**
     * Print templates.
     * @return void
     */
    public function printTemplates()
    {
        $startDate  = date('Y-m-d', strtotime('-1 month'));
        $endDate    = date('Y-m-d');

        $screen = get_current_screen();

        if ($screen->id !== 'optimizepress_page_optimizepress-statistics' || ! isset($_GET['section']) || $_GET['section'] !== 'stats') {
            return;
        }
?>
        <script type="text/html" id="tmpl-op-stats-page-details">
            <form id="op-stats-page-details">
                <h1 class="op-experiment-popup">{{{ data.form_title }}}</h1>
                <div class="op-lightbox-content">
                    <div class="op-actual-lightbox-content">
                        <div class="settings-container">
                            <p>
                                <strong><?php _e('Showing stats for:', 'optimizepress-stats'); ?></strong>
                                <input type="text" name="daterange" id="op-stats-daterange" value="<?php echo $startDate . ' - ' . $endDate; ?>" data-page-id="{{{ data.page_id }}}" />
                            </p>
                            <table class="op-pageviews-list-table">
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
                                        <td>
                                            <h3 id="op-stats-page-sum-views">{{{ data.views }}}</h3>
                                        </td>
                                        <td>
                                            <h3 id="op-stats-page-sum-unique">{{{ data.unique }}}</h3>
                                        </td>
                                        <td>
                                            <h3 id="op-stats-page-sum-conversions">{{{ data.conversions }}}</h3>
                                        </td>
                                        <td>
                                            <h3 id="op-stats-page-sum-conversion-rate">{{{ data.conversion_rate }}}%</h3>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <canvas id="page_views"></canvas>
                        </div>
                    </div>
                </div>
            </form>
        </script>

        <script type="text/html" id="tmpl-op-stats-page-list-row">
            <tr>
                <td class="op-align-left">
                    <a href="#page-stats" title="{{{ data.title }}}" class="op-stats-pageviews-page-details" data-page-id="{{{ data.id }}}">{{{ data.title }}}</a>
                </td>
                <td>
                    <a class="op-pageview-stats-preview-icon" href="{{{ data.view_link }}}" target="_blank" title="{{{ data.title }}}"></a>
                </td>
                <td>{{{ data.stats.views }}}</td>
                <td>{{{ data.stats.unique }}}</td>
                <td>{{{ data.stats.conversions }}}</td>
                <td>{{{ data.stats.conversion_rate }}}%</td>
            </tr>
        </script>

        <script type="text/html" id="tmpl-op-stats-page-list-no-data">
            <tr>
                <td colspan="6">
                    <p><?php _e('There is no data for defined period.', 'optimizepress-stats'); ?>
                </td>
            </tr>
        </script>

        <script type="text/html" id="tmpl-op-stats-page-list-pagination">
            <div class="tablenav bottom">
                <div class="tablenav-pages">
                    <span class="pagination-links">
                        <# if (data.currentPage === 1) { #>
                            <span class="tablenav-pages-navspan" aria-hidden="true">«</span>
                            <span class="tablenav-pages-navspan" aria-hidden="true">‹</span>
                        <# } else { #>
                            <a class="first-page" href="#1" data-page="1"><span aria-hidden="true">«</span></a>
                            <a class="prev-page" href="#{{ parseInt(data.currentPage - 1) }}" data-page="{{ parseInt(data.currentPage - 1) }}"><span aria-hidden="true">‹</span></a>
                        <# } #>
                        <span id="table-paging" class="paging-input">
                            <span class="tablenav-paging-text">{{ data.currentPage }} <?php _ex('of', 'number of pages (eg. 1 of 3)', 'optimizepress-stats'); ?> <span class="total-pages">{{ data.totalPages }}</span></span>
                        </span>
                        <# if (data.currentPage !== data.totalPages) { #>
                            <a class="next-page" href="#{{ parseInt(data.currentPage + 1) }}" data-page="{{ parseInt(data.currentPage + 1) }}"><span aria-hidden="true">›</span></a>
                            <a class="last-page" href="#{{ data.totalPages }}" data-page="{{ data.totalPages }}"><span aria-hidden="true">»</span></a>
                        <# } else { #>
                            <span class="tablenav-pages-navspan" aria-hidden="true">›</span>
                            <span class="tablenav-pages-navspan" aria-hidden="true">»</span>
                        <# } #>
                    </span>
                </div>
            </div>
        </script>
<?php
    }

    /**
     * Enqueue stats UI script on stats page.
     * @param  string $hook
     * @return void
     */
    public function enqueueScripts($hook)
    {
        if ($hook !== 'optimizepress_page_optimizepress-statistics' || ! isset($_GET['section']) || $_GET['section'] !== 'stats') {
            return;
        }

        wp_enqueue_script('moment', OP_S_BASE_URL . 'js/moment.min.js', false, '2.12.0', true);
        wp_enqueue_script('jquery-daterangepicker', OP_S_BASE_URL . 'js/jquery.daterangepicker' . OP_SCRIPT_DEBUG . '.js', array('moment', 'jquery'), '0.1.0', true);

        wp_enqueue_script('optimizepress-stats-pageview-stats-ui', OP_S_BASE_URL . 'js/pageview-stats-ui' . OP_SCRIPT_DEBUG . '.js', array('jquery'), OP_S_VERSION, true);

        wp_enqueue_style('jquery-daterangepicker', OP_S_BASE_URL . 'css/daterangepicker' . OP_SCRIPT_DEBUG . '.css', false, false, false);
    }

    /**
     * Render pageviews subpage.
     * @param  string $activeSection
     * @return void
     */
    public function renderPageViewsPage($activeSection)
    {
        if ('stats' !== $activeSection) {
            return;
        }

        ?>
        <div class="op-info-box op-experiments-stats-main-content">
            <div class="op-stats-optins-container op-stats-section-container">
                <?php $this->renderOptins(); ?>
            </div>
            <div class="op-stats-pageviews-container op-stats-section-container">
                <?php $this->renderPageViews(); ?>
            </div>
            <div class="cf"></div>
        </div>
        <?php
    }

    /**
     * Render optin stats.
     * @return void
     */
    protected function renderOptins()
    {
        ?>
             <h5><?php _e('Total Optins from OptimizePress', 'optimizepress-stats'); ?></h5>

            <div class="op-optin-total">
                <h2><?php printf('%s', number_format_i18n(op_optin_stats_get_local_total_count())); ?></h2>
                <small><?php printf(__('(since %s)', 'optimizepress'), date_i18n(get_option('date_format'), strtotime(OptimizePress_Optin_Stats::SINCE_DATE))); ?></small>
            </div>

            <?php if ($data = op_optin_stats_get_local_data()) : ?>
            <div class="op-optin-monthly">
                <h4><?php _e('Optins per month', 'optimizepress-stats'); ?></h4>
                <canvas id="op-optins-monthly-chart"></canvas>
                <script type="text/javascript">
                    (function($) {
                        $(document).ready(function() {
                            var context = jQuery('#op-optins-monthly-chart').get(0).getContext('2d');
                            var optinChart = new Chart(context, {
                                type: 'bar',
                                data: {
                                    labels: <?php echo json_encode(array_keys($data)); ?>,
                                    datasets: [{
                                        label: "<?php _e('Optins', 'optimizepress-stats'); ?>",
                                        backgroundColor: OpChartOptions.colors.light[0],
                                        borderColor: OpChartOptions.colors.dark[0],
                                        borderWidth: 2,
                                        data: <?php echo json_encode(array_values($data)); ?>,
                                        lineTension: 0
                                    }]
                                },
                                options: {
                                    animation: false,
                                    scales: {
                                        yAxes: [{
                                            ticks: {
                                                beginAtZero: true
                                            }
                                        }]
                                    }
                                }
                            });
                        });
                    }(opjq));
                </script>
            </div>
            <?php endif; ?>
        <?php
    }

    /**
     * Render pageview table.
     * @return void
     */
    protected function renderPageViews()
    {
        $startDate  = date('Y-m-d', strtotime('-1 month'));
        $endDate    = date('Y-m-d');

        $data = $this->getPageviews($startDate, $endDate, 0, $this->itemsPerPage);
        ?>
             <h5><?php _e('Pageviews', 'optimizepress-stats'); ?></h5>
             <p>
                <strong><?php _e('Showing stats for:', 'optimizepress-stats'); ?></strong>
                <input type="text" name="daterange" id="op-stats-list-daterange" value="<?php echo $startDate . ' - ' . $endDate; ?>" />
            </p>
            <table id="op-stats-pageviews" class="op-pageviews-list-table">
                <thead>
                    <tr>
                        <th class="op-align-left"><?php _e('Page', 'optimizepress-stats'); ?></th>
                        <th><?php _e('Preview', 'optimizepress-stats'); ?></th>
                        <th><?php _e('Views', 'optimizepress-stats'); ?></th>
                        <th><?php _e('Unique views', 'optimizepress-stats'); ?></th>
                        <th><?php _e('Conversions', 'optimizepress-stats'); ?></th>
                        <th><?php _e('Conversion rate', 'optimizepress-stats'); ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php if (isset($data['total']) && ! empty($data['total'])) : ?>
                    <?php foreach ($data['items'] as $item) : ?>
                        <tr>
                            <td class="op-align-left">
                                <a href="#page-stats" class="op-stats-pageviews-page-details" data-page-id="<?php echo esc_attr($item['id']); ?>"><?php echo $item['title']; ?></a>
                            </td>
                            <td>
                                <a class="op-pageview-stats-preview-icon" href="<?php echo esc_url($item['view_link']); ?>" target="_blank" title="<?php echo esc_attr($item['title']); ?>"></a>
                            </td>
                            <td><?php echo $item['stats']['views']; ?></td>
                            <td><?php echo $item['stats']['unique']; ?></td>
                            <td><?php echo $item['stats']['conversions']; ?></td>
                            <td><?php echo $item['stats']['conversion_rate']; ?>%</td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="6">
                            <p><?php _e('There is no data for defined period.', 'optimizepress-stats'); ?>
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        <?php

        if (isset($data['total']) && $data['total'] > $this->itemsPerPage) {
            $this->renderPagination(1, (int) ceil($data['total'] / $this->itemsPerPage));
        }
    }

    /**
     * Render pagination markup.
     * @param  integer $currentPage
     * @param  integer $totalPages
     * @return void
     */
    protected function renderPagination($currentPage, $totalPages)
    {
        ?>
        <div class="tablenav bottom">
            <div class="tablenav-pages">
                <span class="pagination-links">
                    <?php if ($currentPage === 1) : ?>
                        <span class="tablenav-pages-navspan" aria-hidden="true">«</span>
                        <span class="tablenav-pages-navspan" aria-hidden="true">‹</span>
                    <?php else : ?>
                        <a class="first-page" href="#1" data-page="1"><span aria-hidden="true">«</span></a>
                        <a class="prev-page" href="#<?php echo esc_attr($currentPage - 1); ?>" data-page="<?php echo esc_attr($currentPage - 1); ?>"><span aria-hidden="true">‹</span></a>
                    <?php endif; ?>
                    <span id="table-paging" class="paging-input">
                        <span class="tablenav-paging-text"><?php printf(__('%d of <span class="total-pages">%d</span>', 'optimizepress-stats'), $currentPage, $totalPages); ?></span>
                    </span>
                    <?php if ($currentPage !== $totalPages) : ?>
                        <a class="next-page" href="#<?php echo esc_attr($currentPage + 1); ?>" data-page="<?php echo esc_attr($currentPage + 1); ?>"><span aria-hidden="true">›</span></a>
                        <a class="last-page" href="#<?php echo esc_attr($totalPages); ?>" data-page="<?php echo esc_attr($totalPages); ?>"><span aria-hidden="true">»</span></a>
                    <?php else : ?>
                        <span class="tablenav-pages-navspan" aria-hidden="true">›</span>
                        <span class="tablenav-pages-navspan" aria-hidden="true">»</span>
                    <?php endif; ?>
                </span>
            </div>
        </div>
        <?php
    }

    /**
     * Return formatted page views.
     * @param   string $startDate expects date in Y-m-d format
     * @param   string $endDate expects date in Y-m-d format
     * @param   integer $offset
     * @param   integer $limit
     * @return  array It returns array if normal PHP request. If called with AJAX it will output JSON
     */
    public function getPageviews($startDate = null, $endDate = null, $offset = 0, $limit = null)
    {
        // Override default values from AJAX request
        if (isset($_GET['start_date']) && ! empty($_GET['start_date'])) {
            $startDate = date('Y-m-d', strtotime(sanitize_text_field($_GET['start_date'])));
        } else if (null === $startDate) {
            $startDate = date('Y-m-d', strtotime('-1 month'));
        }

        if (isset($_GET['end_date']) && ! empty($_GET['end_date'])) {
            $endDate = date('Y-m-d', strtotime(sanitize_text_field($_GET['end_date'])));
        } else if (null === $endDate) {
            $endDate = date('Y-m-d');
        }

        if (isset($_GET['offset']) && ! empty($_GET['offset'])) {
            $offset = (int) $_GET['offset'];
        }

        if (isset($_GET['limit']) && ! empty($_GET['limit'])) {
            $limit = (int) $_GET['limit'];
        } else if (null === $limit) {
            $limit = $this->itemsPerPage;
        }

        // Fetching stats data count
        $count = $this->viewsRepository->getAggregatedViewsCount($startDate, $endDate);

        // Return early if there is no data
        if ($count < 1) {
            if (defined('DOING_AJAX')) {
                wp_send_json_error(array('code' => 404, 'message' => __('There is no data for defined period.', 'optimizepress-stats')));
            }

            return array();
        }

        // Fetching stats data
        $rawData = $this->viewsRepository->getAggregatedViewsSum($startDate, $endDate, $offset, $limit);

        // Get page IDS for WP_Query
        $postIds = array_keys($rawData);

        $posts = new WP_Query(array(
            'post_type'         => 'page',
            'posts_per_page'    => -1,
            'post_status'       => 'publish',
            'orderby'           => 'post__in',
            'post__in'          => $postIds,
        ));

        // Return if no posts have been found
        if ( ! $posts->have_posts()) {
            if (defined('DOING_AJAX')) {
                wp_send_json_error(array('code' => 404, 'message' => __('There is no data for defined period.', 'optimizepress-stats')));
            }

            return array();
        }

        $stats = array();

        // We need to format data for consumption by view (PHP array and JSON)
        while ($posts->have_posts()) {
            $posts->the_post();
            $stats[] = array(
                'id' => get_the_ID(),
                'title' => get_the_title(),
                'view_link' => get_permalink(),
                'edit_link' => get_edit_post_link(),
                'stats' => isset($rawData[get_the_ID()])
                    ? array(
                        'views'             => number_format_i18n($rawData[get_the_ID()]->views),
                        'unique'            => number_format_i18n($rawData[get_the_ID()]->unique),
                        'conversions'       => number_format_i18n($rawData[get_the_ID()]->conversions),
                        'conversion_rate'   => (int) $rawData[get_the_ID()]->views !== 0 ? number_format_i18n(round($rawData[get_the_ID()]->conversions / $rawData[get_the_ID()]->views * 100, 2), 2) : 0,
                    )
                    : array()
            );
        }

        if (defined('DOING_AJAX')) {
            wp_send_json_success(array(
                'total' => $count,
                'items' => $stats
            ));
        }

        return array(
            'total' => $count,
            'items' => $stats
        );
    }
}

new OptimizePressStats_Screen_PageViews;