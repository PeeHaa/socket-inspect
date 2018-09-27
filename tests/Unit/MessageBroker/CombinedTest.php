<?php declare(strict_types=1);

namespace PeeHaa\SocketInspectTest\Unit\MessageBroker;

use PeeHaa\SocketInspect\Message\Message;
use PeeHaa\SocketInspect\Message\ProxyStarted;
use PeeHaa\SocketInspect\MessageBroker\Broker;
use PeeHaa\SocketInspect\MessageBroker\Combined;
use PeeHaa\SocketInspect\Proxy\Address;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CombinedTest extends TestCase
{
    public function testSendSendsToAllBrokers()
    {
        $combined = new Combined();

        /** @var MockObject|Broker $broker1 */
        $broker1 = $this->createMock(Broker::class);
        /** @var MockObject|Broker $broker2 */
        $broker2 = $this->createMock(Broker::class);

        $originalMessage = new ProxyStarted(new Address('tcp://127.0.0.1:1337'));

        $broker1
            ->method('send')
            ->willReturnCallback(function(Message $message) use ($originalMessage) {
                $this->assertSame($originalMessage, $message);
            })
        ;

        $broker2
            ->method('send')
            ->willReturnCallback(function(Message $message) use ($originalMessage) {
                $this->assertSame($originalMessage, $message);
            })
        ;

        $combined->registerBroker($broker1);
        $combined->registerBroker($broker2);

        $combined->send($originalMessage);
    }
}
