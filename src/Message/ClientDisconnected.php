<?php declare(strict_types=1);

namespace PeeHaa\SocketInspect\Message;

use PeeHaa\SocketInspect\Message\Enum\Initiator;
use PeeHaa\SocketInspect\Proxy\Address;

class ClientDisconnected extends Message
{
    public function __construct(Address $proxyAddress, string $clientAddress)
    {
        parent::__construct(
            $proxyAddress->getAddress(),
            Initiator::PROXY(),
            sprintf('Client (%s) disconnected', $clientAddress)
        );
    }
}
