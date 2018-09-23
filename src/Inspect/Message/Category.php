<?php declare(strict_types=1);

namespace PeeHaa\SocketInspect\Inspect\Message;

use MyCLabs\Enum\Enum;

/**
 * @method static Category SERVER()
 * @method static Category INCOMING()
 * @method static Category OUTGOING()
 */
final class Category extends Enum
{
    // phpcs:disable SlevomatCodingStandard.Classes.UnusedPrivateElements.UnusedConstant
    private const SERVER   = 'server';
    private const INCOMING = 'incoming';
    private const OUTGOING = 'outgoing';
    // phpcs:enable SlevomatCodingStandard.Classes.UnusedPrivateElements.UnusedConstant
}
