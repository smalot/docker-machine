<?php

namespace Smalot\Docker\Machine;

use AdamBrett\ShellWrapper\Command\Builder;
use AdamBrett\ShellWrapper\Command\SubCommand;

/**
 * Class Command
 * @package Smalot\Docker\Machine
 */
class Command extends Builder
{
    protected static $mainCommand = 'docker-machine';

    /**
     * @var array
     */
    protected $envVars = [];

    /**
     * Command constructor.
     * @param $subCommand
     */
    public function __construct($subCommand)
    {
        parent::__construct(self::$mainCommand);

        $this->addSubCommand($subCommand);
    }

    /**
     * @param string|SubCommand $subCommand
     * @return $this
     */
    public function addSubCommand($subCommand)
    {
        if (!$subCommand instanceof SubCommand) {
            $this->command->addSubCommand(new SubCommand($subCommand));
        } else {
            parent::addSubCommand($subCommand);
        }

        return $this;
    }

    /**
     * @param array $arguments
     * @return $this
     */
    public function addArguments($arguments)
    {
        foreach ($arguments as $name => $value) {
            $this->addArgument($name, $value);
        }

        return $this;
    }

    /**
     * @param $name
     * @param $value
     * @return $this
     */
    public function addEnvVar($name, $value)
    {
        $this->envVars[$name] = $value;

        return $this;
    }

    /**
     * @param array $vars
     * @return $this
     */
    public function setEnvVars($vars)
    {
        $this->envVars = [];

        foreach ($vars as $name => $value) {
            $this->addEnvVar($name, $value);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $command = '';

        foreach ($this->envVars as $name => $value) {
            // Secure environment name.
            if (!preg_match('/[a-zA-Z_]+[a-zA-Z0-9_]*/', $name)) {
                throw new \InvalidArgumentException('Invalid environment name.');
            }

            if ($value = (string) $value) {
                $command .= $name . '=' . escapeshellarg($value) . ' ';
            } else {
                $command .= $name . '=""';
            }
        }

        $command .= (string) $this->command;

        return $command;
    }

    /**
     * @param string $mainCommand
     */
    public static function setMainCommand($mainCommand)
    {
        self::$mainCommand = $mainCommand;
    }
}
