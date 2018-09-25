<?php declare(strict_types=1);

namespace PeeHaa\SocketInspect\Inspect\Proxy;

use Amp\Socket\ServerSocket;
use PeeHaa\SocketInspect\Inspect\Message\Broker;
use PeeHaa\SocketInspect\Inspect\Message\Incoming\Received;
use PeeHaa\SocketInspect\Inspect\Message\Server\ClientDisconnect;

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

    public function handleMessages(): \Generator // @todo convert to promise
    {
        while (($chunk = yield $this->clientSocket->read()) !== null) {
            $this->messageBroker->send(new Received($this->proxyAddress, $chunk));

            yield from $this->targetSocket->send($chunk, $this->clientSocket);
        }

        $this->messageBroker->send(new ClientDisconnect($this->proxyAddress));
    }
}
