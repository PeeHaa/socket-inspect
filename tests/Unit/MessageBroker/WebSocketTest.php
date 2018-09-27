<?php declare(strict_types=1);

namespace PeeHaa\SocketInspectTest\Unit\MessageBroker;

use Amp\Loop;
use PeeHaa\SocketInspect\Http\WebSocket as WebSocketApplication;
use PeeHaa\SocketInspect\Message\ProxyStarted;
use PeeHaa\SocketInspect\MessageBroker\WebSocket;
use PeeHaa\SocketInspect\Proxy\Address;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class WebSocketTest extends TestCase
{
    public function testSendingMessageToEndpoint()
    {
        Loop::run(function() {
            $broker = new WebSocket();

            $originalMessage = new ProxyStarted(new Address('tcp://127.0.0.1'));

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
