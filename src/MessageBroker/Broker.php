<?php declare(strict_types=1);

namespace PeeHaa\SocketInspect\MessageBroker;

use PeeHaa\SocketInspect\Message\Message;

interface Broker
{
    public function send(Message $message): void;
}
