<?php declare(strict_types=1);

namespace PeeHaa\SocketInspect\Inspect\Message\Server;

use PeeHaa\SocketInspect\Inspect\Message\Severity;

class NewClient extends Server
{
    public function __construct(string $server, string $address)
    {
        parent::__construct($server, 'newClient', Severity::INFO(), sprintf('Accepted connection from %s', $address));
    }
}
