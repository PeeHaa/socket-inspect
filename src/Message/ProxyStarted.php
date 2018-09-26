<?php declare(strict_types=1);

namespace PeeHaa\SocketInspect\Message;

use PeeHaa\SocketInspect\Message\Enum\Initiator;
use PeeHaa\SocketInspect\Proxy\Address;

class ProxyStarted extends Message
{
    public function __construct(Address $address)
    {
        parent::__construct(
            $address->getAddress(),
            Initiator::PROXY(),
            sprintf('Started proxy on %s', $address->getAddress())
        );
    }
}
