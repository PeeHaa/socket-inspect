<?php declare(strict_types=1);

namespace PeeHaa\SocketInspect\Message;

use PeeHaa\SocketInspect\Message\Enum\Initiator;
use PeeHaa\SocketInspect\Proxy\Address;

class ClientSent extends Message
{
    public function __construct(Address $proxyAddress, string $message, string $clientAddress)
    {
        parent::__construct($proxyAddress->getAddress(), Initiator::CLIENT(), $message, $clientAddress);
    }
}
