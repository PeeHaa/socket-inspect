<?php declare(strict_types=1);

namespace PeeHaa\SocketInspect\Bin;

use Amp\Http\Server\Router;
use Amp\Http\Server\Server as HttpServer;
use Amp\Http\Server\StaticContent\DocumentRoot;
use Amp\Http\Server\Websocket\Websocket as AmpWebSocket;
use Amp\Loop;
use PeeHaa\SocketInspect\Http\WebSocket as WebSocketApplication;
use PeeHaa\SocketInspect\MessageBroker\Combined;
use PeeHaa\SocketInspect\MessageBroker\WebSocket as WebSocketMessageBroker;
use PeeHaa\SocketInspect\Proxy\Address;
use PeeHaa\SocketInspect\Proxy\Proxy;
use Psr\Log\NullLogger;
use function Amp\Socket\listen;

require_once __DIR__ . '/../bootstrap.php';

Loop::run(static function() {
    $messageBroker = new Combined();

    $webSocketMessageBroker = new WebSocketMessageBroker();

    $messageBroker->registerBroker($webSocketMessageBroker);

    $webSocketApplication = new WebSocketApplication(static function(Address $proxyAddress, Address $serverAddress) use ($messageBroker) {
        return (new Proxy($proxyAddress, $serverAddress, $messageBroker))->start();
    });

    $webSocketMessageBroker->registerWebSocket($webSocketApplication);

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
