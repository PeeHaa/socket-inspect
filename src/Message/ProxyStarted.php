<?php declare(strict_types=1);

namespace PeeHaa\SocketInspect\Message;

use PeeHaa\SocketInspect\Message\Enum\Initiator;

class ProxyStarted extends Message
{
    public function __construct(string $address)
    {
        parent::__construct($address, Initiator::PROXY(), sprintf('Started proxy on %s', $address));
    }
}
