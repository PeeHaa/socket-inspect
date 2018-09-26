<?php declare(strict_types=1);

namespace PeeHaa\SocketInspect\Inspect\Message;

use PeeHaa\SocketInspect\Inspect\Message\Enum\Initiator;

class ServerSent extends Message
{
    public function __construct(string $server, string $message)
    {
        parent::__construct($server, Initiator::SERVER(), $message);
    }
}
