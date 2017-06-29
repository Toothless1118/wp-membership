<?php

interface OptimizePressStats_Strategy_Experiment_StrategyInterface
{
    /**
     * Select one of the given variants for experiment.
     * @param  object $experiment
     * @param  array $variants
     * @return object
     */
    public function chooseVariant($experiment, $variants);
}
