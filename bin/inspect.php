<?php declare(strict_types=1);

namespace PeeHaa\SocketInspect\Bin;

use Amp\Http\Server\Router;
use Amp\Http\Server\StaticContent\DocumentRoot;
use Amp\Http\Server\Websocket\Websocket;
use Amp\Loop;
use PeeHaa\SocketInspect\Http\WebSocket as WebSocketApplication;
use PeeHaa\SocketInspect\Inspect\Server;
use Psr\Log\NullLogger;
use function Amp\Socket\listen;

require_once __DIR__ . '/../bootstrap.php';

$webSocketApplication = new WebSocketApplication();

$messageBroker = new \PeeHaa\SocketInspect\Inspect\Message\WebSocket($webSocketApplication);

$router = new Router();
$router->addRoute('GET', '/live', new Websocket($webSocketApplication));
$router->setFallback(new DocumentRoot(__DIR__ . '/../public'));

$sockets = [
    listen('0.0.0.0:8080'),
    listen('[::]:8080'),
];

$server = new \Amp\Http\Server\Server($sockets, $router, new NullLogger());

Loop::run(static function() use ($server, $messageBroker) {
    yield $server->start();

    yield (new Server('tcp://127.0.0.1:2800', $messageBroker))->start();
});
