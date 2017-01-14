<?php

use Lechimp\Tsks\Task;
use Lechimp\Tsks\IO;

class GreetingTask extends Task {
    /**
     * @var string
     */
    protected $name;

    public function __construct($name) {
        $this->name = $name;
    }

    public function run(IO $io) {
        $greeting = "Hello {$this->name}!";
        yield $io->put_line($greeting);
    }
}
