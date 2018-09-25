<?php declare(strict_types=1);

namespace PeeHaa\SocketInspect\Inspect\Message\Server;

use PeeHaa\SocketInspect\Inspect\Message\Severity;

class NewTarget extends Server
{
    public function __construct(string $server, string $address)
    {
        parent::__construct($server, 'newTarget', Severity::INFO(), sprintf('Opened connection to target %s', $address));
    }
}
