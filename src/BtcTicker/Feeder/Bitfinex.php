<?php

namespace BtcTicker\Feeder;

class Bitfinex extends BaseFeeder implements PriceInterface
{
    public function extractPrice(Array $message)
    {
        if (false === isset($message[1]) || $this->config['id_message_key'] !== $message[1]) {
            return 0;
        }

        $message = array_combine($this->config['message_keys'], $message);

        return $message['price'];
    }
}
