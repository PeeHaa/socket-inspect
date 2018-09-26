<?php declare(strict_types=1);

namespace PeeHaa\SocketInspect\Proxy;

class Address
{
    private $address;

    private $encrypted;

    public function __construct(string $address, bool $encrypted = false)
    {
        $this->address   = $address;
        $this->encrypted = $encrypted;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function isEncrypted(): bool
    {
        return $this->encrypted;
    }
}
