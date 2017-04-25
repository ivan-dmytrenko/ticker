<?php

namespace BtcTicker\Feeder;

class Coinbase extends BaseFeeder implements PriceInterface
{
    public function extractPrice(Array $message)
    {
        if (false === isset($message['reason']) || false === isset($message['price']) ||
            $this->config['id_message_key'] !== $message['reason']
        ) {
            return 0;
        }

        return $message['price'];
    }
}
