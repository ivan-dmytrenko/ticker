<?php

namespace BtcTicker\Server;

use BtcTicker\Adapter\RatesAdapter;
use Predis\Async\Client;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\WampServerInterface;

class Pusher implements WampServerInterface
{
    /** @var Client  */
    protected $redis;
    /** @var RatesAdapter  */
    protected $ratesAdapter;
    protected $subscribedTopics = [];

    /**
     * Pusher constructor.
     * @param Client $client
     */
    public function __construct(Client $client, RatesAdapter $ratesAdapter)
    {
        $this->redis = $client;
        $this->ratesAdapter = $ratesAdapter;
    }

    public function init($client)
    {
        $this->redis = $client;
    }

    public function onSubscribe(ConnectionInterface $conn, $topic)
    {
        if (!array_key_exists($topic->getId(), $this->subscribedTopics)) {
            $this->subscribedTopics[$topic->getId()] = $topic;
            $pubsubContext = $this->redis->pubsub($topic->getId(), array($this, 'pubsub'));
        }
    }

    public function pubsub($event, $pubsub)
    {
        if (!array_key_exists($event->channel, $this->subscribedTopics)) {
            return;
        }

        $topic = $this->subscribedTopics[$event->channel];
        $topic->broadcast($event->payload);

        if (strtolower(trim($event->payload)) === 'quit') {
            $pubsub->quit();
        }
    }

    public function onUnSubscribe(ConnectionInterface $conn, $topic)
    {
    }

    public function onOpen(ConnectionInterface $conn)
    {
    }

    public function onClose(ConnectionInterface $conn)
    {
    }

    public function onCall(ConnectionInterface $conn, $id, $topic, array $params)
    {
    }

    public function onPublish(ConnectionInterface $conn, $topic, $event, array $exclude, array $eligible)
    {
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
    }
}
