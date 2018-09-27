<?php declare(strict_types=1);

namespace PeeHaa\SocketInspect\MessageBroker;

use PeeHaa\SocketInspect\Message\Message;

class Combined implements Broker
{
    /** @var Broker[] */
    private $brokers = [];

    public function registerBroker(Broker $broker): void
    {
        $this->brokers[] = $broker;
    }

    public function send(Message $message): void
    {
        foreach ($this->brokers as $broker) {
            $broker->send($message);
        }
    }
}
