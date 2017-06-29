<?php

interface OptimizePressStats_Remote_ClientInterface
{
    public function sendDailyAggregates($data);
}