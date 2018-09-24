<?php declare(strict_types=1);

namespace PeeHaa\SocketInspectTest\Unit\Message\Outgoing;

use PeeHaa\SocketInspect\Inspect\Message\Category;
use PeeHaa\SocketInspect\Inspect\Message\Message;
use PeeHaa\SocketInspect\Inspect\Message\Outgoing\Sent;
use PeeHaa\SocketInspect\Inspect\Message\Severity;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class SentTest extends TestCase
{
    /** @var MockObject|Message */
    private $message;

    public function setUp()
    {
        $this->message = new Sent('tcp://127.0.0.1:1337', 'This is the message.');
    }

    public function testSetsServer()
    {
        $this->assertSame('tcp://127.0.0.1:1337', $this->message->getServer());
    }

    public function testSetsCategory()
    {
        $this->assertSame(Category::OUTGOING()->getKey(), $this->message->getCategory()->getKey());
    }

    public function testSetsType()
    {
        $this->assertSame('message', $this->message->getType());
    }

    public function testSetsSeverity()
    {
        $this->assertSame(Severity::INFO()->getKey(), $this->message->getSeverity()->getKey());
    }

    public function testSetsMessage()
    {
        $this->assertSame('This is the message.', $this->message->getMessage());
    }
}
