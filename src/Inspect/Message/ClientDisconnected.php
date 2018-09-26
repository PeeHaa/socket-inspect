<?php declare(strict_types=1);

namespace PeeHaa\SocketInspect\Inspect\Message;

use PeeHaa\SocketInspect\Inspect\Message\Enum\Initiator;

class ClientDisconnected extends Message
{
    public function __construct(string $proxyAddress, string $clientAddress)
    {
        parent::__construct($proxyAddress, Initiator::PROXY(), sprintf('Client (%s) disconnected', $clientAddress));
    }
}
