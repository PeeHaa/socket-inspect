<?php declare(strict_types=1);

namespace PeeHaa\SocketInspect\MessageBroker;

use Amp\ByteStream\ResourceOutputStream;
use PeeHaa\SocketInspect\Message\Message;
use function Amp\asyncCall;

class StdOut implements Broker
{
    // phpcs:disable SlevomatCodingStandard.Classes.UnusedPrivateElements.UnusedConstant
    private const ERROR_PREFIX = "\e[31m[ERROR]\e[0m";
    private const INFO_PREFIX = "\e[1;34m[INFO]\e[0m";
    private const SUCCESS_PREFIX = "\e[32m[DONE]\e[0m";
    // phpcs:enable SlevomatCodingStandard.Classes.UnusedPrivateElements.UnusedConstant

    private $stdOut;

    public function __construct($stream)
    {
        $this->stdOut = new ResourceOutputStream($stream);
    }

    public function send(Message $message): void
    {
        asyncCall(function() use ($message) {
            yield $this->stdOut->write($this->buildMessage($message));
        });
    }

    private function buildMessage(Message $message): string
    {
        return sprintf(
            '%s %s %s::%s %s',
            $this->getSeverityPrefix($message->getSeverity()),
            $message->getTimestamp()->format('Y-m-d H:i:s.u'),
            $message->getCategory(),
            $message->getType(),
            $message->getMessage()
        ) . PHP_EOL;
    }

    private function getSeverityPrefix(Severity $severity): string
    {
        return constant(sprintf('self::%s_PREFIX', $severity->getKey()));
    }
}
