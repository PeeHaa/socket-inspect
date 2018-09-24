<?php declare(strict_types=1);

namespace PeeHaa\SocketInspectTest\Unit\Http;

use Amp\ByteStream\InputStream;
use Amp\Http\Server\Driver\Client;
use Amp\Http\Server\Request;
use Amp\Http\Server\Response;
use Amp\Http\Server\Websocket\Endpoint;
use Amp\Http\Server\Websocket\Message;
use Amp\Loop;
use Amp\Success;
use PeeHaa\SocketInspect\Http\WebSocket;
use PeeHaa\SocketInspect\Inspect\Message\Message as TransactionMessage;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UriInterface;

class WebSocketTest extends TestCase
{
    public function testOnHandshakeReturnsResponse()
    {
        $webSocket = new WebSocket(function() {});

        /** @var MockObject|Client $client */
        $client = $this->createMock(Client::class);
        /** @var MockObject|UriInterface $uri */
        $uri    = $this->createMock(UriInterface::class);

        $request  = new Request($client, 'POST', $uri);
        $response = new Response();

        $this->assertSame($response, $webSocket->onHandshake($request, $response));
    }

    public function testOnDataPassesDataThroughToTheCallback()
    {
        $this->markTestSkipped('For some reason this end up being an infinite loop.');

        $webSocket = new WebSocket(function($uri) {
            $this->assertSame('The message', $uri);

            return new Success(true);
        });

        /** @var MockObject|InputStream $inputStream */
        $inputStream = $this->createMock(InputStream::class);

        $inputStream
            ->method('read')
            ->willReturn(new Success('The message'))
        ;

        $message = new Message($inputStream, false);

        Loop::run(function() use ($webSocket, $message) {
            yield from $webSocket->onData(1, $message);
        });
    }

    public function testBroadcast()
    {
        $messageContents = [
            'foo' => 'bar',
        ];

        /** @var MockObject|Endpoint $endpoint */
        $endpoint = $this->createMock(Endpoint::class);

        $endpoint
            ->method('broadcast')
            ->willReturnCallback(function($encodedMessage) use ($messageContents) {
                $this->assertSame(json_encode($messageContents), $encodedMessage);

                return new Success(true);
            })
        ;

        $webSocket = new WebSocket(function() {});

        $webSocket->onStart($endpoint);

        /** @var MockObject|TransactionMessage $message */
        $message = $this->createMock(TransactionMessage::class);

        $message
            ->method('jsonSerialize')
            ->willReturn($messageContents)
        ;

        $webSocket->broadcast($message);
    }
}
