<?php declare(strict_types=1);

namespace PeeHaa\SocketInspect\Inspect\Message\Server;

use PeeHaa\SocketInspect\Inspect\Message\Severity;

class ClientDisconnect extends Server
{
    public function __construct()
    {
        parent::__construct('clientDisconnect', Severity::INFO(), 'Client disconnected');
    }
}
