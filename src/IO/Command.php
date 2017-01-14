<?php

namespace Lechimp\Tsks\IO;

class Command {
    public function name() {
        $n = explode("\\", get_class($this));
        return array_pop($n);
    }
}
