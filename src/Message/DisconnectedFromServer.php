<?php declare(strict_types=1);

namespace PeeHaa\SocketInspect\Message;

use PeeHaa\SocketInspect\Message\Enum\Initiator;
use PeeHaa\SocketInspect\Proxy\Address;

class DisconnectedFromServer extends Message
{
    public function __construct(Address $proxyAddress, Address $serverAddress, string $clientAddress)
    {
        parent::__construct(
            $proxyAddress->getAddress(),
            Initiator::PROXY(),
            sprintf(
                'Disconnected from server `%s` on behalf of client `%s`',
                $serverAddress->getAddress(),
                $clientAddress
            )
        );
    }
}
