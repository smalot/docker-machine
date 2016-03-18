<?php

namespace Smalot\Docker\Machine;

use AdamBrett\ShellWrapper\Runners\Exec;

/**
 * Class Machine
 * @package Smalot\Docker\Machine
 */
class Machine
{
    const STATUS_RUNNING = 'Running';
    const STATUS_PAUSED = 'Paused';
    const STATUS_SAVED = 'Saved';
    const STATUS_STOPPED = 'Stopped';
    const STATUS_STARTING = 'Starting';
    const STATUS_ERROR = 'Error';

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

    public function getName()
    {
        return $this->name;
    }

    public function getStatus()
    {

    }

    public function start()
    {
        $command = new Command('start');
        $command->addParam($this->getName());

        $runner = new Exec();
        $runner->run($command);

        return ($runner->getReturnValue() === 0);
    }

    public function stop()
    {
        $command = new Command('stop');
        $command->addParam($this->getName());

        $runner = new Exec();
        $runner->run($command);

        return ($runner->getReturnValue() === 0);
    }

    public function restart()
    {
        $command = new Command('restart');
        $command->addParam($this->getName());

        $runner = new Exec();
        $runner->run($command);

        return ($runner->getReturnValue() === 0);
    }

    public function kill()
    {
        $command = new Command('kill');
        $command->addParam($this->getName());

        $runner = new Exec();
        $runner->run($command);

        return ($runner->getReturnValue() === 0);
    }

    public function rm()
    {
        $command = new Command('rm');
        $command->addParam($this->getName());

        $runner = new Exec();
        $runner->run($command);

        return ($runner->getReturnValue() === 0);
    }

    public function ssh($sshCommand)
    {
        $command = new Command('rm');
        $command->addParam($this->getName());

        $runner = new Exec();
        $runner->run($command);

        return ($runner->getReturnValue() === 0);
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
