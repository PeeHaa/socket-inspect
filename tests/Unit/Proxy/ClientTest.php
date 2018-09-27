<?php declare(strict_types=1);

namespace PeeHaa\SocketInspectTest\Unit\Proxy;

use Amp\ByteStream\StreamException;
use Amp\Loop;
use Amp\Socket\ServerSocket;
use Amp\Success;
use PeeHaa\SocketInspect\MessageBroker\Broker;
use PeeHaa\SocketInspect\Proxy\Address;
use PeeHaa\SocketInspect\Proxy\Client;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    public function testOnReceivedExecutesCallback()
    {
        /** @var MockObject|ServerSocket $clientSocket */
        $clientSocket = $this->createMock(ServerSocket::class);

        $clientSocket
            ->method('getRemoteAddress')
            ->willReturn('127.0.0.1:50234')
        ;

        $clientSocket
            ->expects($this->once())
            ->method('read')
        ;

        /** @var MockObject|Broker $messageBroker */
        $messageBroker = $this->createMock(Broker::class);

        $messageBroker
            ->method('send')
            ->willReturn(new Success())
        ;

        $client = new Client(new Address('tcp://127.0.0.1:1337'), $clientSocket, $messageBroker);

        Loop::run(function() use ($client) {
            $closure = function($message) {
                $this->assertSame('Promise value', $message);

                return new Success();
            };

            $client->onReceived($closure);
        });
    }

    public function testDoCloseExecutesCallback()
    {
        /** @var MockObject|ServerSocket $clientSocket */
        $clientSocket = $this->createMock(ServerSocket::class);

        $clientSocket
            ->method('getRemoteAddress')
            ->willReturn('127.0.0.1:50234')
        ;

        $clientSocket
            ->method('read')
            ->willReturnOnConsecutiveCalls(new Success('Promise value'), new Success(null))
        ;

        /** @var MockObject|Broker $messageBroker */
        $messageBroker = $this->createMock(Broker::class);

        $messageBroker
            ->method('send')
            ->willReturn(new Success())
        ;

        $client = new Client(new Address('tcp://127.0.0.1:1337'), $clientSocket, $messageBroker);

        $closureCalled = false;

        Loop::run(function() use ($client, &$closureCalled) {
            $closure = static function() use (&$closureCalled) {
                $closureCalled = true;

                return new Success();
            };

            $client->onClose($closure);

            $closure = function($message) {
                $this->assertSame('Promise value', $message);

                return new Success();
            };

            $client->onReceived($closure);
        });

        $this->assertTrue($closureCalled);
    }

    public function testSend()
    {
        /** @var MockObject|ServerSocket $clientSocket */
        $clientSocket = $this->createMock(ServerSocket::class);

        /** @var MockObject|Broker $messageBroker */
        $messageBroker = $this->createMock(Broker::class);

        $clientSocket
            ->method('write')
            ->willReturn(new Success('Promise value'))
            ->with('The message')
        ;

        $client = new Client(new Address('tcp://127.0.0.1:1337'), $clientSocket, $messageBroker);

        Loop::run(function() use ($client) {
            // phpcs:ignore SlevomatCodingStandard.ControlStructures.ControlStructureSpacing.IncorrectLinesCountBeforeControlStructure
            $this->assertSame('Promise value', yield $client->send('The message'));
        });
    }

    public function testSendReturnsNullOnException()
    {
        /** @var MockObject|ServerSocket $clientSocket */
        $clientSocket = $this->createMock(ServerSocket::class);

        /** @var MockObject|Broker $messageBroker */
        $messageBroker = $this->createMock(Broker::class);

        $clientSocket
            ->method('write')
            ->willReturnCallback(static function() {
                throw new StreamException();
            })
            ->with('The message')
        ;

        $client = new Client(new Address('tcp://127.0.0.1:1337'), $clientSocket, $messageBroker);

        Loop::run(function() use ($client) {
            // phpcs:ignore SlevomatCodingStandard.ControlStructures.ControlStructureSpacing.IncorrectLinesCountBeforeControlStructure
            $this->assertNull(yield $client->send('The message'));
        });
    }

    public function testCloseClosesConnection()
    {
        /** @var MockObject|ServerSocket $clientSocket */
        $clientSocket = $this->createMock(ServerSocket::class);

        /** @var MockObject|Broker $messageBroker */
        $messageBroker = $this->createMock(Broker::class);

        $clientSocket
            ->expects($this->once())
            ->method('close')
        ;

        $client = new Client(new Address('tcp://127.0.0.1:1337'), $clientSocket, $messageBroker);

        $client->close();
    }

    public function testCloseSilencesExceptions()
    {
        /** @var MockObject|ServerSocket $clientSocket */
        $clientSocket = $this->createMock(ServerSocket::class);

        /** @var MockObject|Broker $messageBroker */
        $messageBroker = $this->createMock(Broker::class);

        $clientSocket
            ->expects($this->once())
            ->method('close')
            ->willReturnCallback(static function() {
                throw new StreamException();
            })
        ;

        $client = new Client(new Address('tcp://127.0.0.1:1337'), $clientSocket, $messageBroker);

        $client->close();
    }
}
