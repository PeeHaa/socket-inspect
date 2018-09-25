<?php declare(strict_types=1);

namespace PeeHaa\SocketInspect\Inspect\Proxy;

use Amp\Promise;
use Amp\Socket\ServerSocket;
use PeeHaa\SocketInspect\Inspect\Message\Broker;
use PeeHaa\SocketInspect\Inspect\Message\Incoming\Received;
use PeeHaa\SocketInspect\Inspect\Message\Server\ClientDisconnect;
use function Amp\asyncCall;

class Client
{
    private $proxyAddress;

    private $clientSocket;

    private $targetSocket;

    private $messageBroker;

    /** @var null|callable */
    private $onCloseCallback;

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

            $this->doClose();
        });
    }

    private function doClose(): void
    {
        $this->messageBroker->send(new ClientDisconnect($this->proxyAddress));

        if ($this->onCloseCallback === null) {
            return;
        }

        ($this->onCloseCallback)();
    }

    public function onClose(callable $callback)
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
