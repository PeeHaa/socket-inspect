<?php declare(strict_types=1);

namespace PeeHaa\SocketInspectTest\Unit\Message;

use PeeHaa\SocketInspect\Message\Enum\Initiator;
use PeeHaa\SocketInspect\Message\Message;
use PeeHaa\SocketInspect\Message\ServerSent;
use PeeHaa\SocketInspect\Proxy\Address;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ServerSentTest extends TestCase
{
    /** @var MockObject|Message */
    private $message;

    public function setUp()
    {
        $this->message = new ServerSent(new Address('tcp://127.0.0.1:1337'), 'the message', 'tcp://127.0.0.1:50234');
    }

    public function testGetProxyAddress()
    {
        $this->assertSame('tcp://127.0.0.1:1337', $this->message->getProxyAddress());
    }

    public function testGetClient()
    {
        $this->assertSame('tcp://127.0.0.1:50234', $this->message->getClient());
    }

    public function testGetInitiator()
    {
        $this->assertTrue($this->message->getInitiator()->equals(Initiator::SERVER()));
    }

    public function testGetMessage()
    {
        $this->assertSame('the message', $this->message->getMessage());
    }
}
