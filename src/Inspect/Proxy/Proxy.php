<?php declare(strict_types=1);

namespace PeeHaa\SocketInspect\Inspect\Proxy;

use Amp\ByteStream\StreamException;
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

            /** @var TargetServer $server */
            $server = yield (new TargetServer($this->proxyAddress, $this->targetAddress, $this->messageBroker))->start();
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
