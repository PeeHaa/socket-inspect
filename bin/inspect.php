<?php declare(strict_types=1);

namespace PeeHaa\SocketInspect\Bin;

use Amp\Http\Server\Router;
use Amp\Http\Server\Server as HttpServer;
use Amp\Http\Server\StaticContent\DocumentRoot;
use Amp\Http\Server\Websocket\Websocket as AmpWebSocket;
use Amp\Loop;
use PeeHaa\SocketInspect\Http\WebSocket as WebSocketApplication;
use PeeHaa\SocketInspect\Inspect\Message\WebSocket as WebSocketMessageBroker;
use PeeHaa\SocketInspect\Inspect\Server;
use Psr\Log\NullLogger;
use function Amp\Socket\listen;

require_once __DIR__ . '/../bootstrap.php';

Loop::run(static function() {
    $messageBroker = new WebSocketMessageBroker();

    $webSocketApplication = new WebSocketApplication(static function($uri) use ($messageBroker) {
        return (new Server($uri, $messageBroker))->start();
    });

    $messageBroker->registerWebSocket($webSocketApplication);

    $router = new Router();
    $router->addRoute('GET', '/live', new AmpWebSocket($webSocketApplication));
    $router->setFallback(new DocumentRoot(__DIR__ . '/../public'));

    $sockets = [
        listen('0.0.0.0:8080'),
        listen('[::]:8080'),
    ];

    $server = new HttpServer($sockets, $router, new NullLogger());

    yield $server->start();
});
