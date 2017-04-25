<?php

namespace BtcTicker\Feeder\Repository;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Predis\Client;

class RatesRepositoryTest extends TestCase
{
    public function tearDown()
    {
        m::close();
    }
    /** @var  m\Mock*/
    private $redis;

    public function setUp()
    {
        $this->redis = m::mock(Client::class);
    }

    public function testInitPairSuccess()
    {
        $pair = 'bt_usd_feed';

        $this->redis->shouldReceive('hset')->times(3);

        $repo = new RatesRepository($this->redis);
        $repo->initPair($pair);

        $this->assertTrue(true);
    }

    public function testInitFeedSuccess()
    {
        $feed = 'bitfinex';

        $this->redis->shouldReceive('hincrby')->once();

        $repo = new RatesRepository($this->redis);
        $repo->initFeed($feed);

        $this->assertTrue(true);
    }

    public function testSavePriceWithExpireSuccess()
    {
        $pair = 'bt_usd_feed';
        $feed = 'bitfinex';
        $price = 200;

        $this->redis->shouldReceive('set')->once();
        $this->redis->shouldReceive('expire')->once();

        $repo = new RatesRepository($this->redis);
        $repo->savePriceWithExpire($pair, $feed, $price);

        $this->assertTrue(true);
    }

    public function testGetFeedsByPairSuccess()
    {
        $pair = 'btc_usd_feed';
        $keysPattern = ['bitfinex'];
        $expected = ['bitfinex' => 200];

        $this->redis->shouldReceive('keys')->once()->andReturn($keysPattern);
        $this->redis->shouldReceive('get')->once()->andReturn(200);

        $repo = new RatesRepository($this->redis);
        $result = $repo->getFeedsByPair($pair);

        $this->assertArraySubset($expected, $result);
    }

    public function testSaveAvgPriceForPairSuccess()
    {
        $pair = 'btc_usd_feed';
        $price = 200;

        $this->redis->shouldReceive('hset')->once();

        $repo = new RatesRepository($this->redis);
        $repo->saveAvgPriceForPair($pair, $price);

        $this->assertTrue(true);
    }

    public function testSaveActiveFeedsForPairSuccess()
    {
        $pair = 'btc_usd_feed';
        $count = 2;

        $this->redis->shouldReceive('hset')->once();

        $repo = new RatesRepository($this->redis);
        $repo->saveActiveFeedsForPair($pair, $count);

        $this->assertTrue(true);
    }

    public function testGetBTCUSDRatesWithNoRatesArray()
    {
        $noAvailableRates = [];

        $this->redis->shouldReceive('exists')->once()->andReturn(false);
        $this->redis->shouldNotReceive('hgetall');

        $repo = new RatesRepository($this->redis);
        $result = $repo->getBTCUSDRates();

        $this->assertArraySubset($noAvailableRates, $result);
    }

    public function testGetBTCUSDRatesWithRatesArray()
    {
        $rates = [
            'feeds_count' => 3,
            'feeds_active' => 3,
            'avg_price' => 200
        ];

        $this->redis->shouldReceive('exists')->once()->andReturn(true);
        $this->redis->shouldReceive('hgetall')->once()->andReturn($rates);

        $repo = new RatesRepository($this->redis);
        $result = $repo->getBTCUSDRates();

        $this->assertArraySubset($rates, $result);
    }
}
