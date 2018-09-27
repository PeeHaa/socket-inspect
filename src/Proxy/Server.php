<?php declare(strict_types=1);

namespace PeeHaa\SocketInspect\Proxy;

use Amp\ByteStream\StreamException;
use Amp\Promise;
use Amp\Socket\ClientSocket;
use Amp\Success;
use PeeHaa\SocketInspect\Message\ConnectedToServer;
use PeeHaa\SocketInspect\Message\DisconnectedFromServer;
use PeeHaa\SocketInspect\Message\ServerSent;
use PeeHaa\SocketInspect\MessageBroker\Broker;
use function Amp\asyncCall;
use function Amp\call;
use function Amp\Socket\connect;
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
        Address $proxyAddress,
        Address $serverAddress,
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
            if ($this->serverAddress->isEncrypted()) {
                $this->socket = yield cryptoConnect($this->serverAddress->getAddress());
            } else {
                $this->socket = yield connect($this->serverAddress->getAddress());
            }

            $this->messageBroker->send(new ConnectedToServer($this->proxyAddress, $this->serverAddress, $this->clientAddress));

            return $this;
        });
    }

    public function onReceived(\Closure $callback): void
    {
        asyncCall(function() use ($callback) {
            while (($chunk = yield $this->socket->read()) !== null) {
                yield $callback($chunk);

                $this->messageBroker->send(new ServerSent($this->proxyAddress, $chunk, $this->clientAddress));
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
        try {
            return $this->socket->write($message);
        } catch (StreamException $e) {
            // we catch and silence writing to closed connections here
            return new Success();
        }
    }

    public function close(): void
    {
        try {
            $this->socket->close();
        } catch (StreamException $e) {
            // we catch and silence closing an already closed connections here
        }
    }
}
