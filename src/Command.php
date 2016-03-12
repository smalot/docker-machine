<?php

namespace Smalot\Docker\Machine;

use AdamBrett\ShellWrapper\Command\Builder;

/**
 * Class Command
 * @package Smalot\Docker\Machine
 */
class Command extends Builder
{
    /**
     * Command constructor.
     * @param string $command
     */
    public function __construct($command)
    {
        parent::__construct($command);
    }

    /**
     * @param array $arguments
     */
    public function addArguments($arguments)
    {
        foreach ($arguments as $name => $value) {
            $this->addArgument($name, $value);
        }
    }
}
