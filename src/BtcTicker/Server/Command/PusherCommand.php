<?php

namespace BtcTicker\Server\Command;

use Knp\Command\Command;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\Wamp\WampServer;
use Ratchet\WebSocket\WsServer;
use React\Socket\Server;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PusherCommand extends Command
{
    protected function configure()
    {
        $this
            ->setHidden(true)
            ->setName('btcticker:pusher')
            ->setDescription('Push rates for the subscribers');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $app = $this->getSilexApplication();

        /** @var \React\EventLoop\StreamSelectLoop $loop */
        $loop = $app['react.event_loop'];

        /** @var \BtcTicker\Server\Pusher $pusher */
        $pusher = $app['btc_ticker.server.pusher'];

        /** @var \Predis\Async\Client $client */
        $client = $app['predis.async.client'];
        $client->connect(array($pusher, 'init'));

        $webSock = new Server($loop);
        $webSock->listen($app['ws']['port'], '0.0.0.0');
        $webServer = new IoServer(
            new HttpServer(
                new WsServer(
                    new WampServer(
                        $pusher
                    )
                )
            ),
            $webSock
        );
    }
}
