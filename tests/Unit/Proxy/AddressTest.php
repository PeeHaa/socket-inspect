<?php declare(strict_types=1);

namespace PeeHaa\SocketInspectTest\Unit\Proxy;

use PeeHaa\SocketInspect\Proxy\Address;
use PHPUnit\Framework\TestCase;

class AddressTest extends TestCase
{
    public function testGetAddress()
    {
        $address = new Address('tcp://127.0.0.1:1337');

        $this->assertSame('tcp://127.0.0.1:1337', $address->getAddress());
    }

    public function testIsEncryptedReturnsTrue()
    {
        $address = new Address('tcp://127.0.0.1:1337', true);

        $this->assertTrue($address->isEncrypted());
    }

    public function testIsEncryptedReturnsFalse()
    {
        $address = new Address('tcp://127.0.0.1:1337');

        $this->assertFalse($address->isEncrypted());
    }
}
