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
                $this->io->run($cmd);
                file_put_contents($this->file, "\$tsks_results[] = null;\n", FILE_APPEND);
                break;
            case "GetLine":
                $this->io->run($cmd);
                $val = serialize($this->io->last_result);
                file_put_contents($this->file, "\$tsks_results[] = unserialize('$val');\n", FILE_APPEND);
                break;
            default:
                throw new \LogicException("Unknown action: {$cmd->name()}");
        }
    }

    protected function run_task(Task $task) {
        $step = 0;
        $skip = 0;
        $results = [];
        if (file_exists($this->file)) {
            require_once($this->file);
            global $tsks_skip, $tsks_results;
            $skip = $tsks_skip;
            $results = $tsks_results;
        }
        else {
            file_put_contents($this->file, 
                "<?php\n".
                "// INIT\n".
                "global \$tsks_skip, \$tsks_results;\n".
                "\$tsks_skip = 0;\n".
                "\$tsks_results = [];\n");
        }
        foreach($task->run($this) as $cmd) {
            $step++;
            if ($step <= $skip) {
                if ($cmd->name() == "GetLine") {
                   $this->last_result = $results[$step-1];
                }
                continue;
            }

            file_put_contents($this->file, "// STEP $step\n", FILE_APPEND);
            file_put_contents($this->file, "\$tsks_skip = $step;\n", FILE_APPEND);
            $this->run_command($cmd);
            return;
        }
        unlink($this->file);
        echo "!! Task done. Removing {$this->file}.\n";
    }
}

class PutLineTask extends Task {
    protected $line = null;
    public function __construct($line) {
        $this->line = $line;
    }
    public function run(IO $io) {
        yield $io->putLine($this->line);
    }
}

class GetLineTask extends Task {
    public function run(IO $io) {
        yield $io->getLine();
    }
}
