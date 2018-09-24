<?php declare(strict_types=1);

namespace PeeHaa\SocketInspectTest\Unit\Message;

use Amp\Loop;
use PeeHaa\SocketInspect\Http\WebSocket as WebSocketApplication;
use PeeHaa\SocketInspect\Inspect\Message\Server\Start;
use PeeHaa\SocketInspect\Inspect\Message\WebSocket;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class WebSocketTest extends TestCase
{
    public function testSendingMessageToEndpoint()
    {
        Loop::run(function() {
            $broker = new WebSocket();

            $originalMessage = new Start('tcp://127.0.0.1');

            /** @var MockObject|WebSocketApplication $application */
            $application = $this->createMock(WebSocketApplication::class);

            $application
                ->method('broadcast')
                ->willReturnCallback(function($message) use ($originalMessage) {
                    $this->assertSame($originalMessage, $message);
                })
            ;

            $broker->registerWebSocket($application);

            $broker->send($originalMessage);
        });
    }
}
