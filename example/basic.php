<?php

require_once(__DIR__."/../src/Task.php");
require_once(__DIR__."/../src/IO.php");
require_once(__DIR__."/../src/Runtime/Console.php");

use Lechimp\Tsks\Task;
use Lechimp\Tsks\IO;
use Lechimp\Tsks\Runtime\Console;

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
$console->run(new MyTask());
