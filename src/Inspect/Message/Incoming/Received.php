<?php declare(strict_types=1);

namespace PeeHaa\SocketInspect\Inspect\Message\Incoming;

use PeeHaa\SocketInspect\Inspect\Message\Category;
use PeeHaa\SocketInspect\Inspect\Message\Message;
use PeeHaa\SocketInspect\Inspect\Message\Severity;

class Received extends Message
{
    public function __construct(string $server, string $message)
    {
        parent::__construct($server, Category::INCOMING(), 'message', Severity::INFO(), $message);
    }
}
