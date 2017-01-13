<?php

namespace Lechimp\Tsks\Runtime;

use Lechimp\Tsks\IO;
use Lechimp\Tsks\Task;

class Stepwise implements IO {
    protected $io;
    protected $file;
    public $last_result = null;

    public function __construct($io, $file) {
        $this->file = $file;
        $this->io = $io;
    }

    public function run(Task $task) {
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
        foreach($task->run($this) as $action) {
            $step++;
            if ($step <= $skip) {
                switch ($action[0]) {
                    case "putLine":
                        break;
                    case "getLine":
                        $this->last_result = $results[$step-1];
                        break;
                    default:
                        throw new \LogicException("Unknown action: {$action[0]}");
                }
            }
            else {
                file_put_contents($this->file, "// STEP $step\n", FILE_APPEND);
                file_put_contents($this->file, "\$tsks_skip = $step;\n", FILE_APPEND);
                switch ($action[0]) {
                    case "putLine":
                        $this->io->run(new PutLineTask($action[1]));
                        $val = serialize($action[1]);
                        file_put_contents($this->file, "\$tsks_results[] = null;\n", FILE_APPEND);
                        break;
                    case "getLine":
                        $this->io->run(new GetLineTask($action[1]));
                        $val = serialize($this->io->last_result);
                        file_put_contents($this->file, "\$tsks_results[] = unserialize('$val');\n", FILE_APPEND);
                        break;
                    default:
                        throw new \LogicException("Unknown action: {$action[0]}");
                }
                return;
            }
        }
        unlink($this->file);
        echo "Task done. Remove {$this->file}.\n";
    }

    public function putLine($line) {
        return ["putLine", $line]; 
    }
    public function getLine() {
        return ["getLine"];
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
