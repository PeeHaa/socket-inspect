<?php declare(strict_types=1);

namespace PeeHaa\SocketInspect\Http;

use Amp\Http\Server\Request;
use Amp\Http\Server\Response;
use Amp\Http\Server\Websocket\Application;
use Amp\Http\Server\Websocket\Endpoint;
use Amp\Http\Server\Websocket\Message;
use PeeHaa\SocketInspect\Message\Message as TransactionMessage;

class WebSocket implements Application
{
    /** @var Endpoint */
    private $endpoint;

    private $onNewServer;

    public function __construct(callable $onNewServer)
    {
        $this->onNewServer = $onNewServer;
    }

    public function onStart(Endpoint $endpoint): void
    {
        $this->endpoint = $endpoint;
    }

    // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
    public function onHandshake(Request $request, Response $response)
    {
        return $response;
    }

    // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
    public function onOpen(int $clientId, Request $request)
    {
        // TODO: Implement onOpen() method.
    }

    // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
    public function onData(int $clientId, Message $message)
    {
        // phpcs:ignore SlevomatCodingStandard.ControlStructures.ControlStructureSpacing.IncorrectLinesCountBeforeControlStructure
        $data = json_decode(yield $message->read(), true);

        // phpcs:ignore SlevomatCodingStandard.ControlStructures.ControlStructureSpacing.IncorrectLinesCountBeforeControlStructure
        yield ($this->onNewServer)($data['proxy_address'], $data['server_address']);
    }

    // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
    public function onClose(int $clientId, int $code, string $reason)
    {
        // TODO: Implement onClose() method.
    }

    public function onStop()
    {
        // TODO: Implement onStop() method.
    }

    public function broadcast(TransactionMessage $message)
    {
        $this->endpoint->broadcast(json_encode($message));
    }
}
