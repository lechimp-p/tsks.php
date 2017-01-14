<?php

namespace Lechimp\Tsks\IO;

class PutLine extends Command {
    /**
     * @var string
     */
    protected $line;

    public function __construct($line) {
        assert('is_string($line)');
        $this->line = $line;
    }

    /**
     * @return  string
     */
    public function line() {
        return $this->line;
    }
}

