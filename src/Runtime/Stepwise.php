<?php

namespace Lechimp\Tsks\Runtime;

use Lechimp\Tsks\IO;
use Lechimp\Tsks\Task;

class Stepwise implements IO {
    use IO\Runtime;

    protected $io;
    protected $file;
    public $last_result = null;

    public function __construct($io, $file) {
        $this->file = $file;
        $this->io = $io;
    }

    public function run($cmd_or_task) {
        if ($cmd_or_task instanceof IO\Command) {
            return $this->io->run_command($cmd_or_task);
        }
        if ($cmd_or_task instanceof Task) {
            return $this->run_task($cmd_or_task);
        }
        throw new \InvalidArgumentException(
                    "Can't handle: '".get_class($cmd_or_task)."'");
    }

    protected function run_task(Task $task) {
        list($skip, $results, $on_level) = $this->get_state();
        $this->run_task_level($task, 0, $skip, $results, $on_level);
    }

    protected function run_task_level(Task $task, $level, $skip, $results, $on_level) {
        $step = 0;
        $gen = $task->run($this);
        while($gen->valid()) {
            $step++;

            // Skip the steps at the current level that were
            // already completed.
            if ($step <= $skip[$level]) {
                $gen->send($results[$level][$step-1]);
                continue;
            }

            // This is the thing we are currently working on.
            $cmd_or_task = $gen->current();

            // Go down one level if we didn't reach the level
            // we are currently working in.
            if ($level < $on_level) {
                assert('$cmd instanceof Lechimp\Tsks\Task');
                return $this->run_task_level
                                ( $cmd_or_task
                                , $level + 1
                                , $skip
                                , $results
                                , $on_level
                                );
            }

            // Easy case: we are working on a plain command.
            // Just perform it.
            if ($cmd_or_task instanceof IO\Command) {
                $res = $this->io->run($cmd_or_task);
                $this->update_state($level, $step, $res);
                return $res;
            }

            // More involved case: we are workin on a subtask.
            // We need to dive down into it.
            $skip[] = 0;
            $on_level++;
            echo "!! Working on subtask.\n";
            $this->descend_state($on_level);
            return $this->run_task_level
                            ( $cmd_or_task
                            , $level + 1
                            , $skip
                            , $results
                            , $on_level
                            );
        }

        if ($level == 0) {
            unlink($this->file);
            echo "!! Task done. Removing {$this->file}.\n";
            return;
        }

        $res = $gen->getReturn();
        $on_level--;
        $this->ascend_state($on_level, $skip[$on_level] + 1, $res);
        echo "!! Subtask done.\n";
        return $res;
    }

    protected function get_state() {
        if ($this->state_exists()) {
            return $this->read_state();
        }
        $this->init_state();
        return [[0], [], 0];
    }

    protected function state_exists() {
        return file_exists($this->file);
    }

    protected function read_state() {
        require_once($this->file);
        global $tsks_skip, $tsks_results, $tsks_level;
        return [$tsks_skip, $tsks_results, $tsks_level];
    }

    protected function init_state() {
        file_put_contents($this->file,
            "<?php\n".
            "// INIT\n".
            "global \$tsks_skip, \$tsks_results, \$tsks_level;\n".
            "\$tsks_level = 0;\n".
            "\$tsks_skip = [0];\n".
            "\$tsks_results = [[]];\n");
    }

    protected function update_state($level, $step, $res) {
        $res = serialize($res);
        file_put_contents
            ( $this->file
            , "// STEP $level, $step\n"
            . "\$tsks_skip[$level] = $step;\n"
            . "\$tsks_results[$level][] = unserialize('$res');\n"
            , FILE_APPEND
            );
    }

    protected function ascend_state($level, $step, $res) {
        $res = serialize($res);
        file_put_contents
            ( $this->file
            , "// ASCEND $level, $step\n"
            . "\$tsks_level = $level;\n"
            . "array_pop(\$tsks_skip);\n"
            . "\$tsks_skip[$level] = $step;\n"
            . "\$tsks_results[$level][] = unserialize('$res');\n"
            , FILE_APPEND
            );
    }

    protected function descend_state($level) {
        file_put_contents
            ( $this->file
            , "// DESCEND $level\n"
            . "\$tsks_level = $level;\n"
            . "\$tsks_skip[] = 0;\n"
            . "\$tsks_results[] = [];\n"
            , FILE_APPEND
            );
    }
}
