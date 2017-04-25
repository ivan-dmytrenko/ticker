<?php

namespace BtcTicker\Feeder;

interface PriceInterface
{
    public function extractPrice(Array $message);
}
