<?php declare(strict_types=1);

namespace PeeHaa\SocketInspect\Inspect\Message\Enum;

use MyCLabs\Enum\Enum;

/**
 * @method static Initiator APPLICATION()
 * @method static Initiator PROXY()
 * @method static Initiator CLIENT()
 * @method static Initiator SERVER()
 */
final class Initiator extends Enum
{
    // phpcs:disable SlevomatCodingStandard.Classes.UnusedPrivateElements.UnusedConstant
    private const APPLICATION = 'application';
    private const PROXY       = 'proxy';
    private const CLIENT      = 'client';
    private const SERVER      = 'server';
    // phpcs:enable SlevomatCodingStandard.Classes.UnusedPrivateElements.UnusedConstant
}
