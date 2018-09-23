<?php declare(strict_types=1);

namespace PeeHaa\SocketInspect\Bin;

use Amp\Loop;
use PeeHaa\SocketInspect\Inspect\Message\StdOut;
use PeeHaa\SocketInspect\Inspect\Server;

require_once __DIR__ . '/../bootstrap.php';

Loop::run(static function() {
    yield (new Server('tcp://127.0.0.1:2800', new StdOut()))->start();
});
