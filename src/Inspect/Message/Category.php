<?php declare(strict_types=1);

namespace PeeHaa\SocketInspect\Inspect\Message;

use MyCLabs\Enum\Enum;

/**
 * @method static Category SERVER()
 */
final class Category extends Enum
{
    // phpcs:disable SlevomatCodingStandard.Classes.UnusedPrivateElements.UnusedConstant
    private const SERVER = 'server';
    // phpcs:enable SlevomatCodingStandard.Classes.UnusedPrivateElements.UnusedConstant
}
