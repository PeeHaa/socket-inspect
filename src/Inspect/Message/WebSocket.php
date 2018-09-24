<?php declare(strict_types=1);

namespace PeeHaa\SocketInspect\Inspect\Message;

use PeeHaa\SocketInspect\Http\WebSocket as WebSocketApplication;
use function Amp\asyncCall;

class WebSocket implements Broker
{
    private $webSockets = [];

    public function registerWebSocket(WebSocketApplication $webSocket): void
    {
        $this->webSockets[] = $webSocket;
    }

    public function send(Message $message): void
    {
        asyncCall(function() use ($message) {
            foreach ($this->webSockets as $webSocket) {
                $webSocket->broadcast($message);
            }
        });
    }
}
