<?php

namespace BtcTicker\Adapter;

use BtcTicker\Feeder\Repository\RatesRepository;
use Mockery as m;
use PHPUnit\Framework\TestCase;

class RatesAdapterTest extends TestCase
{
    public function tearDown()
    {
        m::close();
    }

    private $rates;

    public function setUp()
    {
        $this->rates = m::mock(RatesRepository::class);
    }

    public function testGetBTCUSDInfoInLineWithNoRatesArray()
    {
        $noAvailableRates = [];

        $this->rates->shouldReceive('getBTCUSDRates')->once()->andReturn($noAvailableRates);

        $adapter = new RatesAdapter($this->rates);
        $resultStr = $adapter->getBTCUSDInfoInLine();

        $this->assertEquals('No available rates', $resultStr);
    }

    public function testGetBTCUSDInfoInLineWithRatesArray()
    {
        $rates = [
            'feeds_count' => 3,
            'feeds_active' => 3,
            'avg_price' => 200
        ];

        $this->rates->shouldReceive('getBTCUSDRates')->once()->andReturn($rates);

        $adapter = new RatesAdapter($this->rates);
        $resultStr = $adapter->getBTCUSDInfoInLine();

        $this->assertEquals(sprintf('BTC/USD: 200.00 Active sources: BTC/USD (3 of 3)'), $resultStr);
    }
}
