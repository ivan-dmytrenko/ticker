<?php

namespace BtcTicker\Adapter;

use BtcTicker\Feeder\Repository\RatesRepository;

/**
 * Class RatesAdapter
 * @package BtcTicker\Adapter
 */
class RatesAdapter
{
    /**
     * @var RatesRepository
     */
    private $rates;

    /**
     * RatesAdapter constructor.
     * @param RatesRepository $ratesRepository
     */
    public function __construct(RatesRepository $ratesRepository)
    {
        $this->rates = $ratesRepository;
    }

    /**
     * Adapt rates information for required pattern
     * @return string
     */
    public function getBTCUSDInfoInLine() : string
    {
        $rates = $this->rates->getBTCUSDRates();
        if (true === empty($rates)) {
            return 'No available rates';
        }
        return sprintf(
            'BTC/USD: %.2F Active sources: BTC/USD (%d of %d)',
            $rates[RatesRepository::$avgPriceKey],
            $rates[RatesRepository::$activeFeeds],
            $rates[RatesRepository::$feedsCountKey]
        );
    }
}
