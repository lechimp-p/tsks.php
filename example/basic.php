<?php
require_once(__DIR__."/../vendor/autoload.php");
require_once(__DIR__."/HelloTask.php");

use Lechimp\Tsks\Runtime\Console;

$console = new Console();
$console->run(new HelloTask());
