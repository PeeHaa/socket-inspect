<?php declare(strict_types=1);

namespace PeeHaa\SocketInspect\Inspect\Message\Outgoing;

use PeeHaa\SocketInspect\Inspect\Message\Category;
use PeeHaa\SocketInspect\Inspect\Message\Message;
use PeeHaa\SocketInspect\Inspect\Message\Severity;

class Sent extends Message
{
    public function __construct(string $server, string $message)
    {
        parent::__construct($server, Category::OUTGOING(), 'message', Severity::INFO(), $message);
    }
}
