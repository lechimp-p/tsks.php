<?php

namespace Lechimp\Tsks;

/**
 * Text base input and output operations.
 */
interface IO {
    /**
     * Put a line to the output.
     *
     * @param   string  $line
     * @return  IO\Command
     */
    public function put_line($line);

    /**
     * Get a line from the output.
     *
     * @return  IO\Command
     */
    public function get_line();
}
