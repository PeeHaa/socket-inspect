<?php declare(strict_types=1);

namespace PeeHaa\SocketInspect\Message;

use PeeHaa\SocketInspect\Message\Enum\Initiator;
use PeeHaa\SocketInspect\Proxy\Address;

abstract class Message implements \JsonSerializable
{
    private $server;

    private $client;

    private $initiator;

    private $timestamp;

    private $message;

    public function __construct(string $server, Initiator $initiator, string $message, ?string $client = null)
    {
        $this->server    = $server;
        $this->client    = $client;
        $this->initiator = $initiator;
        $this->timestamp = new \DateTimeImmutable();
        $this->message   = $message;
    }

    public function getServer(): string
    {
        return $this->server;
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
            'server'    => $this->server,
            'client'    => $this->client,
            'initiator' => $this->initiator->getValue(),
            'timestamp' => $this->timestamp->format('Y-m-d H:i:s.u'),
            'message'   => $this->message,
        ];
    }
}
