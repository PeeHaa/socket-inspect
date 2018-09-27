<?php declare(strict_types=1);

namespace PeeHaa\SocketInspect\Proxy;

use Amp\ByteStream\StreamException;
use Amp\Promise;
use Amp\Socket\ServerSocket;
use Amp\Success;
use PeeHaa\SocketInspect\Message\ClientDisconnected;
use PeeHaa\SocketInspect\Message\ClientSent;
use PeeHaa\SocketInspect\MessageBroker\Broker;
use function Amp\asyncCall;

class Client
{
    private $proxyAddress;

    private $clientSocket;

    private $clientAddress;

    private $messageBroker;

    /** @var \Closure|null */
    private $onCloseCallback;

    public function __construct(Address $proxyAddress, ServerSocket $clientSocket, Broker $messageBroker)
    {
        $this->proxyAddress  = $proxyAddress;
        $this->clientSocket  = $clientSocket;
        $this->clientAddress = $clientSocket->getRemoteAddress();
        $this->messageBroker = $messageBroker;
    }

    public function onReceived(\Closure $callback): void
    {
        asyncCall(function() use ($callback) {
            while (($chunk = yield $this->clientSocket->read()) !== null) {
                yield $callback($chunk);

                $this->messageBroker->send(new ClientSent($this->proxyAddress, $chunk, $this->clientAddress));
            }

            $this->doClose();
        });
    }

    private function doClose(): void
    {
        $this->messageBroker->send(new ClientDisconnected($this->proxyAddress, $this->clientAddress));

        if ($this->onCloseCallback === null) {
            return;
        }

        ($this->onCloseCallback)();
    }

    public function onClose(\Closure $callback)
    {
        $this->onCloseCallback = $callback;
    }

    public function send($message): Promise
    {
        try {
            return $this->clientSocket->write($message);
        } catch (StreamException $e) {
            // we catch and silence writing to closed connections here
            return new Success();
        }
    }

    public function close(): void
    {
        try {
            $this->clientSocket->close();
        } catch (StreamException $e) {
            // we catch and silence closing an already closed connections here
        }
    }
}
