<?php

namespace Lechimp\Tsks\IO;

trait Runtime {
    /**
     * Put a line to the output.
     *
     * @param   string  $line
     * @return  IO\Command
     */
    public function put_line($line) {
        return new PutLine($line);
    }

    /**
     * Get a line from the output.
     *
     * @return  IO\Command
     */
    public function get_line() {
        return new GetLine();
    }
}
