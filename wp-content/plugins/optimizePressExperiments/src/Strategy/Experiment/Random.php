<?php

class OptimizePressStats_Strategy_Experiment_Random implements OptimizePressStats_Strategy_Experiment_StrategyInterface
{
    /**
     * Select one of the given variants for experiment.
     * @param  object $experiment
     * @param  array $variants
     * @return object
     */
    public function chooseVariant($experiment, $variants)
    {
        return $variants[rand(0, count($variants) - 1)];
    }
}
