<?php declare(strict_types=1);

namespace PeeHaa\SocketInspect\Message;

use PeeHaa\SocketInspect\Message\Enum\Initiator;
use PeeHaa\SocketInspect\Proxy\Address;

class ProxyStarted extends Message
{
    public function __construct(Address $address)
    {
        $encrypted = '';

        if ($address->isEncrypted()) {
            $encrypted = '(encrypted) ';
        }

        parent::__construct(
            $address->getAddress(),
            Initiator::PROXY(),
            sprintf('Started %sproxy on %s', $encrypted, $address->getAddress())
        );
    }
}
