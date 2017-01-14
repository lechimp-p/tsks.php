<?php

use Lechimp\Tsks\Task;
use Lechimp\Tsks\IO;

class HelloTask extends Task {
    public function run(IO $io) {
        yield $io->put_line("Hello! What is your name?");
        yield $io->get_line();
        $name = $io->last_result;
        $greeting = "Hello $name!";
        yield $io->put_line($greeting);
    }
}
