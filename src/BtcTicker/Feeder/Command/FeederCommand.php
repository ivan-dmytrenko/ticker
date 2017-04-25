<?php

namespace BtcTicker\Feeder\Command;

use Knp\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Ratchet\Client\WebSocket;
use Ratchet\RFC6455\Messaging\MessageInterface;

class FeederCommand extends Command
{
    protected function configure()
    {
        $this
            ->setHidden(true)
            ->setName('btcfeed')
            ->setDescription('Get rates from feeds');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $app = $this->getSilexApplication();

        /** @var \React\EventLoop\StreamSelectLoop $loop */
        $loop = $app['react.event_loop'];

        foreach ($app['rates']['feeds_api'] as $pairFeed => $config) {
            $app['btc_ticker.feeder.repository.rates_repository']->initPair($pairFeed);

            foreach ($config as $key => $feed) {
                $app['btc_ticker.feeder.repository.rates_repository']->initFeed($pairFeed);
                $app['ratchet.client.connector']($feed['host'])
                    ->then(function (WebSocket $conn) use ($loop, $output, $app, $pairFeed, $key, $feed) {
                        $conn->send($app[$feed['container']]->getSubscribeMessage());

                        $conn->on(
                            'message',
                            function (MessageInterface $msg) use ($conn, $loop, $output, $app, $key, $feed, $pairFeed) {
                                $msg = json_decode($msg, true);

                                if (true === is_array($msg) &&
                                    0 !== $price = $app[$feed['container']]->extractPrice($msg)
                                ) {
                                    $app[$feed['container']]->update($pairFeed, $price);
                                    $app[$feed['container']]->publishBTCUSDTrigger($app['redis']['default_channel']);
                                }
                            }
                        );

                        $conn->on('close', function ($code = null, $reason = null) {
                            echo sprintf('Connection closed (%s - %s) \n', $code, $reason);
                        });

                    }, function (\Exception $e) use ($loop) {
                        echo sprintf('Could not connect: %s \n', $e->getMessage());
                    });
            }
        }

    }
}
