<?php declare(strict_types=1);

namespace PeeHaa\SocketInspectTest\Unit\Message;

use PeeHaa\SocketInspect\Message\Enum\Initiator;
use PeeHaa\SocketInspect\Message\Message;
use PeeHaa\SocketInspect\Message\ProxyStarted;
use PeeHaa\SocketInspect\Proxy\Address;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ProxyStartedTest extends TestCase
{
    /** @var MockObject|Message */
    private $message;

    public function setUp()
    {
        $this->message = new ProxyStarted(new Address('tcp://127.0.0.1:1337'));
    }

    public function testGetProxyAddress()
    {
        $this->assertSame('tcp://127.0.0.1:1337', $this->message->getProxyAddress());
    }

    public function testGetClient()
    {
        $this->assertNull($this->message->getClient());
    }

    public function testGetInitiator()
    {
        $this->assertTrue($this->message->getInitiator()->equals(Initiator::PROXY()));
    }

    public function testGetMessage()
    {
        $this->assertSame('Started proxy on tcp://127.0.0.1:1337', $this->message->getMessage());
    }

    public function testGetMessageOnEncryptedProxy()
    {
        $message = new ProxyStarted(new Address('tcp://127.0.0.1:1337', true));

        $this->assertSame('Started (encrypted) proxy on tcp://127.0.0.1:1337', $message->getMessage());
    }
}
