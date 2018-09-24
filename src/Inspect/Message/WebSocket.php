<?php declare(strict_types=1);

namespace PeeHaa\SocketInspect\Inspect\Message;

use PeeHaa\SocketInspect\Http\WebSocket as WebSocketApplication;
use function Amp\asyncCall;

class WebSocket implements Broker
{
    private $webSocket;

    public function __construct(WebSocketApplication $webSocket)
    {
        $this->webSocket = $webSocket;
    }

    public function send(Message $message): void
    {
        asyncCall(function() use ($message) {
            $this->webSocket->broadcast($message);
        });
    }
}
