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
    private $server;

    private $socket;

    private $messageBroker;

    public function __construct(string $server, ServerSocket $socket, Broker $messageBroker)
    {
        $this->server        = $server;
        $this->socket        = $socket;
        $this->messageBroker = $messageBroker;
    }

    public function handleMessages(): \Generator
    {
        while (($chunk = yield $this->socket->read()) !== null) {
            $this->messageBroker->send(new Received($this->server, $chunk));

            $this->send('Well hello there. And welcome to you!');
        }

        $this->messageBroker->send(new ClientDisconnect($this->server));
    }

    private function send(string $message): void
    {
        asyncCall(function() use ($message) {
            yield $this->socket->write($message);

            $this->messageBroker->send(new Sent($this->server, $message));
        });
    }
}
