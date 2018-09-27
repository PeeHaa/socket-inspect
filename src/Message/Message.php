<?php declare(strict_types=1);

namespace PeeHaa\SocketInspect\Message;

use PeeHaa\SocketInspect\Message\Enum\Initiator;

abstract class Message implements \JsonSerializable
{
    private $proxyAddress;

    private $client;

    private $initiator;

    private $timestamp;

    private $message;

    public function __construct(string $proxyAddress, Initiator $initiator, string $message, ?string $client = null)
    {
        $this->proxyAddress = $proxyAddress;
        $this->client       = $client;
        $this->initiator    = $initiator;
        $this->timestamp    = new \DateTimeImmutable();
        $this->message      = $message;
    }

    public function getProxyAddress(): string
    {
        return $this->proxyAddress;
    }

    public function getClient(): ?string
    {
        return $this->client;
    }

    public function getInitiator(): Initiator
    {
        return $this->initiator;
    }

    public function getTimestamp(): \DateTimeImmutable
    {
        return $this->timestamp;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function jsonSerialize(): array
    {
        return [
            'proxy'     => $this->proxyAddress,
            'client'    => $this->client,
            'initiator' => $this->initiator->getValue(),
            'timestamp' => $this->timestamp->format('Y-m-d H:i:s.u'),
            'message'   => $this->message,
        ];
    }
}
