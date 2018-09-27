<?php declare(strict_types=1);

namespace PeeHaa\SocketInspect\MessageBroker;

use Amp\ByteStream\ResourceOutputStream;
use PeeHaa\SocketInspect\Message\Enum\Initiator;
use PeeHaa\SocketInspect\Message\Message;
use function Amp\asyncCall;

class StdOut implements Broker
{
    // phpcs:disable SlevomatCodingStandard.Classes.UnusedPrivateElements.UnusedConstant
    private const PREFIX_PROXY  = "\e[0;33m[PROXY]\e[0m";
    private const PREFIX_CLIENT = "\e[4;36m[CLIENT %s]\e[0m";
    private const PREFIX_SERVER = "\e[0;96m[SERVER %s]\e[0m";
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
            '%s %s %s %s',
            $message->getProxyAddress(),
            $this->getInitiatorPrefix($message->getInitiator(), $message->getClient()),
            $message->getTimestamp()->format('Y-m-d H:i:s.u'),
            $message->getMessage()
        ) . PHP_EOL;
    }

    private function getInitiatorPrefix(Initiator $initiator, ?string $clientAddress = null): string
    {
        $prefix = constant(sprintf('self::PREFIX_%s', $initiator->getKey()));

        if ($clientAddress !== null) {
            $prefix = sprintf($prefix, $clientAddress);
        }

        return $prefix;
    }
}
