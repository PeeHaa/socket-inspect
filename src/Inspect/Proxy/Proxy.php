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

    private function processClients(Server $server): void // use call and return a promise?
    {
        asyncCall(function() use ($server) {
            /** @var ServerSocket $socket */
            while ($socket = yield $server->accept()) {
                asyncCall(function() use ($socket) {
                    $this->messageBroker->send(new NewClient($this->proxyAddress, $socket->getRemoteAddress()));

                    $server = yield (new TargetServer($this->proxyAddress, $this->targetAddress, $this->messageBroker))->start();

                    yield from (new Client($this->proxyAddress, $socket, $server, $this->messageBroker))->handleMessages();
                }, $socket);
            }
        });
    }
}
