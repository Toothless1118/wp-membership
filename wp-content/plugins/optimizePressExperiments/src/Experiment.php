<?php

/**
 * Experiment singleton class
 */
class OptimizePressStats_Experiment
{
    const COOKIE_LIFETIME = 86400;

    /**
     * @var OptimizePressStats_Experiment
     */
    protected static $instance = null;

    /**
     * @var int
     */
    protected $experimentId = 0;

    /**
     * @var int
     */
    protected $originalPageId;

    /**
     * @var int
     */
    protected $currentPageId;

    /**
     * @var int
     */
    protected $variationPageId;

    /**
     * @var OptimizePressStats_Repository_Experiments
     */
    protected $experimentsRepository;

    /**
     * @var OptimizePressStats_Repository_Variants
     */
    protected $variantsRepository;

    /**
     * Hook into WP.
     */
    private function __construct()
    {
        add_action('pre_get_posts', array($this, 'switchPage'), 10, 1);
    }

    /**
     * Return experiment object
     *
     * Singleton pattern
     * @return OptimizePressStats_Experiment
     */
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Switch page object.
     * @param  WP_Query $query
     * @return void
     */
    public function switchPage(WP_Query $query)
    {
        $this->currentPageId = $this->getQueriedObjectId($query);

        // We are hijacking only main page query for a page created with OP Live Editor
        if ($query->is_main_query() && $query->is_page() && ! $query->is_admin() && is_le_page($this->currentPageId) && OptimizePressStats_Request::isValid()) {
            $this->variationPageId  = apply_filters(
                'optimizepress_stats/experiment_alternative',
                $this->getAlternativePageId($this->currentPageId),
                $this->currentPageId
            );

            // If current request is from one of the variants we'll redirect to original page
            if ($this->currentPageId !== $this->originalPageId) {
                wp_redirect(get_permalink($this->originalPageId), 302);
                exit();
            }

            // If there is no experiment the values will be the same
            if ($this->currentPageId !== $this->variationPageId) {
                $query->queried_object_id   = $this->variationPageId;
                $query->queried_object      = get_post($this->variationPageId);
            }
        }
    }

    /**
     * Return queried object ID, taking into consideration the page_on_front option.
     * @param  WP_Query $query
     * @return integer
     */
    protected function getQueriedObjectId($query)
    {
        if ($query->is_main_query() && ! $query->is_admin() && (empty($query->query) || $query->get('preview') == 'true')
        && ('page' == get_option('show_on_front')) && get_option('page_on_front')) {
            return (int) get_option('page_on_front');
        }

        return $query->get_queried_object_id();
    }

    /**
     * Return alternative page ID. If page isn't part of any experiment, orignal page ID will be returned.
     * @param  int $pageId
     * @return int
     */
    protected function getAlternativePageId($pageId)
    {
        // Check if there is active experiment for current page
        $experiment = $this->getExperimentsRepository()->getExperimentForVariationPage($pageId, 2);

        // If there is no experiment return the same page ID
        if (null === $experiment) {
            $this->originalPageId = $pageId;
            return $pageId;
        }

        // Save data for later use
        $this->experimentId = (int) $experiment->id;
        $this->originalPageId = (int) $experiment->page_id;

        // Check if we have already shown this experiment to user
        $variantPageId = $this->getChosenVariantFromCookies($experiment->id);

        // If variant is found in cookies we'll show them this one
        if (null !== $variantPageId) {
            // Prolonging chosen variant
            $this->saveChosenVariantToCookies($experiment->id, $variantPageId);

            return $variantPageId;
        }

        // Fetch original and variants
        $variants = $this->getVariantsRepository()->getVariantsForExperiment($experiment->id);

        // Choose one variant (mind you, variant return could be original as well)
        $alternative = $this->chooseVariant($experiment, $variants);

        // Save chosen variant to cookies
        $this->saveChosenVariantToCookies($experiment->id, $alternative->page_id);

        return $alternative->page_id;
    }

    /**
     * Return experiment ID.
     * @return int
     */
    public function getExperimentId()
    {
        return $this->experimentId;
    }

    /**
     * Return original page ID.
     * @return int
     */
    public function getOriginalPageId()
    {
        return $this->originalPageId;
    }

    /**
     * Return variation (substituted) page ID.
     * @return int
     */
    public function getVariationPageId()
    {
        return $this->variationPageId;
    }

    /**
     * Return experiments repository using lazy loading.
     * @return OptimizePressStats_Repository_Experiments
     */
    protected function getExperimentsRepository()
    {
        if (null === $this->experimentsRepository) {
            $this->experimentsRepository = new OptimizePressStats_Repository_Experiments;
        }

        return $this->experimentsRepository;
    }

    /**
     * Return variants repository using lazy loading.
     * @return OptimizePressStats_Repository_Variants
     */
    protected function getVariantsRepository()
    {
        if (null === $this->variantsRepository) {
            $this->variantsRepository = new OptimizePressStats_Repository_Variants;
        }

        return $this->variantsRepository;
    }

    /**
     * Choose a variant based on experiment settings.
     * @param  object $experiment
     * @param  array $variants
     * @return object
     */
    protected function chooseVariant($experiment, $variants)
    {
        $strategyName = apply_filters('optimizepress_stats/strategy_experiment', 'OptimizePressStats_Strategy_Experiment_RoundRobin');
        $strategy = new $strategyName;

        return $strategy->chooseVariant($experiment, $variants);
    }

    /**
     * Check if we have already shown this experiment to user. If we did, chosen variant page ID should be saved to cookie.
     * Uses optimizepress_stats_chosen_variants cookie.
     * @param  integer $experimentId
     * @return integer|null
     */
    protected function getChosenVariantFromCookies($experimentId)
    {
        $variantsData = OptimizePressStats_Cookie::get('chosen_variants');

        // Check if there is a cookie called optimizepress_stats_chosen_variants and if it has the data for given experiment
        if (null === $variantsData) {
            return null;
        } else {
            // Decoding JSON data
            $variantsData = json_decode(stripslashes($variantsData), true);

            if (! is_array($variantsData) || ! isset($variantsData[$experimentId])) {
                return null;
            }
        }

        return $variantsData[$experimentId];
    }

    /**
     * Save chosen experiment variant page ID to cookies. Append it to optimizepress_stats_chosen_variants cookie array.
     * @param  integer $experimentId
     * @param  integer $variantPageId
     * @return boolean
     */
    protected function saveChosenVariantToCookies($experimentId, $variantPageId)
    {
        $variantsData = OptimizePressStats_Cookie::get('chosen_variants');

        // Cast to array if this is the first experiment that we are saving
        if (null === $variantsData) {
            $variantsData = array();
        } else {
            // Decoding JSON data
            $variantsData = json_decode(stripslashes($variantsData), true);
        }

        // Append experiment and variant page ID couple
        $variantsData[$experimentId] = $variantPageId;

        return OptimizePressStats_Cookie::set('chosen_variants', json_encode($variantsData), self::COOKIE_LIFETIME);
    }
}

OptimizePressStats_Experiment::getInstance();