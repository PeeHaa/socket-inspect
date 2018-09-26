<?php declare(strict_types=1);

namespace PeeHaa\SocketInspect\Message;

use PeeHaa\SocketInspect\Message\Enum\Initiator;

class ServerSent extends Message
{
    public function __construct(string $server, string $message)
    {
        parent::__construct($server, Initiator::SERVER(), $message);
    }
}
