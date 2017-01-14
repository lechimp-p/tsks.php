<?php

use Lechimp\Tsks\Task;
use Lechimp\Tsks\IO;

class GetNameTask extends Task {
    public function run(IO $io) {
        yield $io->put_line("Hello! What is your name?");
        $name = yield $io->get_line();
        return $name;
    }
}
