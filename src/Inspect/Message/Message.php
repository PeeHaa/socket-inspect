<?php declare(strict_types=1);

namespace PeeHaa\SocketInspect\Inspect\Message;

abstract class Message implements \JsonSerializable
{
    private $server;

    private $category;

    private $type;

    private $severity;

    private $timestamp;

    private $message;

    public function __construct(string $server, Category $category, string $type, Severity $severity, string $message)
    {
        $this->server    = $server;
        $this->category  = $category;
        $this->type      = $type;
        $this->severity  = $severity;
        $this->timestamp = new \DateTimeImmutable();
        $this->message   = $message;
    }

    public function getServer(): string
    {
        return $this->server;
    }

    public function getCategory(): Category
    {
        return $this->category;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getSeverity(): Severity
    {
        return $this->severity;
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
            'category'  => $this->category->getValue(),
            'type'      => $this->type,
            'severity'  => $this->severity->getKey(),
            'timestamp' => $this->timestamp->format('Y-m-d H:i:s.u'),
            'message'   => $this->message,
        ];
    }
}
