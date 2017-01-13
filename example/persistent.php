<?php

require_once(__DIR__."/../vendor/autoload.php");

use Lechimp\Tsks\Task;
use Lechimp\Tsks\IO;
use Lechimp\Tsks\Runtime\Console;
use Lechimp\Tsks\Runtime\Stepwise;

class MyTask extends Task {
    public function run(IO $io) {
        yield $io->putLine("Hello! What is your name?");
        yield $io->getLine();
        $name = $io->last_result;
        $greeting = "Hello $name!";
        yield $io->putLine($greeting);
    }
}

$console = new Console();
$step_wise = new Stepwise($console, "persistent.state");
$step_wise->run(new MyTask());
