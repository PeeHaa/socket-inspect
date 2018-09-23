<?php declare(strict_types=1);

namespace PeeHaa\SocketInspect\Inspect\Message;

use MyCLabs\Enum\Enum;

/**
 * @method static Severity INFO()
 * @method static Severity WARNING()
 * @method static Severity ERROR()
 */
final class Severity extends Enum
{
    // phpcs:disable SlevomatCodingStandard.Classes.UnusedPrivateElements.UnusedConstant
    private const INFO    = 1;
    private const WARNING = 2;
    private const ERROR   = 3;
    // phpcs:enable SlevomatCodingStandard.Classes.UnusedPrivateElements.UnusedConstant
}
