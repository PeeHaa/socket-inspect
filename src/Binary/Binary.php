<?php declare(strict_types=1);

namespace PeeHaa\SocketInspect\Binary;

use Amp\Http\Server\Router;
use Amp\Http\Server\Server as HttpServer;
use Amp\Http\Server\StaticContent\DocumentRoot;
use Amp\Http\Server\Websocket\Websocket as AmpWebSocket;
use Amp\Loop;
use PeeHaa\SocketInspect\Http\WebSocket as WebSocketApplication;
use PeeHaa\SocketInspect\MessageBroker\Combined;
use PeeHaa\SocketInspect\MessageBroker\StdOut;
use PeeHaa\SocketInspect\MessageBroker\WebSocket as WebSocketMessageBroker;
use PeeHaa\SocketInspect\Proxy\Address;
use PeeHaa\SocketInspect\Proxy\Proxy;
use Psr\Log\NullLogger;
use function Amp\Socket\listen;

class Binary
{
    private const DEFAULT_WEB_PORT = 8080;

    public function run(array $arguments): void
    {
        array_shift($arguments);

        if (count($arguments) === 0) {
            echo $this->renderHelpText();

            return;
        }

        Loop::run(function() use ($arguments) {
            $messageBroker = new Combined();

            if ($this->reportToCli($arguments)) {
                $messageBroker->registerBroker(new StdOut(STDOUT));
            }

            $webSocketMessageBroker = new WebSocketMessageBroker();

            $messageBroker->registerBroker($webSocketMessageBroker);

            $webSocketApplication = new WebSocketApplication(static function(Address $proxyAddress, Address $serverAddress) use ($messageBroker) {
                return (new Proxy($proxyAddress, $serverAddress, $messageBroker))->start();
            });

            $webSocketMessageBroker->registerWebSocket($webSocketApplication);

            $router = new Router();
            $router->addRoute('GET', '/live', new AmpWebSocket($webSocketApplication));
            $router->setFallback(new DocumentRoot(__DIR__ . '/../../public'));

            $sockets = [
                listen('0.0.0.0:' . $this->getWebInterfacePort($arguments)),
                listen('[::]:' . $this->getWebInterfacePort($arguments)),
            ];

            $server = new HttpServer($sockets, $router, new NullLogger());

            yield $server->start();
        });
    }

    private function reportToCli(array $arguments): bool
    {
        return in_array('-report-cli', $arguments, true);
    }

    private function getWebInterfacePort(array $arguments): int
    {
        foreach ($arguments as $argument) {
            if (strpos($argument, '-report-web=') === false) {
                continue;
            }

            return (int) substr($argument, 12);
        }

        return self::DEFAULT_WEB_PORT;
    }

    private function renderHelpText(): string
    {
        return <<<'EOD'
sinspect [-report-cli] [-report-web=PORT]

Usage:
  -report-cli       Outputs information to stdout
  -report-web=PORT  Specifies the PORT under which the web interface is available
EOD;
    }
}
