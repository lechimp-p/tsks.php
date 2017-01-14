<?php

require_once(__DIR__."/GetNameTask.php");
require_once(__DIR__."/GreetingTask.php");

use Lechimp\Tsks\Task;
use Lechimp\Tsks\IO;

class HelloTask extends Task {
    public function run(IO $io) {
        $name = yield (new GetNameTask());
        yield (new GreetingTask($name));
    }
}
