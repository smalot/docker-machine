<?php

namespace Smalot\Docker\Machine;


use AdamBrett\ShellWrapper\Runners\ShellExec;

class Manager
{
    /**
     * @var string
     */
    protected $command;

    /**
     * Manager constructor.
     * @param string $command
     */
    public function __construct($command)
    {
        $this->command = $command;
    }

    /**
     * @return array
     */
    public function getList()
    {
        $placeholders = [
          'name' => '.Name',
          'active' => '.Active',
          'active_host' => '.ActiveHost',
          'active_swarm' => '.ActiveSwarm',
          'driver_name' => '.DriverName',
          'state' => '.State',
          'url' => '.URL',
          'swarm' => '.Swarm',
          'docker_version' => '.DockerVersion',
          'response_time' => '.ResponseTime',
          'error' => '.Error',
        ];

        $command = new Command($this->command);
        $command->addSubCommand('ls');
        $command->addArgument('format', '{{'.implode('}},{{', $placeholders).'}}');

        $runner = new ShellExec();
        $response = $runner->run($command);
        $lines = preg_split('/[\n\r]+/', $response);
        $list = [];

        foreach (array_filter($lines) as $line) {
            $values = explode(',', $line, count($placeholders));
            $values = array_combine(array_keys($placeholders), $values);
            $name = array_shift($values);
            $list[$name] = $values;
        }

        return $list;
    }
}
