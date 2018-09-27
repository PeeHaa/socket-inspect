<?php declare(strict_types=1);

namespace PeeHaa\SocketInspectTest\Unit\MessageBroker;

use Amp\Loop;
use PeeHaa\SocketInspect\Message\ClientSent;
use PeeHaa\SocketInspect\Message\ProxyStarted;
use PeeHaa\SocketInspect\Message\ServerSent;
use PeeHaa\SocketInspect\MessageBroker\StdOut;
use PeeHaa\SocketInspect\Proxy\Address;
use PHPUnit\Framework\TestCase;

class StdOutTest extends TestCase
{
    public function testSendIncludesColoredLabel()
    {
        Loop::run(function() {
            $message = new ProxyStarted(new Address('tcp://127.0.0.1:1337'));

            $stream = fopen('php://memory', 'r+');

            $stdOut = new StdOut($stream);

            $stdOut->send($message);

            rewind($stream);

            $infoColorPrefix = "\e[0;33m[PROXY]\e[0m";

            $output = stream_get_contents($stream);

            $this->assertRegExp('~' . preg_quote($infoColorPrefix) . '~', $output);
        });
    }

    public function testSendIncludesColoredLabelWithClientAddressForClientInitiator()
    {
        Loop::run(function() {
            $message = new ClientSent(new Address('tcp://127.0.0.1:1337'), 'The message', 'tcp://127.0.0.1:50234');

            $stream = fopen('php://memory', 'r+');

            $stdOut = new StdOut($stream);

            $stdOut->send($message);

            rewind($stream);

            $infoColorPrefix = "\e[4;36m[CLIENT tcp://127.0.0.1:50234]\e[0m";

            $output = stream_get_contents($stream);

            $this->assertRegExp('~' . preg_quote($infoColorPrefix) . '~', $output);
        });
    }

    public function testSendIncludesColoredLabelWithClientAddressForServerInitiator()
    {
        Loop::run(function() {
            $message = new ServerSent(new Address('tcp://127.0.0.1:1337'), 'The message', 'tcp://127.0.0.1:50234');

            $stream = fopen('php://memory', 'r+');

            $stdOut = new StdOut($stream);

            $stdOut->send($message);

            rewind($stream);

            $infoColorPrefix = "\e[0;96m[SERVER tcp://127.0.0.1:50234]\e[0m";

            $output = stream_get_contents($stream);

            $this->assertRegExp('~' . preg_quote($infoColorPrefix) . '~', $output);
        });
    }

    public function testSendIncludesTimestamp()
    {
        Loop::run(function() {
            $message = new ProxyStarted(new Address('tcp://127.0.0.1:1337'));

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
            $message = new ProxyStarted(new Address('tcp://127.0.0.1:1337'));

            $stream = fopen('php://memory', 'r+');

            $stdOut = new StdOut($stream);

            $stdOut->send($message);

            rewind($stream);

            $output = stream_get_contents($stream);

            $this->assertRegExp('~Started proxy on tcp://127.0.0.1:1337' . PHP_EOL . '$~', $output);
        });
    }
}
