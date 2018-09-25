<?php declare(strict_types=1);

namespace PeeHaa\SocketInspect\Inspect\Proxy;

use Amp\Promise;
use Amp\Socket\ClientSocket;
use PeeHaa\SocketInspect\Inspect\Message\Broker;
use PeeHaa\SocketInspect\Inspect\Message\Outgoing\Sent;
use PeeHaa\SocketInspect\Inspect\Message\Server\NewTarget;
use PeeHaa\SocketInspect\Inspect\Message\Server\ServerDisconnected;
use function Amp\asyncCall;
use function Amp\call;
use function Amp\Socket\cryptoConnect;

class Server
{
    private $proxyAddress;

    private $targetAddress;

    private $messageBroker;

    /** @var null|ClientSocket */
    private $socket;

    /** @var null|callable */
    private $onCloseCallback;

    public function __construct(string $proxyAddress, string $targetAddress, Broker $messageBroker)
    {
        $this->proxyAddress  = $proxyAddress;
        $this->targetAddress = $targetAddress;
        $this->messageBroker = $messageBroker;
    }

    public function start(): Promise
    {
        return call(function() {
            $this->socket = yield cryptoConnect($this->targetAddress);

            $this->messageBroker->send(new NewTarget($this->proxyAddress, $this->targetAddress));

            return $this;
        });
    }

    public function onReceived(callable $callback): void
    {
        asyncCall(function() use ($callback) {
            while (($chunk = yield $this->socket->read()) !== null) {
                yield $callback($chunk);

                $this->messageBroker->send(new Sent($this->proxyAddress, $chunk));
            }

            $this->doClose();
        });
    }

    private function doClose(): void
    {
        $this->messageBroker->send(new ServerDisconnected($this->proxyAddress));

        if ($this->onCloseCallback === null) {
            return;
        }

        ($this->onCloseCallback)();
    }

    public function onClose(callable $callback)
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
