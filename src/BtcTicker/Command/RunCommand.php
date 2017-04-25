<?php

namespace BtcTicker\Command;

use BtcTicker\Feeder\Command\FeederCommand;
use BtcTicker\Server\Command\PusherCommand;
use Knp\Command\Command;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RunCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('btcticker:run')
            ->setDescription('Run Pusher and Feeders');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $app = $this->getSilexApplication();

        /** @var Application $console */
        $console = $app['console'];
        $console->add(new FeederCommand())->run($input, $output);
        $console->add(new PusherCommand())->run($input, $output);

        /** @var \React\EventLoop\StreamSelectLoop $loop */
        $loop = $app['react.event_loop'];

        $loop->run();
    }
}
