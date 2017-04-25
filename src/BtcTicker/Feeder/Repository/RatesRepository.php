<?php

namespace BtcTicker\Feeder\Repository;

use Predis\Client;

class RatesRepository
{
    /** @var Client */
    private $redis;

    private $BTCUSDPairKey = 'btc_usd_feeds';
    static public $feedsCountKey = 'feeds_count';
    static public $activeFeeds = 'feeds_active';
    static public $avgPriceKey = 'avg_price';
    private $feedExpire = 300;

    /**
     * RatesRepository constructor.
     * @param Client $redis
     */
    public function __construct(Client $redis)
    {
        $this->redis = $redis;
    }

    /**
     * Initialize a currency pair
     * @param string $pair
     */
    public function initPair(string $pair)
    {
        $this->redis->hset($pair, self::$feedsCountKey, 0);
        $this->redis->hset($pair, self::$activeFeeds, 0);
        $this->redis->hset($pair, self::$avgPriceKey, 0);
    }

    /**
     * Initialize a feed
     * @param string $pair
     */
    public function initFeed(string $pair)
    {
        $this->redis->hincrby($pair, self::$feedsCountKey, 1);
    }

    /**
     * Store a price and set expiration time
     * @param string $pair
     * @param string $feed
     * @param float $price
     */
    public function savePriceWithExpire(string $pair, string $feed, float $price)
    {
        $key = sprintf('%s:%s', $pair, $feed);
        $this->redis->set($key, $price);
        $this->redis->expire($key, $this->feedExpire);
    }

    /**
     * Get feed by a currency pair
     * @param string $pair
     * @return array
     */
    public function getFeedsByPair(string $pair) : array
    {
        $keysPattern = sprintf('%s:*', $pair);
        $feeds = [];
        foreach ($this->redis->keys($keysPattern) as $key) {
            $feeds[$key] = $this->redis->get($key);
        }

        return $feeds;
    }

    /**
     * Store average price for a pair
     * @param string $pair
     * @param float $price
     */
    public function saveAvgPriceForPair(string $pair, float $price)
    {
        $this->redis->hset($pair, self::$avgPriceKey, $price);
    }

    /**
     * Store count of active feeds
     * @param string $pair
     * @param int $count
     */
    public function saveActiveFeedsForPair(string $pair, int $count)
    {
        $this->redis->hset($pair, self::$activeFeeds, $count);
    }

    /**
     * Get rates for
     * @return array
     */
    public function getBTCUSDRates() : array
    {
        return ($this->redis->exists($this->BTCUSDPairKey)) ? $this->redis->hgetall($this->BTCUSDPairKey) : [];
    }
}
