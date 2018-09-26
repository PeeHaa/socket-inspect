<?php declare(strict_types=1);

namespace PeeHaa\SocketInspect\Message;

use PeeHaa\SocketInspect\Message\Enum\Initiator;

abstract class Message implements \JsonSerializable
{
    private $server;

    private $initiator;

    private $timestamp;

    private $message;

    public function __construct(string $server, Initiator $initiator, string $message)
    {
        $this->server    = $server;
        $this->initiator = $initiator;
        $this->timestamp = new \DateTimeImmutable();
        $this->message   = $message;
    }

    public function getServer(): string
    {
        return $this->server;
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
            'initiator' => $this->initiator->getValue(),
            'timestamp' => $this->timestamp->format('Y-m-d H:i:s.u'),
            'message'   => $this->message,
        ];
    }
}
