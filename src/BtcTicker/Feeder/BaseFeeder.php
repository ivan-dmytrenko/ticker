<?php

namespace BtcTicker\Feeder;

use BtcTicker\Adapter\RatesAdapter;
use BtcTicker\Feeder\Repository\RatesRepository;
use Predis\Client;
use Silex\Application;

abstract class BaseFeeder
{
    /**
     * Config for a feed
     * @var array
     */
    protected $config;
    /** @var RatesRepository  */
    protected $rates;
    /** @var RatesAdapter  */
    protected $ratesAdapter;
    /** @var Client  */
    protected $redis;

    /**
     * BaseFeeder constructor.
     * @param array $config
     * @param RatesRepository $rates
     * @param RatesAdapter $ratesAdapter
     * @param Client $redis
     */
    public function __construct(
        Array $config,
        RatesRepository $rates,
        RatesAdapter $ratesAdapter,
        Client $redis
    ) {
        $this->config = $config;
        $this->rates = $rates;
        $this->ratesAdapter = $ratesAdapter;
        $this->redis = $redis;
    }

    /**
     * Get initial message for subscribing to a channel
     * @return string
     */
    public function getSubscribeMessage() : string
    {
        return $this->config['subscribe_message'];
    }

    /**
     * Update currency pair collection
     * @param string $pair
     * @param $price
     */
    public function update(string $pair, $price) : void
    {
        $price = number_format($price, 2, '.', '');
        if ($price != 0) {
            $this->rates->savePriceWithExpire(
                $pair,
                $this->config['name'],
                $price
            );
            $avgWithCount = $this->getAvgWithElementsCount(
                $this->rates->getFeedsByPair($pair)
            );
            $this->rates->saveActiveFeedsForPair($pair, $avgWithCount['count']);
            $this->rates->saveAvgPriceForPair($pair, $avgWithCount['avg']);
        }
    }

    /**
     * Get count of active feeds and average price of them
     * @param array $elements
     * @return array
     */
    private function getAvgWithElementsCount(array $elements) : array
    {
        $count = count($elements);
        $avgWithElCount = ['count' => 0, 'avg' => 0];
        if ($count > 0) {
            $avgWithElCount['count'] = $count;
            $avgWithElCount['avg'] = array_sum($elements) / $count;
        }

        return $avgWithElCount;
    }

    /**
     * Publish the changes
     * @param $channel
     */
    public function publishBTCUSDTrigger(string $channel) : void
    {
        $this->redis->publish($channel, $this->ratesAdapter->getBTCUSDInfoInLine());
    }
}
