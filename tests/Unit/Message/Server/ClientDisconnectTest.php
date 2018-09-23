<?php declare(strict_types=1);

namespace PeeHaa\SocketInspectTest\Unit\Message\Server;

use PeeHaa\SocketInspect\Inspect\Message\Category;
use PeeHaa\SocketInspect\Inspect\Message\Message;
use PeeHaa\SocketInspect\Inspect\Message\Server\ClientDisconnect;
use PeeHaa\SocketInspect\Inspect\Message\Severity;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ClientDisconnectTest extends TestCase
{
    /** @var MockObject|Message */
    private $message;

    public function setUp()
    {
        $this->message = new ClientDisconnect();
    }

    public function testSetsCategory()
    {
        $this->assertSame(Category::SERVER()->getKey(), $this->message->getCategory()->getKey());
    }

    public function testSetsType()
    {
        $this->assertSame('clientDisconnect', $this->message->getType());
    }

    public function testSetsSeverity()
    {
        $this->assertSame(Severity::INFO()->getKey(), $this->message->getSeverity()->getKey());
    }

    public function testSetsMessage()
    {
        $this->assertSame('Client disconnected', $this->message->getMessage());
    }
}
