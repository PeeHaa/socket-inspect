<?php declare(strict_types=1);

namespace PeeHaa\SocketInspectTest\Unit\Message;

use PeeHaa\SocketInspect\Message\Enum\Initiator;
use PeeHaa\SocketInspect\Message\Message;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class MessageTest extends TestCase
{
    /** @var MockObject|Message */
    private $message;

    public function setUp()
    {
        $this->message = new class extends Message {
            public function __construct()
            {
                parent::__construct('tcp://127.0.0.1:1337', Initiator::PROXY(), 'The message', 'tcp://127.0.0.1:50234');
            }
        };
    }

    public function testGetProxyAddress()
    {
        $this->assertSame('tcp://127.0.0.1:1337', $this->message->getProxyAddress());
    }

    public function testGetClientWhenNull()
    {
        $message = new class extends Message {
            public function __construct()
            {
                parent::__construct('tcp://127.0.0.1:1337', Initiator::PROXY(), 'The message');
            }
        };

        $this->assertNull($message->getClient());
    }

    public function testGetClient()
    {
        $this->assertSame('tcp://127.0.0.1:50234', $this->message->getClient());
    }

    public function testGetInitiator()
    {
        $this->assertTrue($this->message->getInitiator()->equals(Initiator::PROXY()));
    }

    public function testSetTimestamp()
    {
        $this->assertInstanceOf(\DateTimeImmutable::class, $this->message->getTimestamp());
    }

    public function testSetsMessage()
    {
        $this->assertSame('The message', $this->message->getMessage());
    }

    public function testJsonSerializerContainsAllKeys()
    {
        $jsonData = json_decode(json_encode($this->message), true);

        $this->assertArrayHasKey('proxy', $jsonData);
        $this->assertArrayHasKey('client', $jsonData);
        $this->assertArrayHasKey('initiator', $jsonData);
        $this->assertArrayHasKey('timestamp', $jsonData);
        $this->assertArrayHasKey('message', $jsonData);
    }

    public function testJsonSerializeSetsProxy()
    {
        $jsonData = json_decode(json_encode($this->message), true);

        $this->assertSame('tcp://127.0.0.1:1337', $jsonData['proxy']);
    }

    public function testJsonSerializeSetsClient()
    {
        $jsonData = json_decode(json_encode($this->message), true);

        $this->assertSame('tcp://127.0.0.1:50234', $jsonData['client']);
    }

    public function testJsonSerializeSetsInitiator()
    {
        $jsonData = json_decode(json_encode($this->message), true);

        $this->assertSame('proxy', $jsonData['initiator']);
    }

    public function testJsonSerializeSetsTimestamp()
    {
        $jsonData = json_decode(json_encode($this->message), true);

        $this->assertRegExp('~^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\.\d{6}$~', $jsonData['timestamp']);
    }

    public function testJsonSerializeSetsMessage()
    {
        $jsonData = json_decode(json_encode($this->message), true);

        $this->assertSame('The message', $jsonData['message']);
    }
}
