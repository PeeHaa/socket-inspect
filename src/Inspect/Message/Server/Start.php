<?php declare(strict_types=1);

namespace PeeHaa\SocketInspect\Inspect\Message\Server;

use PeeHaa\SocketInspect\Inspect\Message\Severity;

class Start extends Server
{
    public function __construct(string $address)
    {
        parent::__construct('start', Severity::INFO(), sprintf('Listening for new connections on %s', $address));
    }
}
