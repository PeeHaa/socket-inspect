<?php declare(strict_types=1);

namespace PeeHaa\SocketInspect\Examples;

use Amp\Loop;
use Amp\Socket\Socket;
use function Amp\Socket\connect;

require_once __DIR__ . '/../vendor/autoload.php';

Loop::run(function () {
    /** @var Socket $client */
    $client = yield connect('tcp://127.0.0.1:2800');

    yield $client->write('Hello there server!');
});
