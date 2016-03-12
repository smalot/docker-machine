<?php

namespace Smalot\Docker\Machine;

/**
 * Class Machine
 * @package Smalot\Docker\Machine
 */
class Machine
{
    /**
     * @var array
     */
    static protected $stack = [];

    /**
     * @var string
     */
    protected $name;

    /**
     * Machine constructor.
     * @param $name
     */
    protected function __construct($name)
    {
        $this->name = $name;
    }

    public static function createInstance($name)
    {

    }

    /**
     * @param $name
     * @return \Smalot\Docker\Machine\Machine
     */
    public static function getInstance($name)
    {
        if (!isset(self::$stack[$name])) {
            self::$stack[$name] = new self($name);
        }

        return self::$stack[$name];
    }
}
