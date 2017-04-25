<?php

namespace BtcTicker\Feeder;

class Bitmex extends BaseFeeder implements PriceInterface
{
    public function extractPrice(Array $message)
    {
        if (false === isset($message['table']) || false === isset($message['data'][0]['price'])) {
            return 0;
        }

        return $message['data'][0]['price'];
    }
}
