<?php declare(strict_types=1);

namespace PeeHaa\SocketInspect\Inspect\Message;

use PeeHaa\SocketInspect\Inspect\Message\Enum\Initiator;

class ProxyStarted extends Message
{
    public function __construct(string $address)
    {
        parent::__construct($address, Initiator::PROXY(), sprintf('Started proxy on %s', $address));
    }
}
