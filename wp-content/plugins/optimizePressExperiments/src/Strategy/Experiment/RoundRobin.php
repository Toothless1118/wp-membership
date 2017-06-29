<?php

class OptimizePressStats_Strategy_Experiment_RoundRobin implements OptimizePressStats_Strategy_Experiment_StrategyInterface
{
    /**
     * Select one of the given variants for experiment.
     * @param  object $experiment
     * @param  array $variants
     * @return object
     */
    public function chooseVariant($experiment, $variants)
    {
        // Fetch last chosen index
        $lastIndex = $this->getLastChosenVariantIndex($experiment->id);

        if (null === $lastIndex) {
            $selectedIndex = 0;
        } else {
            $selectedIndex = ($lastIndex + 1) % count($variants);
        }

        // Saving chosen index
        $this->setLastChosenVariantIndex($experiment->id, $selectedIndex);

        return $variants[$selectedIndex];
    }

    /**
     * Return last chosen variant index for given experiment.
     * @param  integer $experimentId
     * @return integer|null
     */
    protected function getLastChosenVariantIndex($experimentId)
    {
        if (false === $variantIndex = get_option('op_experiment_es_index_' . $experimentId)) {
            return null;
        }

        return $variantIndex;
    }

    /**
     * Save last chosen variant index for given experiment.
     * @param integer $experimentId
     * @param integer $variantIndex
     */
    protected function setLastChosenVariantIndex($experimentId, $variantIndex)
    {
        update_option('op_experiment_es_index_' . $experimentId, $variantIndex, true);
    }
}