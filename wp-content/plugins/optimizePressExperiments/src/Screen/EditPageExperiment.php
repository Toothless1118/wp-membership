<?php

/**
 * Edit page experiment UI
 */
class OptimizePressStats_Screen_EditPageExperiment
{
    /**
     * @var OptimizePressStats_Repository_Experiments
     */
    protected $experimentsRepository;

    /**
     * @var OptimizePressStats_Repository_Variants
     */
    protected $variantsRepository;

    /**
     * Attach to WordPress hooks and initialize repositories.
     */
    public function __construct()
    {
        add_action('admin_enqueue_scripts', array($this, 'enqueueScripts'));
        add_action('op_pagebuilder_container_items_after', array($this, 'experimentButton'));

        $this->variantsRepository       = new OptimizePressStats_Repository_Variants;
        $this->experimentsRepository    = new OptimizePressStats_Repository_Experiments;
    }

    /**
     * Show start/edit experiment on edit page screen (in pagebuilder container section).
     * @return void
     */
    public function experimentButton()
    {
        global $post;

        $variant = $this->variantsRepository->getVariantForPage($post->ID);

        // If this is a variant of existing experiment we won't show anything
        if (null !== $variant && $variant->page_id === $post->ID && $variant->page_id !== $variant->original_page_id) {
            return;
        }

        $experiment = $this->experimentsRepository->getExperimentForPage($post->ID);
        ?>
        <li>
            <img src="<?php echo OP_S_BASE_URL; ?>images/icon-pb-beaker.png" height="100" width="100" />
            <div class="page-builder-indent">
                <h4><?php _e('Experiment', 'optimizepress-stats'); ?></h4>
                <p><?php _e('Manage A/B experiment on this page.', 'optimizepress-stats'); ?></p>
                <a href="#add-experiment" data-page-id="<?php echo esc_attr($post->ID); ?>" class="op-experiments-new-experiment"<?php if (null !== $experiment) echo ' style="display:none;"' ?>><?php _e('Create', 'optimizepress-stats'); ?></a>
                <a href="#edit-experiment" data-experiment-id="<?php echo esc_attr(null !== $experiment ? $experiment->id : 0); ?>" class="op-experiments-edit-experiment"<?php if (null === $experiment) echo ' style="display:none;"' ?>><?php _e('Edit', 'optimizepress-stats'); ?></a>
                <span class="op-experiments-experiment-status"<?php if (null === $experiment) echo ' style="display:none;"' ?>><?php echo $this->renderExperimentStatus($experiment); ?></span>
            </div>
        </li>
        <?php
    }

    /**
     * Enqueue experiments UI script on stats page.
     * @param  string $hook
     * @return void
     */
    public function enqueueScripts($hook)
    {
        if ($hook !== 'optimizepress_page_optimizepress-stats' && $hook !== 'post.php') {
            return;
        }

        wp_enqueue_script('moment', OP_S_BASE_URL . 'js/moment.min.js', false, '2.12.0', true);
        wp_enqueue_script('jquery-daterangepicker', OP_S_BASE_URL . 'js/jquery.daterangepicker' . OP_SCRIPT_DEBUG . '.js', array('moment', 'jquery'), '0.1.0', true);

        wp_enqueue_script('optimizepress-stats-experiments-ui', OP_S_BASE_URL . 'js/experiments-ui' . OP_SCRIPT_DEBUG . '.js', array('jquery', 'jquery-ui-autocomplete'), OP_S_VERSION, true);

        wp_enqueue_style('jquery-daterangepicker', OP_S_BASE_URL . 'css/daterangepicker' . OP_SCRIPT_DEBUG . '.css', false, false, false);
    }

    /**
     * Render experiment status UI.
     * @param  object $experiment
     * @return string
     */
    protected function renderExperimentStatus($experiment)
    {
        if (null === $experiment) {
            $experiment = new stdClass;

            $experiment->id     = 0;
            $experiment->status = 0;
        }

        switch ((int) $experiment->status) {
            case 2:
                $label = __('Live', 'optimizepress-stats');
                $icon  = '<span class="dashicons dashicons-controls-pause"></span>';
                break;
            default:
                $label = __('Paused', 'optimizepress-stats');
                $icon  = '<span class="dashicons dashicons-controls-play"></span>';
        }

        return sprintf('<a href="#switch-experiment-status" class="op-experiments-switch-status" data-experiment-id="%3$s" data-status="%1$s">%2$s</a>', $experiment->status, $label . $icon, $experiment->id);
    }
}

new OptimizePressStats_Screen_EditPageExperiment;