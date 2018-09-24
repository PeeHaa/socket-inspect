<?php declare(strict_types=1);

namespace PeeHaa\SocketInspect\Http;

use Amp\Http\Server\Request;
use Amp\Http\Server\Response;
use Amp\Http\Server\Websocket\Application;
use Amp\Http\Server\Websocket\Endpoint;
use Amp\Http\Server\Websocket\Message;

class WebSocket implements Application
{
    /** @var Endpoint */
    private $endpoint;

    public function onStart(Endpoint $endpoint): void
    {
        $this->endpoint = $endpoint;
    }

    public function onHandshake(Request $request, Response $response)
    {
        return $response;
    }

    public function onOpen(int $clientId, Request $request)
    {
        // TODO: Implement onOpen() method.
    }

    public function onData(int $clientId, Message $message)
    {
        // TODO: Implement onData() method.
    }

    public function onClose(int $clientId, int $code, string $reason)
    {
        // TODO: Implement onClose() method.
    }

    public function onStop()
    {
        // TODO: Implement onStop() method.
    }

    public function broadcast(\PeeHaa\SocketInspect\Inspect\Message\Message $message)
    {
        $this->endpoint->broadcast(json_encode($message));
    }
}
