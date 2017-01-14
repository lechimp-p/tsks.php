<?php

require_once(__DIR__."/../src/Task.php");
require_once(__DIR__."/../src/IO.php");

use Lechimp\Tsks\Task;
use Lechimp\Tsks\IO;

class HelloTask extends Task {
    public function run(IO $io) {
        yield $io->putLine("Hello! What is your name?");
        yield $io->getLine();
        $name = $io->last_result;
        $greeting = "Hello $name!";
        yield $io->putLine($greeting);
    }
}
