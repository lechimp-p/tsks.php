<?php

require_once(__DIR__."/../vendor/autoload.php");

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

/*class MyTask extends Task {
    public function build(IO $io) {
        return $io
            ->putLine("Hello! What is your name?")
            ->getLine()->bind(function($name) use ($io) {
                $greeting = "Hello World";
                return $io->putLine($greeting); 
            });
    }
}*/
