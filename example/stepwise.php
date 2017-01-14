<?php

require_once(__DIR__."/../vendor/autoload.php");
require_once(__DIR__."/HelloTask.php");

use Lechimp\Tsks\Runtime\Console;
use Lechimp\Tsks\Runtime\Stepwise;

$console = new Console();
$step_wise = new Stepwise($console, "stepwise.state");
$step_wise->run(new HelloTask());
