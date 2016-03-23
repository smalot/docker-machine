<?php

namespace Smalot\Docker\Machine;

use Smalot\Commander\Command as BaseCommand;

/**
 * Class Command
 * @package Smalot\Docker\Machine
 */
class Command extends BaseCommand
{
    /**
     * @var string
     */
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
     * @param string $mainCommand
     */
    public static function setMainCommand($mainCommand)
    {
        self::$mainCommand = $mainCommand;
    }
}
