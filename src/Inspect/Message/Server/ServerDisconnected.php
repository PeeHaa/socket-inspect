<?php declare(strict_types=1);

namespace PeeHaa\SocketInspect\Inspect\Message\Server;

use PeeHaa\SocketInspect\Inspect\Message\Severity;

class ServerDisconnected extends Server
{
    public function __construct(string $server)
    {
        parent::__construct($server, 'serverDisconnected', Severity::INFO(), 'Server disconnected');
    }
}
