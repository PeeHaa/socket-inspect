<?php declare(strict_types=1);

namespace PeeHaa\SocketInspectTest\Unit\Message\Server;

use PeeHaa\SocketInspect\Inspect\Message\Category;
use PeeHaa\SocketInspect\Inspect\Message\Message;
use PeeHaa\SocketInspect\Inspect\Message\Server\NewClient;
use PeeHaa\SocketInspect\Inspect\Message\Severity;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class NewClientTest extends TestCase
{
    /** @var MockObject|Message */
    private $message;

    public function setUp()
    {
        $this->message = new NewClient('tcp://127.0.0.1:1337', 'tcp://127.0.0.1:1338');
    }

    public function testSetsServer()
    {
        $this->assertSame('tcp://127.0.0.1:1337', $this->message->getServer());
    }

    public function testSetsCategory()
    {
        $this->assertSame(Category::SERVER()->getKey(), $this->message->getCategory()->getKey());
    }

    public function testSetsType()
    {
        $this->assertSame('newClient', $this->message->getType());
    }

    public function testSetsSeverity()
    {
        $this->assertSame(Severity::INFO()->getKey(), $this->message->getSeverity()->getKey());
    }

    public function testSetsMessage()
    {
        $this->assertSame('Accepted connection from tcp://127.0.0.1:1338', $this->message->getMessage());
    }
}
