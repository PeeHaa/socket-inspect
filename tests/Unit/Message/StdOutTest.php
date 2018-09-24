<?php declare(strict_types=1);

namespace PeeHaa\SocketInspectTest\Unit\Message;

use Amp\Loop;
use PeeHaa\SocketInspect\Inspect\Message\Server\Start;
use PeeHaa\SocketInspect\Inspect\Message\StdOut;
use PHPUnit\Framework\TestCase;

class StdOutTest extends TestCase
{
    public function testSendIncludesColoredLabel()
    {
        Loop::run(function() {
            $message = new Start('tcp://127.0.0.1');

            $stream = fopen('php://memory', 'r+');

            $stdOut = new StdOut($stream);

            $stdOut->send($message);

            rewind($stream);

            $infoColorPrefix = "\e[1;34m[INFO]\e[0m";

            $output = stream_get_contents($stream);

            $this->assertRegExp('~^' . preg_quote($infoColorPrefix) . '~', $output);
        });
    }

    public function testSendIncludesTimestamp()
    {
        Loop::run(function() {
            $message = new Start('tcp://127.0.0.1');

            $stream = fopen('php://memory', 'r+');

            $stdOut = new StdOut($stream);

            $stdOut->send($message);

            rewind($stream);

            $output = stream_get_contents($stream);

            $this->assertRegExp('~\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\.\d{6}~', $output);
        });
    }

    public function testSendIncludesMessage()
    {
        Loop::run(function() {
            $message = new Start('tcp://127.0.0.1');

            $stream = fopen('php://memory', 'r+');

            $stdOut = new StdOut($stream);

            $stdOut->send($message);

            rewind($stream);

            $output = stream_get_contents($stream);

            $this->assertRegExp('~server::start Listening for new connections on tcp://127.0.0.1' . PHP_EOL . '$~', $output);
        });
    }
}
