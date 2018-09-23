<?php declare(strict_types=1);

namespace PeeHaa\SocketInspect\Inspect\Message;

interface Broker
{
    public function send(Message $message): void;
}
