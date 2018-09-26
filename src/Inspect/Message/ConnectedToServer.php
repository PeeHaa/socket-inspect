<?php declare(strict_types=1);

namespace PeeHaa\SocketInspect\Inspect\Message;

use PeeHaa\SocketInspect\Inspect\Message\Enum\Initiator;

class ConnectedToServer extends Message
{
    public function __construct(string $proxyAddress, string $serverAddress, string $clientAddress)
    {
        parent::__construct(
            $proxyAddress,
            Initiator::PROXY(),
            sprintf('Connected to server `%s` on behalf of client `%s`', $serverAddress, $clientAddress)
        );
    }
}
