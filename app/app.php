<?php

use BtcTicker\Adapter\RatesAdapter;
use BtcTicker\Feeder\Bitmex;
use BtcTicker\Feeder\Bitfinex;
use BtcTicker\Feeder\Coinbase;
use BtcTicker\Server\Pusher;
use BtcTicker\Feeder\Repository\RatesRepository;
use Knp\Provider\ConsoleServiceProvider;
use Predis\Client;
use Predis\Async\Client as AsyncClient;
use React\EventLoop\Factory;
use Ratchet\Client\Connector;
use Silex\Application;
use Silex\Provider\AssetServiceProvider;
use Silex\Provider\TwigServiceProvider;

$app = new Application();

$app->register(new ConsoleServiceProvider(), [
    'console.name' => 'btcTicker',
    'console.version' => '1.0.0',
    'console.project_directory' => __DIR__.'/..'
]);

$app->register(new AssetServiceProvider());

$app->register(new TwigServiceProvider());

$app['predis.client'] = function ($app) {
    return new Client(vsprintf('%s://%s:%d', $app['redis']['server']));
};

$app['btc_ticker.feeder.repository.rates_repository'] = function ($app) {
    return new RatesRepository($app['predis.client']);
};

$app['btc_ticker.adapter.rates_adapter'] = function ($app) {
    return new RatesAdapter($app['btc_ticker.feeder.repository.rates_repository']);
};

$app['react.event_loop'] = function () {
    return Factory::create();
};

$app['ratchet.client.connector'] = $app->factory(function ($app) {
    return new Connector($app['react.event_loop']);
});

$app['predis.async.client'] = $app->factory(function ($app) {
    return new AsyncClient(
        vsprintf('%s://%s:%d', $app['redis']['server']),
        $app['react.event_loop']
    );
});

$app['btc_ticker.server.pusher'] = function ($app) {
    return new Pusher($app['predis.async.client'], $app['btc_ticker.adapter.rates_adapter']);
};

$app['btc_ticker.feeder.bitfinex'] = function ($app) {
    return new Bitfinex(
        $app['rates']['feeds_api']['btc_usd_feeds']['bitfinex'],
        $app['btc_ticker.feeder.repository.rates_repository'],
        $app['btc_ticker.adapter.rates_adapter'],
        $app['predis.client']
    );
};

$app['btc_ticker.feeder.bitmex'] = function ($app) {
    return new Bitmex(
        $app['rates']['feeds_api']['btc_usd_feeds']['bitmex'],
        $app['btc_ticker.feeder.repository.rates_repository'],
        $app['btc_ticker.adapter.rates_adapter'],
        $app['predis.client']
    );
};

$app['btc_ticker.feeder.coinbase'] = function ($app) {
    return new Coinbase(
        $app['rates']['feeds_api']['btc_usd_feeds']['coinbase'],
        $app['btc_ticker.feeder.repository.rates_repository'],
        $app['btc_ticker.adapter.rates_adapter'],
        $app['predis.client']
    );
};

$app->get('/', function () use ($app) {
    return $app['twig']->render(
        'ticker.html.twig'
    );
});

return $app;
