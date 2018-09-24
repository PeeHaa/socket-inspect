<?php declare(strict_types=1);

namespace PeeHaa\SocketInspect\Inspect\Message\Server;

use PeeHaa\SocketInspect\Inspect\Message\Category;
use PeeHaa\SocketInspect\Inspect\Message\Message;
use PeeHaa\SocketInspect\Inspect\Message\Severity;

abstract class Server extends Message
{
    public function __construct(string $server, string $type, Severity $severity, string $message)
    {
        parent::__construct($server, Category::SERVER(), $type, $severity, $message);
    }
}
