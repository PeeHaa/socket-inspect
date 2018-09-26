<?php declare(strict_types=1);

namespace PeeHaa\SocketInspect\Proxy;

use Amp\ByteStream\StreamException;
use Amp\Promise;
use Amp\Socket\Server;
use Amp\Socket\ServerSocket;
use PeeHaa\SocketInspect\Message\ClientConnected;
use PeeHaa\SocketInspect\Message\ProxyStarted;
use PeeHaa\SocketInspect\MessageBroker\Broker;
use PeeHaa\SocketInspect\Proxy\Server as TargetServer;
use function Amp\asyncCall;
use function Amp\call;
use function Amp\Socket\listen;

class Proxy
{
    private $proxyAddress;

    private $serverAddress;

    private $messageBroker;

    public function __construct(Address $proxyAddress, Address $serverAddress, Broker $messageBroker)
    {
        $this->proxyAddress  = $proxyAddress;
        $this->serverAddress = $serverAddress;
        $this->messageBroker = $messageBroker;
    }

    public function start(): Promise
    {
        return call(function() {
            $proxy = listen($this->proxyAddress->getAddress());

            $this->messageBroker->send(new ProxyStarted($this->proxyAddress));

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
            $this->messageBroker->send(new ClientConnected($this->proxyAddress, $clientSocket->getRemoteAddress()));

            /** @var TargetServer $server */
            $server = yield (new TargetServer(
                $this->proxyAddress,
                $this->serverAddress,
                $clientSocket->getRemoteAddress(),
                $this->messageBroker
            ))->start();

            $client = new Client($this->proxyAddress, $clientSocket, $this->messageBroker);

            asyncCall(static function() use ($server, $client) {
                try {
                    $server->onReceived(\Closure::fromCallable([$client, 'send']));
                    $client->onReceived(\Closure::fromCallable([$server, 'send']));

                    $server->onClose(\Closure::fromCallable([$client, 'close']));
                    $client->onClose(\Closure::fromCallable([$server, 'close']));
                } catch (StreamException $e) {
                    // we catch and silence writing to closed connections here
                    // as it seems it's normal operation?
                }
            });
        });
    }
}
