<?php declare(strict_types=1);

namespace PeeHaa\SocketInspect\Proxy;

use Amp\Promise;
use Amp\Socket\ClientSocket;
use PeeHaa\SocketInspect\Message\ConnectedToServer;
use PeeHaa\SocketInspect\Message\DisconnectedFromServer;
use PeeHaa\SocketInspect\Message\ServerSent;
use PeeHaa\SocketInspect\MessageBroker\Broker;
use function Amp\asyncCall;
use function Amp\call;
use function Amp\Socket\cryptoConnect;

class Server
{
    private $proxyAddress;

    private $serverAddress;

    private $clientAddress;

    private $messageBroker;

    /** @var ClientSocket|null */
    private $socket;

    /** @var \Closure|null */
    private $onCloseCallback;

    public function __construct(
        string $proxyAddress,
        string $serverAddress,
        string $clientAddress,
        Broker $messageBroker
    ) {
        $this->proxyAddress  = $proxyAddress;
        $this->serverAddress = $serverAddress;
        $this->clientAddress = $clientAddress;
        $this->messageBroker = $messageBroker;
    }

    public function start(): Promise
    {
        return call(function() {
            $this->socket = yield cryptoConnect($this->serverAddress);

            $this->messageBroker->send(new ConnectedToServer($this->proxyAddress, $this->serverAddress, $this->clientAddress));

            return $this;
        });
    }

    public function onReceived(\Closure $callback): void
    {
        asyncCall(function() use ($callback) {
            while (($chunk = yield $this->socket->read()) !== null) {
                yield $callback($chunk);

                $this->messageBroker->send(new ServerSent($this->proxyAddress, $chunk));
            }

            $this->doClose();
        });
    }

    private function doClose(): void
    {
        $this->messageBroker->send(
            new DisconnectedFromServer($this->proxyAddress, $this->serverAddress, $this->clientAddress)
        );

        if ($this->onCloseCallback === null) {
            return;
        }

        ($this->onCloseCallback)();
    }

    public function onClose(\Closure $callback)
    {
        $this->onCloseCallback = $callback;
    }

    public function send(string $message): Promise
    {
        return $this->socket->write($message);
    }

    public function close(): void
    {
        $this->socket->close();
    }
}
