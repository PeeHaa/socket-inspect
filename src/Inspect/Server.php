<?php declare(strict_types=1);

namespace PeeHaa\SocketInspect\Inspect;

use Amp\Promise;
use Amp\Socket\ServerSocket;
use PeeHaa\SocketInspect\Inspect\Message\Broker;
use PeeHaa\SocketInspect\Inspect\Message\Server\NewClient;
use PeeHaa\SocketInspect\Inspect\Message\Server\Start;
use function Amp\asyncCall;
use function Amp\call;
use function Amp\Socket\listen;

class Server
{
    private $uri;

    private $messageBroker;

    public function __construct(string $uri, Broker $messageBroker)
    {
        $this->uri           = $uri;
        $this->messageBroker = $messageBroker;
    }

    public function start(): Promise
    {
        return call(function() {
            $server = listen($this->uri);

            $this->messageBroker->send(new Start($this->uri));

            /** @var ServerSocket $socket */
            while ($socket = yield $server->accept()) {
                asyncCall(function() use ($socket) {
                    $this->messageBroker->send(new NewClient($socket->getRemoteAddress()));

                    yield from (new Client($socket, $this->messageBroker))->handleMessages();
                }, $socket);
            }
        });
    }
}
