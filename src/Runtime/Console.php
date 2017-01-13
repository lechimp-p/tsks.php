<?php

namespace Lechimp\Tsks\Runtime;

use Lechimp\Tsks\IO;
use Lechimp\Tsks\Task;

class Console implements IO {
    public $last_result = null;

    public function run(Task $task) {
        foreach($task->run($this) as $action) {
            switch ($action[0]) {
                case "putLine":
                    echo $action[1]."\n"; 
                    break;
                case "getLine":
                    $this->last_result = readline("> "); 
                    break;
                default:
                    throw new \LogicException("Unknown action: {$action[0]}");
            }
        }
    }
    public function putLine($line) {
        return ["putLine", $line]; 
    }
    public function getLine() {
        return ["getLine"];
    }
}
