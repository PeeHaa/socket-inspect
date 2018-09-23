<?php declare(strict_types=1);

namespace PeeHaa\SocketInspectTest\Unit\Message;

use PeeHaa\SocketInspect\Inspect\Message\Category;
use PeeHaa\SocketInspect\Inspect\Message\Message;
use PeeHaa\SocketInspect\Inspect\Message\Severity;
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
                parent::__construct(Category::SERVER(), 'test', Severity::INFO(), 'The message');
            }
        };
    }

    public function testSetsCategory()
    {
        $this->assertSame(Category::SERVER()->getKey(), $this->message->getCategory()->getKey());
    }

    public function testSetsType()
    {
        $this->assertSame('test', $this->message->getType());
    }

    public function testSetsSeverity()
    {
        $this->assertSame(Severity::INFO()->getKey(), $this->message->getSeverity()->getKey());
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

        $this->assertArrayHasKey('category', $jsonData);
        $this->assertArrayHasKey('type', $jsonData);
        $this->assertArrayHasKey('severity', $jsonData);
        $this->assertArrayHasKey('timestamp', $jsonData);
        $this->assertArrayHasKey('message', $jsonData);
    }

    public function testJsonSerializeSetsCategory()
    {
        $jsonData = json_decode(json_encode($this->message), true);

        $this->assertSame('server', $jsonData['category']);
    }

    public function testJsonSerializeSetsType()
    {
        $jsonData = json_decode(json_encode($this->message), true);

        $this->assertSame('test', $jsonData['type']);
    }

    public function testJsonSerializeSetsSeverity()
    {
        $jsonData = json_decode(json_encode($this->message), true);

        $this->assertSame('INFO', $jsonData['severity']);
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
