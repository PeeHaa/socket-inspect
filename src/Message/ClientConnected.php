<?php declare(strict_types=1);

namespace PeeHaa\SocketInspect\Message;

use PeeHaa\SocketInspect\Message\Enum\Initiator;

class ClientConnected extends Message
{
    public function __construct(string $proxyAddress, string $clientAddress)
    {
        parent::__construct($proxyAddress, Initiator::PROXY(), sprintf('Client (%s) connected', $clientAddress));
    }
}
