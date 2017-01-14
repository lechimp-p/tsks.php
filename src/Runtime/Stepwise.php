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
        $step = 0;
        list($skip, $results) = $this->get_state();

        $gen = $task->run($this);
        while($gen->valid()) {
            $step++;
            if ($step <= $skip) {
                $gen->send($results[$step-1]);
                continue;
            }

            $cmd = $gen->current();
            $res = $this->io->run($cmd);
            $this->update_state($step, $res);
            return;
        }
        unlink($this->file);
        echo "!! Task done. Removing {$this->file}.\n";
    }

    protected function get_state() {
        if ($this->state_exists()) {
            return $this->read_state();
        }
        $this->init_state();
        return [0, []];
    }

    protected function state_exists() {
        return file_exists($this->file);
    }

    protected function read_state() {
        require_once($this->file);
        global $tsks_skip, $tsks_results;
        return [$tsks_skip, $tsks_results];
    }

    protected function init_state() {
        file_put_contents($this->file,
            "<?php\n".
            "// INIT\n".
            "global \$tsks_skip, \$tsks_results;\n".
            "\$tsks_skip = 0;\n".
            "\$tsks_results = [];\n");
    }

    protected function update_state($step, $res) {
        file_put_contents($this->file, "// STEP $step\n", FILE_APPEND);
        file_put_contents($this->file, "\$tsks_skip = $step;\n", FILE_APPEND);
        $res = serialize($res);
        file_put_contents
            ( $this->file
            , "\$tsks_results[] = unserialize('$res');\n"
            , FILE_APPEND
            );
    }
}
