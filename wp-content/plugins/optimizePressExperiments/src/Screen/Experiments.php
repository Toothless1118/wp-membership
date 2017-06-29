<?php

class OptimizePressStats_Screen_Experiments
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
     * @var array
     */
    protected $experiments;

    /**
     * @var array
     */
    protected $stats = array();

    /**
     * @var array
     */
    protected $variants = array();

    public function __construct()
    {
        add_action('admin_enqueue_scripts', array($this, 'enqueueScripts'));
        add_action('print_media_templates', array($this, 'printTemplates'));
        add_action('optimizepress_stats/statistics_page', array($this, 'renderExperimentsPage'));

        // Loading ChartJS lib and enqueueing colors data
        $this->charting = OptimizePressStats_Charting_Helper::getInstance();

        // Loading repos
        $this->viewsRepository          = new OptimizePressStats_Repository_Views;
        $this->variantsRepository       = new OptimizePressStats_Repository_Variants;
        $this->experimentsRepository    = new OptimizePressStats_Repository_Experiments;
    }

    /**
     * Enqueue styles and scripts.
     * @param  string $hook
     * @return void
     */
    public function enqueueScripts($hook)
    {
        if ('optimizepress_page_optimizepress-statistics' !== $hook || (isset($_GET['section']) && 'experiments' !== $_GET['section'])) {
            return;
        }

        wp_enqueue_script('moment', OP_S_BASE_URL . 'js/moment.min.js', false, '2.12.0', true);
        wp_enqueue_script('jquery-daterangepicker', OP_S_BASE_URL . 'js/jquery.daterangepicker' . OP_SCRIPT_DEBUG . '.js', array('moment', 'jquery'), '0.1.0', true);
        wp_enqueue_script('select2', OP_S_BASE_URL . 'js/select2.min.js', array('jquery'), '4.0.3', true);

        wp_enqueue_script('optimizepress-stats-experiments-ui', OP_S_BASE_URL . 'js/experiments-ui' . OP_SCRIPT_DEBUG . '.js', array('jquery', 'select2'), OP_S_VERSION, true);

        wp_localize_script('optimizepress-stats-experiments-ui', 'OPSE', array(
            'l10n' => array(
                'are_you_sure_you_want_to_delete_experiment' => __('Are you sure you want to delete experiment?', 'optimizepress-stats'),
                'select_page' => __('Select a page', 'optimizepress-stats'),
            )
        ));

        wp_enqueue_script('optimizepress-stats-experiment-stats-ui', OP_S_BASE_URL . 'js/experiment-stats-ui' . OP_SCRIPT_DEBUG . '.js', array('jquery'), OP_S_VERSION, true);

        wp_enqueue_style('jquery-daterangepicker', OP_S_BASE_URL . 'css/daterangepicker' . OP_SCRIPT_DEBUG . '.css', array(), '4.0.3', 'all');
        wp_enqueue_style('select2', OP_S_BASE_URL . 'css/select2.min.css', array(), '4.0.3', 'all');
    }

    /**
     * Print templates.
     * @return void
     */
    public function printTemplates()
    {
        $screen = get_current_screen();

        if ($screen->id !== 'optimizepress_page_optimizepress-statistics' && $screen->id !== 'page'
        || (isset($_GET['section']) && 'experiments' !== $_GET['section'])) {
            return;
        }

        $startDate  = date('Y-m-d', strtotime('-1 month'));
        $endDate    = date('Y-m-d');
    ?>
        <script type="text/html" id="tmpl-op-experiment-table">
            <table class="op-experiments-list-table" id="op-experiments-table">
                <thead>
                    <tr>
                        <th class="op-align-left"><?php _e('Type', 'optimizepress-stats'); ?></th>
                        <th class="op-align-left"><?php _e('Test Name', 'optimizepress-stats'); ?></th>
                        <th><?php _e('Best Conversion', 'optimizepress-stats'); ?></th>
                        <th><?php _e('Total Visitors', 'optimizepress-stats'); ?></th>
                        <th><?php _e('Status', 'optimizepress-stats'); ?></th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </script>

        <script type="text/html" id="tmpl-op-experiment-add">
            <a class="op-experiments-new-experiment op-experiments-btn-primary" href="#new-experiment"><?php _e('Create new experiment', 'optimizepress-stats'); ?></a>
        </script>

        <script type="text/html" id="tmpl-op-experiment-row">
            <tr data-experiment-id="{{{ data.experiment_id }}}">
                <td class="op-align-left">A/B Test</td>
                <td class="op-align-left">
                    <a href="#edit-experiment" class="op-experiments-edit">{{{ data.experiment_name }}}</a>
                </td>
                <td>0%</td>
                <td>0</td>
                <td class="op-experiments-experiment-status-label"></td>
                <td class="op-experiments-experiment-status-button"></td>
                <td class="op-experiments-delete-experiment"><a href="#delete-experiment" class="dashicons dashicons-trash op-delete-experiment"></a></td>
            </tr>
        </script>

        <script type="text/html" id="tmpl-op-experiment-edit">
            <form id="op-experiments-experiment-form">
                <h1 class="op-experiment-popup">{{{ data.form_title }}}</h1>
                <div class="op-lightbox-content">
                    <div class="op-actual-lightbox-content">
                        <div class="settings-container">
                            <div id="{{{ data.container_class }}}">
                                <input type="hidden" name="op[sections][experiments][experiment_id]" id="op_sections_experiments_experiment_id" value="0" />
                                <input type="hidden" name="op[sections][experiments][experiment_status]" id="op_sections_experiments_experiment_status" value="0" />
                                <h2 class="op-experiment-popup-headline"><?php _e('Experiment information', 'optimizepress-stats'); ?></h2>
                                <hr />
                                <label for="op_sections_experiments_experiment_name" class="form-title"><?php _e('Name your experiment (for your reference only)', 'optimizepress-stats'); ?></label>
                                <input type="text" name="op[sections][experiments][experiment_name]" id="op_sections_experiments_experiment_name" value="" />


                                <label for="op_sections_experiments_original_page" class="form-title"><?php _e('Original page', 'optimizepress-stats'); ?></label>
                                <input type="hidden" class="op-experiments-page-id" name="op[sections][experiments][original_page_id]" id="op_sections_experiments_original_page_id" value="" />
                                <select name="op[sections][experiments][original_page]" id="op_sections_experiments_original_page" class="op-experiments-original-page-autocomplete">
                                    <option></option>
                                </select>

                                <div class="op-experiment-popup-left-column">
                                    <label for="op_sections_experiments_start_date" class="form-title"><?php _e('Start date', 'optimizepress-stats'); ?></label>
                                    <input type="text" class="op-experiments-datepicker" id="op_sections_experiments_start_date" />
                                </div>
                                <div class="op-experiment-popup-right-column">
                                    <label for="op_sections_experiments_end_date" class="form-title"><?php _e('End date', 'optimizepress-stats'); ?></label>
                                    <input type="text" class="op-experiments-datepicker" id="op_sections_experiments_end_date" />
                                </div>

                                <h2 class="op-experiment-popup-headline op-pull-left"><?php _e('Variations', 'optimizepress-stats'); ?></h2>
                                <a href="#new-variant" class="op-experiments-new-variant op-experiments-btn-primary"><?php _e('Add variant', 'optimizepress-stats'); ?></a>
                                <a href="#new-clone-variant" class="op-experiments-new-clone-variant op-experiments-btn-primary"><?php _e('Clone original', 'optimizepress-stats'); ?></a>
                                <hr style="clear: both;" />
                                <div class="op-experiments-variants">
                                    <div class="op-experiments-variant">
                                        <label class="form-title"><?php _e('Variation name', 'optimizepress-stats'); ?></label>
                                        <input type="text" name="op[sections][experiments][variation_label][]" class="op-experiments-page-label" value="" />

                                        <label class="form-title"><?php _e('Variation page', 'optimizepress-stats'); ?></label>
                                        <input type="hidden" class="op-experiments-page-id" name="op[sections][experiments][variation_page_id][]" value="" />
                                        <select name="op[sections][experiments][variation_page][]" class="op-experiments-variant-page-autocomplete">
                                            <option></option>
                                        </select>

                                        <a href="#delete-variation" class="op-experiments-variant-delete"><img src="<?php echo OP_IMG . '/remove-row.png'; ?>" alt="<?php esc_attr_e('Remove Row', 'optimizepress-stats'); ?>"></a>
                                    </div>
                                </div>

                                <h2 class="cf"><?php _e('Goal', 'optimizepress-stats'); ?></h2>
                                <label for="op_sections_experiments_goal_type" class="form-title"><?php _e('Goal type', 'optimizepress-stats'); ?></label>
                                <select name="op[sections][experiments][goal_type]" id="op_sections_experiments_goal_type" class="op-experiments-form-select">
                                    <option value="optin"><?php _e('Optin', 'optimizepress-stats'); ?></option>
                                    <option value="visit"><?php _e('Page visit', 'optimizepress-stats'); ?></option>
                                </select>

                                <label for="op_sections_experiments_goal_page" class="form-title op-experiment-goal-visit"><?php _e('Goal page', 'optimizepress-stats'); ?></label>
                                <input type="hidden" name="op[sections][experiments][goal_page_id]" id="op_sections_experiments_goal_page_id" value="0" />
                                <select name="op[sections][experiments][goal_page]" id="op_sections_experiments_goal_page" class="op-experiment-goal-visit">
                                    <option></option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="op-insert-button cf" style="">
                    <button type="button" name="submit" class="editor-button op-experiments-save-experiment"><span>{{{ data.action_label }}}</span></button>
                </div>
            </form>
        </script>

        <script type="text/html" id="tmpl-op-experiment-no-experiments">
            <div class="op-experiments-no-experiment">
                <h2><?php _e('Create an experiment now', 'optimizepress-stats'); ?></h2>
                <p><?php _e("It's time to get started, set up your next experiment and optimize your conversion rates", 'optimizepress-stats'); ?></p>
                <hr />
                <a class="op-experiments-new-experiment op-experiments-btn-primary" href="#new-experiment"><?php _e('Create new experiment', 'optimizepress-stats'); ?></a>
            </div>
        </script>

        <script type="text/html" id="tmpl-op-stats-experiment-details">
            <form id="op-stats-experiment-details">
                <h1 class="op-experiment-popup">{{{ data.experiment.name }}}</h1>
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
     * Render experiments page. Hooks onto optimizepress_stats/statistics_page.
     * @return void
     */
    public function renderExperimentsPage($activeSection)
    {
        if ($activeSection !== 'experiments') {
            return;
        }

        $experiments = $this->getExperiments();
        ?>
            <div class="op-info-box op-experiments-stats-main-content">
                <?php if (count($experiments)) : ?>
                    <?php
                        $this->renderExperimentsTable($experiments);
                        $this->renderExperimentsStats($experiments);
                    ?>
                <?php else : ?>
                    <div class="op-experiments-no-experiment">
                        <h2><?php _e('Create an experiment now', 'optimizepress-stats'); ?></h2>
                        <p><?php _e("It's time to get started, set up your next experiment and optimize your conversion rates", 'optimizepress-stats'); ?></p>
                        <hr />
                        <a class="op-experiments-new-experiment op-experiments-btn-primary" href="#new-experiment"><?php _e('Create new experiment', 'optimizepress-stats'); ?></a>
                    </div>
                <?php endif; ?>
            </div>
        <?php
    }

    /**
     * Render experiments table.
     * @param  array $experiments
     * @return void
     */
    protected function renderExperimentsTable($experiments)
    {
?>
<table class="op-experiments-list-table" id="op-experiments-table">
    <thead>
        <tr>
            <th class="op-align-left"><?php _e('Type', 'optimizepress-stats'); ?></th>
            <th class="op-align-left"><?php _e('Test Name', 'optimizepress-stats'); ?></th>
            <th><?php _e('Best Conversion', 'optimizepress-stats'); ?></th>
            <th><?php _e('Total Visitors', 'optimizepress-stats'); ?></th>
            <th><?php _e('Status', 'optimizepress-stats'); ?></th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
        <?php
            $index = 1;
            foreach ($experiments as $experiment) :
                list($totalViews, $originalConversionRate, $bestAlternativeConversionRate) = $this->charting->getBasicExperimentStats($this->getExperimentStats($experiment->id));
        ?>
        <tr data-experiment-id="<?php echo esc_attr($experiment->id); ?>">
            <td class="op-align-left">A/B Test</td>
            <td class="op-align-left">
                <a href="#edit-experiment" class="op-experiments-edit"><?php echo $experiment->name; ?></a>
            </td>
            <td><?php echo number_format_i18n($originalConversionRate > $bestAlternativeConversionRate ? $originalConversionRate : $bestAlternativeConversionRate, 2); ?>%</td>
            <td><?php echo number_format_i18n($totalViews, 0); ?></td>
            <td class="op-experiments-experiment-status-label"><?php echo $this->renderExperimentStatusLabel($experiment); ?></td>
            <td class="op-experiments-experiment-status-button"><?php echo $this->renderExperimentStatusButton($experiment); ?></td>
            <td class="op-experiments-delete-experiment"><a href="#delete-experiment" class="dashicons dashicons-trash op-delete-experiment"></a></td>
            <?php if ($index === 1) : ?>
                <td rowspan="<?php echo count($experiments); ?>">
                    <a class="op-experiments-new-experiment op-experiments-btn-primary" href="#new-experiment"><?php _e('Create new experiment', 'optimizepress-stats'); ?></a>
                </td>
            <?php endif; ?>
        </tr>
        <?php
            $index++;
            endforeach;
        ?>
    </tbody>
</table>
<?php
    }

    protected function renderExperimentsStats($experiments)
    {
?>
<div id="op-experiment-stats">
    <?php
        $index = 1;
        foreach ($experiments as $experiment) :
    ?>
        <?php
            $variants = $this->getExperimentVariants($experiment->id);
            $stats = $this->charting->manuallySort(
                $this->getExperimentStats($experiment->id),
                wp_list_pluck($variants, 'page_id')
            );

            list($totalViews, $originalConversionRate, $bestAlternativeConversionRate) = $this->charting->getBasicExperimentStats($stats);

            $data = array(
                'stats' => $this->charting->formatExperimentConversionRates($stats),
                'labels' => wp_list_pluck($variants, 'name'),
                'backgroundColor' => $this->charting->getColors('1.0'),
                'hoverBackgroundColor' => $this->charting->getColors('0.2'),
            );
        ?>
            <div class="op-experiment-overview" id="op-experiment-charts-<?php echo esc_attr($experiment->id); ?>">
                <h2><a href="#experiment-details" class="op-stats-experiment-details-trigger" data-experiment-id="<?php echo esc_attr($experiment->id); ?>"><?php echo $experiment->name; ?></a></h2>
                <?php if ( ! empty($experiment->description)) : ?>
                    <p><?php echo $experiment->description; ?></p>
                <?php endif; ?>
                <?php if (count($data['stats']) > 1) : ?>
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
                                <h4><?php _e('Conversion rates (%)', 'optimizepress-stats'); ?></h4>
                                <canvas id="op-experiment-chart-<?php echo $experiment->id; ?>" class="op-experiment-variants-pie-chart"></canvas>
                                <script type="text/javascript">
                                    (function($) {
                                        $(document).ready(function() {
                                            var context = jQuery('#op-experiment-chart-<?php echo $experiment->id; ?>').get(0).getContext('2d');
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
                                                    animated: false
                                                }
                                            });
                                        });
                                    }(opjq));
                                </script>
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
                <?php elseif (count($data['stats']) === 1) : ?>
                    <p class="op-experiments-no-experiment-data">
                        <?php _e('Insufficient conversion data for this experiment!', 'optimizepress-stats'); ?>
                    </p>
                <?php else : ?>
                    <p class="op-experiments-no-experiment-data">
                        <?php _e('There are no conversions yet for this experiment!', 'optimizepress-stats'); ?>
                    </p>
                <?php endif; ?>
            </div>
            <?php if ($index % 2 === 0) : ?>
                <div class="cf"></div>
            <?php endif; ?>
    <?php
        $index++;
        endforeach;
    ?>
</div>
<?php
    }

    /**
     * Return markup for experiment status button.
     * @param  object $experiment
     * @return string
     */
    protected function renderExperimentStatusButton($experiment)
    {
        return sprintf('<a href="#switch-experiment-status" class="op-experiments-switch-status op-experiment-status-icon op-experiment-status-icon-%1$s" data-experiment-id="%2$s" data-status="%1$s"></a>', $experiment->status, $experiment->id);
    }

    /**
     * Return markup for experiment status label.
     * @param  object $experiment
     * @return string
     */
    protected function renderExperimentStatusLabel($experiment)
    {
        if (null === $experiment) {
            return;
        }

        switch ((int) $experiment->status) {
            case 2:
                $label = __('Live', 'optimizepress-stats');
                break;
            default:
                $label = __('Paused', 'optimizepress-stats');
        }

        return sprintf('<span class="op-experiment-status-%1$s" data-experiment-id="%3$s" data-status="%1$s">%2$s</span>', $experiment->status, $label, $experiment->id);
    }

    /**
     * Return all experiments (lazy DB load).
     * @return array
     */
    protected function getExperiments()
    {
        if (null === $this->experiments) {
            $this->experiments = $this->experimentsRepository->getExperiments();
        }

        return $this->experiments;
    }

    /**
     * Return all stats for given experiment (lazy DB load).
     * @param  integer $experimentId
     * @return array
     */
    protected function getExperimentStats($experimentId)
    {
        if ( ! isset($this->stats[$experimentId])) {
            $this->stats[$experimentId] = $this->viewsRepository->getExperimentViewsSum($experimentId);
        }

        return $this->stats[$experimentId];
    }

    /**
     * Return all variants for given experiment (lazy DB load).
     * @param  integer $experimentId
     * @return array
     */
    protected function getExperimentVariants($experimentId)
    {
        if ( ! isset($this->variants[$experimentId])) {
            $this->variants[$experimentId] = $this->variantsRepository->getVariantsForExperiment($experimentId);
        }

        return $this->variants[$experimentId];
    }
}

new OptimizePressStats_Screen_Experiments;