<?php declare(strict_types=1);

namespace PeeHaa\SocketInspect\MessageBroker;

use PeeHaa\SocketInspect\Http\WebSocket as WebSocketApplication;
use PeeHaa\SocketInspect\Inspect\Message\Message;
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
            /** @var WebSocketApplication $webSocket */
            foreach ($this->webSockets as $webSocket) {
                $webSocket->broadcast($message);
            }
        });
    }
}
