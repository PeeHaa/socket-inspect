<?php declare(strict_types=1);

namespace PeeHaa\SocketInspect\Inspect\Proxy;

use Amp\Promise;
use Amp\Socket\Server;
use Amp\Socket\ServerSocket;
use PeeHaa\SocketInspect\Inspect\Message\Broker;
use PeeHaa\SocketInspect\Inspect\Message\Server\NewClient;
use PeeHaa\SocketInspect\Inspect\Message\Server\Start;
use PeeHaa\SocketInspect\Inspect\Proxy\Server as TargetServer;
use function Amp\asyncCall;
use function Amp\call;
use function Amp\Socket\listen;

class Proxy
{
    private $proxyAddress;

    private $targetAddress;

    private $messageBroker;

    public function __construct(string $proxyAddress, string $targetAddress, Broker $messageBroker)
    {
        $this->proxyAddress  = $proxyAddress;
        $this->targetAddress = $targetAddress;
        $this->messageBroker = $messageBroker;
    }

    public function start(): Promise
    {
        return call(function() {
            $proxy = listen($this->proxyAddress);

            $this->messageBroker->send(new Start($this->proxyAddress));

            $this->processClients($proxy);
        });
    }

    private function processClients(Server $server): void
    {
        asyncCall(function() use ($server) {
            /** @var ServerSocket $socket */
            while ($socket = yield $server->accept()) {
                $this->processClient($socket);
            }
        });
    }

    private function processClient(ServerSocket $clientSocket): void
    {
        asyncCall(function() use ($clientSocket) {
            $this->messageBroker->send(new NewClient($this->proxyAddress, $clientSocket->getRemoteAddress()));

            /** @var \PeeHaa\SocketInspect\Inspect\Proxy\Server $server */
            $server = yield (new TargetServer($this->proxyAddress, $this->targetAddress, $this->messageBroker))->start();
            $client = new Client($this->proxyAddress, $clientSocket, $server, $this->messageBroker);

            asyncCall(function() use ($server, $client) {
                $server->onReceived(function($message) use ($client) {
                    return $client->send($message);
                });
            });

            asyncCall(function() use ($server, $client) {
                $client->onReceived(function($message) use ($server) {
                    return $server->send($message);
                });
            });

            asyncCall(function() use ($server, $client) {
                $server->onClose(function() use ($client) {
                    $client->close();
                });
            });

            asyncCall(function() use ($server, $client) {
                $client->onClose(function() use ($server) {
                    $server->close();
                });
            });
        });
    }
}
