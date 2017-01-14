<?php

require_once(__DIR__."/HelloTask.php");
require_once(__DIR__."/../src/Runtime/Console.php");

use Lechimp\Tsks\Runtime\Console;

$console = new Console();
$console->run(new HelloTask());
