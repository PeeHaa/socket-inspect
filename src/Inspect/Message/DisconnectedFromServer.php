<?php declare(strict_types=1);

namespace PeeHaa\SocketInspect\Inspect\Message;

use PeeHaa\SocketInspect\Inspect\Message\Enum\Initiator;

class DisconnectedFromServer extends Message
{
    public function __construct(string $proxyAddress, string $serverAddress, string $clientAddress)
    {
        parent::__construct(
            $proxyAddress,
            Initiator::PROXY(),
            sprintf('Disconnected from server `%s` on behalf of client `%s`', $serverAddress, $clientAddress)
        );
    }
}
