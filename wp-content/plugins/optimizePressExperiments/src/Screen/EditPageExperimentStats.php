<?php

/**
 * Experiment stats UI for page edit screen
 */
class OptimizePressStats_Screen_EditPageExperimentStats
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
     * @var OptimizePressStats_Repository_Variants
     */
    protected $variantsRepository;

    /**
     * @var OptimizePressStats_Repository_Experiments
     */
    protected $experimentRepository;

    /**
     * Hook into WP and init repositories.
     */
    public function __construct()
    {
        add_action('edit_page_form', array($this, 'showExperimentStats'), 30);
        add_action('admin_enqueue_scripts', array($this, 'enqueueScripts'));
        add_action('print_media_templates', array($this, 'printTemplates'));

        // Loading ChartJS lib and enqueueing colors data
        $this->charting = OptimizePressStats_Charting_Helper::getInstance();

        $this->viewsRepository = new OptimizePressStats_Repository_Views;
        $this->variantsRepository = new OptimizePressStats_Repository_Variants;
        $this->experimentRepository = new OptimizePressStats_Repository_Experiments;
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

        if ($screen->id !== 'page') {
            return;
        }
?>
        <script type="text/html" id="tmpl-op-stats-experiment-details">
            <form id="op-stats-experiment-details">
                <h1 style="margin-top: 0px;font-family: 'Helvetica Neue', Helvetica, sans-serif !important;font-size: 28px !important;font-weight: bold !important;color: #fff !important;text-shadow: 0 0 1px #000, 0 0 1px #000, 0 1px 1px #000;margin-bottom: 0px;padding: 33px 25px 0px 25px;height: 67px;background: #404040;background: linear-gradient(to bottom, #404040 0%, #2c2c2c 100%);filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#404040', endColorstr='#2c2c2c',GradientType=0 );box-shadow: inset 0 -1px 0 0px rgba(0,0,0,1);border-top: 1px solid #4f4f4f;border-bottom: 1px solid #000000;">{{{ data.experiment.name }}}</h1>
                <div class="op-lightbox-content">
                    <div class="op-actual-lightbox-content">
                        <div class="settings-container">
                            <# if (data.experiment.description.length) { #>
                            <p>{{ data.experiment.description }}</p>
                            <# } #>

                            <h3><?php _e('Alternatives overview', 'optimizepress-stats'); ?></h3>
                            <canvas id="op-experiment-variations-bar" width="750" height="400"></canvas>
                            <div id="op-experiment-variations-bar-legend"></div>

                            <h3><?php _e('Alternatives data', 'optimizepress-stats'); ?></h3>
                            <p>
                                <strong><?php _e('Data type:', 'optimizepress-stats'); ?></strong>
                                <select name="stat" id="op-stats-stat" data-experiment-id="{{ data.experiment.id }}">
                                    <option value="views"><?php _e('Views', 'optimizepress-stats'); ?></option>
                                    <option value="unique"><?php _e('Unique', 'optimizepress-stats'); ?></option>
                                    <option value="conversions" selected><?php _e('Conversions', 'optimizepress-stats'); ?></option>
                                </select>
                            </p>
                            <p>
                                <strong><?php _e('Showing stats for:', 'optimizepress-stats'); ?></strong>
                                <input type="text" name="daterange" id="op-stats-daterange" value="<?php echo $startDate . ' - ' . $endDate; ?>" data-experiment-id="{{ data.experiment.id }}" />
                            </p>

                            <canvas id="op-experiment-variations-line"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </script>
<?php
    }

    /**
     * Show experiment stats
     * @param  WP_Post $post
     * @return void
     */
    public function showExperimentStats($post)
    {
        $screen = get_current_screen();

        // Leave if we aren't on edit page screen
        if ($screen->id !== 'page') {
            return;
        }

        // Check if page is a part of experiment (either runing one or finished)
        $experiment = $this->experimentRepository->getExperimentForVariationPage($post->ID);

        // Page isn't part of any experiment
        if (null === $experiment) {
            return;
        }

        $stats      = $this->viewsRepository->getExperimentViewsSum($experiment->id);
        $variants   = $this->variantsRepository->getVariantsForExperiment($experiment->id);

        // Sorting stats accoring to variants order
        $stats = $this->charting->manuallySort($stats, wp_list_pluck($variants, 'page_id'));

        list($totalViews, $originalConversionRate, $bestAlternativeConversionRate) = $this->charting->getBasicExperimentStats($stats);

        $data = array(
            'stats' => $this->charting->formatExperimentConversionRates($stats),
            'labels' => wp_list_pluck($variants, 'name'),
            'backgroundColor' => $this->charting->getColors('1.0'),
            'hoverBackgroundColor' => $this->charting->getColors('0.2'),
        );

        ?>
        <div id="op-stats-edit-page-experiment-container" class="postbox">
            <h3 class="hndle ui-sortable-handle">
                <span><?php _e('Experiment stats', 'optimizepress-stats'); ?></span>
            </h3>
            <div class="inside">
                <h2>
                    <a href="#experiment-details" class="op-stats-experiment-details-trigger" data-experiment-id="<?php echo esc_attr($experiment->id); ?>"><?php echo $experiment->name; ?></a>
                </h2>
                <?php if ( ! empty($experiment->description)) : ?>
                    <p><?php echo $experiment->description; ?></p>
                <?php endif; ?>

                <table class="op-experiments-experiment-stats">
                    <tr>
                        <td class="op-experiment-total-views" width="25%">
                            <strong><?php echo number_format_i18n($totalViews); ?></strong>
                            <br>
                            <?php _e('visits', 'optimizepress-stats'); ?>
                        </td>
                        <td class="op-experiment-original-conversion-rate" width="25%">
                            <strong><?php echo number_format_i18n($originalConversionRate, 2); ?>%</strong>
                            <br>
                            <?php _e("original's<br />conversion rate", 'optimizepress-stats'); ?>
                        </td>
                        <td rowspan="2" class="op-experiment-conversion-rates">
                            <?php if ($data['stats']) : ?>
                            <h4><?php _e('Conversion rates (%)', 'optimizepress-stats'); ?></h4>
                            <canvas id="op-experiment-chart" width="300" height="300"></canvas>
                            <script type="text/javascript">
                                (function($) {
                                    $(document).ready(function() {
                                        var context = jQuery('#op-experiment-chart').get(0).getContext('2d');
                                        var experimentChart = new Chart(context, {
                                            type: 'pie',
                                            data: {
                                                labels: <?php echo json_encode($data['labels']); ?>,
                                                datasets: [{
                                                    data: <?php echo json_encode($data['stats']); ?>,
                                                    backgroundColor: <?php echo json_encode($data['backgroundColor']); ?>,
                                                }]
                                            },
                                            options: {
                                                responsive: true
                                            }
                                        });
                                    });
                                }(opjq));
                            </script>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="op-experiment-variants">
                            <strong><?php echo count($variants) - 1; ?></strong>
                            <br>
                            <?php _e('alternatives', 'optimizepress-stats'); ?>
                        </td>
                        <td class="op-experiment-best-alternative-conversion-rate">
                            <strong><?php echo number_format_i18n($bestAlternativeConversionRate, 2); ?>%</strong>
                            <br>
                            <?php _e("best alternative's<br />conversion rate", 'optimizepress-stats'); ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <?php
    }

    /**
     * Enqueue stats UI script on stats page.
     * @param  string $hook
     * @return void
     */
    public function enqueueScripts($hook)
    {
        if ($hook !== 'post.php') {
            return;
        }

        wp_enqueue_script('moment', OP_S_BASE_URL . 'js/moment.min.js', false, '2.12.0', true);
        wp_enqueue_script('jquery-daterangepicker', OP_S_BASE_URL . 'js/jquery.daterangepicker' . OP_SCRIPT_DEBUG . '.js', array('moment', 'jquery'), '0.1.0', true);

        wp_enqueue_script('optimizepress-stats-experiment-stats-ui', OP_S_BASE_URL . 'js/experiment-stats-ui' . OP_SCRIPT_DEBUG . '.js', array('jquery'), OP_S_VERSION, true);

        wp_enqueue_style('jquery-daterangepicker', OP_S_BASE_URL . 'css/daterangepicker' . OP_SCRIPT_DEBUG . '.css', false, false, false);
    }
}

new OptimizePressStats_Screen_EditPageExperimentStats;