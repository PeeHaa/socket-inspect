<?php declare(strict_types=1);

namespace PeeHaa\SocketInspectTest\Unit\Message\Server;

use PeeHaa\SocketInspect\Inspect\Message\Category;
use PeeHaa\SocketInspect\Inspect\Message\Message;
use PeeHaa\SocketInspect\Inspect\Message\Server\Start;
use PeeHaa\SocketInspect\Inspect\Message\Severity;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class StartTest extends TestCase
{
    /** @var MockObject|Message */
    private $message;

    public function setUp()
    {
        $this->message = new Start('tcp://127.0.0.1:1337');
    }

    public function testSetsCategory()
    {
        $this->assertSame(Category::SERVER()->getKey(), $this->message->getCategory()->getKey());
    }

    public function testSetsType()
    {
        $this->assertSame('start', $this->message->getType());
    }

    public function testSetsSeverity()
    {
        $this->assertSame(Severity::INFO()->getKey(), $this->message->getSeverity()->getKey());
    }

    public function testSetsMessage()
    {
        $this->assertSame('Listening for new connections on tcp://127.0.0.1:1337', $this->message->getMessage());
    }
}
