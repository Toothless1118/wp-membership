<?php

class OptimizePressStats_Charting_Helper
{
    /**
     * RGBA color codes used in charts.
     * @var array
     */
    protected $colorsRgb = array("0,74,128", "99,184,197", "124,193,90", "164,123,178", "241,173,80");

    /**
     * @var OptimizePressStats_Charting_Helper
     */
    protected static $instance = null;

    /**
     * Init hooks and actions
     */
    protected function __construct()
    {
        add_action('admin_enqueue_scripts', array($this, 'enqueueScripts'));
    }

    /**
     * Return experiment object
     *
     * Singleton pattern
     * @return OptimizePressStats_Charting_Helper
     */
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Enqueue scripts and load chart colors data.
     * @return void
     */
    public function enqueueScripts()
    {
        wp_enqueue_script('chartjs', OP_S_BASE_URL . 'js/chart' . OP_SCRIPT_DEBUG . '.js', false, '2.0.1', true);

        wp_localize_script('chartjs', 'OpChartOptions', array(
            'colors' => array(
                'dark'  => $this->getColors('1.0'),
                'light' => $this->getColors('0.2'),
            ),
        ));
    }

    /**
     * Convert stats and variations data to ChartJS readable format.
     * @param  array $stats
     * @return array           Conversion rates data for ChartJS, total views, original page conversion rate and best alternative conversion rate
     */
    public function getBasicExperimentStats($stats)
    {
        // Calculate sums
        $views = 0;
        $originalConversionRate = 0;
        $bestAlternativeConversionRate = 0;

        $counter = 0;
        foreach ($stats as $item) {
            // Calculate conversion rate
            $conversionRate = $item->views !== 0 ? ($item->conversions / $item->views) * 100 : 0;

            // If current variation is actually original page
            if ($item->variation === $item->page) {
                $originalConversionRate = $conversionRate;
            } else  if ($conversionRate > $bestAlternativeConversionRate) {
                $bestAlternativeConversionRate = $conversionRate;
            }

            $views += $item->views;

            $counter += 1;
        }

        return array($views, $originalConversionRate, $bestAlternativeConversionRate);
    }

    /**
     * Format experiment conversion rates.
     * @param  array $stats
     * @return array
     */
    public function formatExperimentConversionRates($stats)
    {
        $data = array();

        $nonZeros = false;

        foreach ($stats as $item) {
            // Calculate conversion rate
            $conversionRate = $item->views !== 0 ? ($item->conversions / $item->views) * 100 : 0;

            $data[] = round($conversionRate, 2);

            if ($conversionRate > 0) {
                $nonZeros = true;
            }
        }

        if ($nonZeros) {
            return $data;
        } else {
            return null;
        }
    }

    /**
     * Format experiment stats for ChartJS usage.
     * @param  array $stats
     * @param  string $start expects date in Y-m-d format
     * @param  string $end   expects date in Y-m-d format
     * @return array
     */
    public function formatExperimentDataForChart($stats, $start, $end)
    {
        $zeros = array();

        // First we initialize 0 values for each day/period that we need
        // We are doing this cause there may be a date without any recorded stat
        $diff = date_diff(new DateTime($end), new DateTime($start));
        for ($a = 0; $a < $diff->days + 1; $a+= 1) {
            $zeros[date('Y-m-d', strtotime($start . " + $a day"))] = 0;
        }

        $data = array(
            'stats'     => array(),
            'labels'    => array_keys($zeros),
        );

        foreach ($stats as $row) {
            // Making sure that on the first mention of new variation we prefill it with zeros
            if ( ! array_key_exists($row->variation, $data['stats'])) {
                $data['stats'][$row->variation] = array(
                    'views'         => $zeros,
                    'unique'        => $zeros,
                    'conversions'   => $zeros,
                );
            }

            $data['stats'][$row->variation]['views'][$row->date]        = (int) $row->views;
            $data['stats'][$row->variation]['unique'][$row->date]       = (int) $row->unique;
            $data['stats'][$row->variation]['conversions'][$row->date]  = (int) $row->conversions;
        }

        return $data;
    }

    /**
     * Return data for aggregated stats for given page to be consumed by ChartJS.
     * @param  array $stats
     * @param  string $start expects date('Y-m-d')
     * @param  string $end expects date('Y-m-d')
     * @return array
     */
    public function formatPageViewDataForChart($stats, $start, $end)
    {
        $data = array(
            'labels'        => array(),
            'views'         => array(),
            'unique'        => array(),
            'conversions'   => array(),
        );

        foreach ($stats as $row) {
            // Fill in missing periods
            $diff = date_diff(new DateTime($row->date), new DateTime($start));

            if ($diff->days > 0) {
                for ($a = 0; $a < $diff->days; $a += 1) {
                    $data['labels'][]       = date('Y-m-d', strtotime($start . " + $a day"));
                    $data['views'][]        = 0;
                    $data['unique'][]       = 0;
                    $data['conversions'][]  = 0;
                }
            }

            $data['labels'][]       = $row->date;
            $data['views'][]        = (int)$row->views;
            $data['unique'][]       = (int)$row->unique;
            $data['conversions'][]  = (int)$row->conversions;

            $start = date('Y-m-d', strtotime($row->date . " + 1 day"));
        }

        $start = date('Y-m-d', strtotime($start . " - 1 day"));

        // Fill in missing periods at the end of result data
        $diff = date_diff(new DateTime($end), new DateTime($start));
        if ($diff->days > 0) {
            for ($a = 1; $a <= $diff->days; $a += 1) {
                $data['labels'][]       = date('Y-m-d', strtotime($start . " + $a day"));
                $data['views'][]        = 0;
                $data['unique'][]       = 0;
                $data['conversions'][]  = 0;
            }
        }

        $viewsSum       = array_sum($data['views']);
        $uniqueSum      = array_sum($data['unique']);
        $conversionsSum = array_sum($data['conversions']);

        $data['sum']    = array(
            'views'             => $viewsSum,
            'unique'            => $uniqueSum,
            'conversions'       => $conversionsSum,
            'conversion_rate'   => $viewsSum !== 0 ? round($conversionsSum / $viewsSum * 100, 2) : 0,
        );

        return $data;
    }

    /**
     * Sort given $unsorted data according to $order (using keys from first array and values from second).
     * @param  array $unsorted
     * @param  array $order
     * @return array
     */
    public function manuallySort($unsorted, $order)
    {
        $sorted = array();

        if (count($unsorted)) {
            foreach ($order as $key) {
                if (isset($unsorted[$key])) {
                    $sorted[$key] = $unsorted[$key];
                }
            }
        }

        return $sorted;
    }

    /**
     * Return colors used in charts.
     * @return array
     */
    public function getColors($opacity = '1.0')
    {
        $colors = array();
        foreach ($this->colorsRgb as $color) {
            $colors[] = 'rgba(' . $color . ',' . $opacity . ')';
        }

        return $colors;
    }
}