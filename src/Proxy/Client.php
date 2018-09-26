<?php declare(strict_types=1);

namespace PeeHaa\SocketInspect\Proxy;

use Amp\Promise;
use Amp\Socket\ServerSocket;
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

    public function __construct(string $proxyAddress, ServerSocket $clientSocket, Broker $messageBroker)
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

                $this->messageBroker->send(new ClientSent($this->proxyAddress, $chunk));
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
        return $this->clientSocket->write($message);
    }

    public function close(): void
    {
        $this->clientSocket->close();
    }
}
