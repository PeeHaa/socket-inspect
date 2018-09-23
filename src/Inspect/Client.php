<?php declare(strict_types=1);

namespace PeeHaa\SocketInspect\Inspect;

use Amp\Socket\ServerSocket;
use PeeHaa\SocketInspect\Inspect\Message\Broker;
use PeeHaa\SocketInspect\Inspect\Message\Incoming\Received;
use PeeHaa\SocketInspect\Inspect\Message\Outgoing\Sent;
use PeeHaa\SocketInspect\Inspect\Message\Server\ClientDisconnect;
use function Amp\asyncCall;

class Client
{
    private $socket;

    private $messageBroker;

    public function __construct(ServerSocket $socket, Broker $messageBroker)
    {
        $this->socket        = $socket;
        $this->messageBroker = $messageBroker;
    }

    public function handleMessages(): \Generator
    {
        while (($chunk = yield $this->socket->read()) !== null) {
            $this->messageBroker->send(new Received($chunk));

            $this->send('Well hello there. And welcome to you!');
        }

        $this->messageBroker->send(new ClientDisconnect());
    }

    private function send(string $message): void
    {
        asyncCall(function() use ($message) {
            yield $this->socket->write($message);

            $this->messageBroker->send(new Sent($message));
        });
    }
}
