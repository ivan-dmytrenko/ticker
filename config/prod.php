<?php

$app['twig.path'] = [__DIR__ . '/../views'];

$app['rates'] = [
    'feeds_api' => [
        'btc_usd_feeds' => [
            'bitfinex' => [
                'name' => 'bitfinex',
                'container' => 'btc_ticker.feeder.bitfinex',
                'host' => 'wss://api.bitfinex.com/ws/',
                'subscribe_message' => '{"event": "subscribe", "channel": "trades", "pair":"tBTCUSD"}',
                'auth' => false,
                'id_message_key' => 'tu',
                'message_keys' => ['session', 'key', 'seq', 'id', 'timestamp', 'price', 'amount']
            ],
            'bitmex' => [
                'name' => 'bitmex',
                'container' => 'btc_ticker.feeder.bitmex',
                'host' => 'wss://www.bitmex.com/realtime',
                'subscribe_message' => '{"op": "subscribe", "args": ["trade:XBTUSD"]}',
                'auth' => false
            ],
            'coinbase' => [
                'name' =>'coinbase',
                'container' => 'btc_ticker.feeder.coinbase',
                'host' => 'wss://ws-feed.gdax.com',
                'subscribe_message' => '{"type": "subscribe","product_ids": ["BTC-USD"]}',
                'auth' => false,
                'id_message_key' => 'filled'
            ]
        ],
        'btc_eur_feeds' => [],
        'eur_usd_feeds' => []
    ]
];