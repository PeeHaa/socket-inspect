#!/usr/bin/env php
<?php declare(strict_types=1);

namespace PeeHaa\SocketInspect\Bin;

use PeeHaa\SocketInspect\Binary\Binary;

require_once __DIR__ . '/../bootstrap.php';

(new Binary())->run($argv);
