<?php

namespace Lechimp\Tsks\Runtime;

use Lechimp\Tsks\IO;
use Lechimp\Tsks\Task;

class Console implements IO {
    use IO\Runtime;

    public $last_result = null;

    public function run($cmd_or_task) {
        if ($cmd_or_task instanceof IO\Command) {
            return $this->run_command($cmd_or_task);
        }
        if ($cmd_or_task instanceof Task) {
            return $this->run_task($cmd_or_task);
        }
        throw new \InvalidArgumentException(
                    "Can't handle: '".get_class($cmd_or_task)."'");
    }

    protected function run_command(IO\Command $cmd) {
        switch ($cmd->name()) {
            case "PutLine":
                echo $cmd->line()."\n";
                return;
            case "GetLine":
                return readline("> ");
            default:
                throw new \LogicException("Unknown command: {$cmd->name()}");
        }
    }

    protected function run_task(Task $task) {
        $gen = $task->run($this);
        while($gen->valid()) {
            $cmd_or_task = $gen->current();
            $res = $this->run($cmd_or_task);
            $gen->send($res);
        }
        return $gen->getReturn();
    }
}
