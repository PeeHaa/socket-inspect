<?php declare(strict_types=1);

namespace PeeHaa\SocketInspectTest\Unit\Binary;

use PeeHaa\SocketInspect\Binary\Binary;
use PHPUnit\Framework\TestCase;

class BinaryTest extends TestCase
{
    public function testRunEchosHelpText()
    {
        $binary = new Binary();

        $this->expectOutputRegex('~sinspect~');

        $binary->run(['sinspect']);
    }
}
