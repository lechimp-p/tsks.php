<?php

use Lechimp\Tsks\Task;
use Lechimp\Tsks\IO;

class HelloTask extends Task {
    public function run(IO $io) {
        yield $io->put_line("Hello! What is your name?");
        $name = yield $io->get_line();
        $greeting = "Hello $name!";
        yield $io->put_line($greeting);
    }
}
