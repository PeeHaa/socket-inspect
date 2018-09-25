<?php declare(strict_types=1);

namespace PeeHaa\SocketInspect\Inspect\Proxy;

use Amp\Promise;
use Amp\Socket\ServerSocket;
use PeeHaa\SocketInspect\Inspect\Message\Broker;
use PeeHaa\SocketInspect\Inspect\Message\Incoming\Received;
use PeeHaa\SocketInspect\Inspect\Message\Server\ClientDisconnect;
use function Amp\asyncCall;
use function Amp\call;

class Client
{
    private $proxyAddress;

    private $clientSocket;

    private $targetSocket;

    private $messageBroker;

    public function __construct(string $proxyAddress, ServerSocket $clientSocket, Server $targetSocket, Broker $messageBroker)
    {
        $this->proxyAddress  = $proxyAddress;
        $this->clientSocket  = $clientSocket;
        $this->targetSocket  = $targetSocket;
        $this->messageBroker = $messageBroker;
    }

    public function onReceived(callable $callback): void
    {
        asyncCall(function() use ($callback) {
            while (($chunk = yield $this->clientSocket->read()) !== null) {
                yield $callback($chunk);

                $this->messageBroker->send(new Received($this->proxyAddress, $chunk));
            }
        });
    }

    public function handleMessages(): Promise
    {
        return call(function() {
            while (($chunk = yield $this->clientSocket->read()) !== null) {
                $this->messageBroker->send(new Received($this->proxyAddress, $chunk));

                yield $this->targetSocket->send($chunk);
            }

            $this->messageBroker->send(new ClientDisconnect($this->proxyAddress));
        });
    }

    public function send($message): Promise
    {
        return $this->clientSocket->write($message);
    }
}
