<?php

namespace Smalot\Docker\Machine;

use Smalot\Commander\Runner\ProcOpen;
use Smalot\Docker\Machine\Driver\BaseDriver;

class Manager
{
    /**
     * @var array
     */
    protected $env;

    /**
     * Manager constructor.
     * @param array $env
     */
    public function __construct($env = [])
    {
        $this->env = $env;
    }

    /**
     * @return string|false
     */
    public function active()
    {
        $command = new Command('active');

        $runner = $this->executeCommand($command);

        return trim($runner->getOutput());
    }

    /**
     * @param string $machineName
     * @param array $arguments
     * @return string
     * @throws \Smalot\Docker\Machine\HostNotFoundException
     * @throws \RuntimeException
     */
    public function config($machineName, $arguments = [])
    {
        $command = new Command('config');
        $command->addArguments($arguments);
        $command->addParam($machineName);

        $runner = $this->executeCommand($command);

        return $runner->getOutput();
    }

    /**
     * @param string $machineName
     * @param \Smalot\Docker\Machine\Driver\BaseDriver|null $driver
     * @param array $arguments
     * @return string
     */
    public function create($machineName, BaseDriver $driver = null, $arguments = [])
    {
        $command = new Command('create');
        $command->addArguments($arguments);
        $command->addParam($machineName);

        if (!is_null($driver)) {
            $command->addArgument('driver', $driver->getCode());
            $command->addArguments($driver->getArguments());
        }

        $runner = $this->executeCommand($command, 120);

        return $runner->getOutput();
    }

    /**
     * @param string $machineName
     * @param array $arguments
     * @return array
     * @throws \Smalot\Docker\Machine\HostNotFoundException
     */
    public function env($machineName, $arguments = [])
    {
        $command = new Command('env');
        $command->addArguments($arguments);
        $command->addParam($machineName);

        $runner = $this->executeCommand($command);

        // Todo: generate an array with envs.

        return array();// $runner->getOutput();
    }

    /**
     * @param $machineName
     * @param array $arguments
     * @return mixed
     * @throws \Exception
     * @throws \Smalot\Docker\Machine\HostNotFoundException
     */
    public function inspect($machineName, $arguments = [])
    {
        $command = new Command('inspect');
        $command->addArguments($arguments);
        $command->addParam($machineName);

        $runner = $this->executeCommand($command);

        if ($json = json_decode($runner->getOutput(), true)) {
            return $json;
        }

        throw new \RuntimeException('Invalid response.', -1);
    }

    /**
     * @param string $machineName
     * @return string
     * @throws \Smalot\Docker\Machine\HostNotFoundException
     */
    public function ip($machineName)
    {
        $command = new Command('ip');
        $command->addParam($machineName);

        $runner = $this->executeCommand($command);

        return $runner->getOutput();
    }

    /**
     * @param string $machineName
     * @return bool
     * @throws \Exception
     * @throws \Smalot\Docker\Machine\HostNotFoundException
     */
    public function kill($machineName)
    {
        $command = new Command('kill');
        $command->addParam($machineName);

        $runner = $this->executeCommand($command);

        return $runner->getOutput();
    }

    /**
     * @return array
     */
    public function machines()
    {
        $command = new Command('ls');
        $command->addFlag('q');

        $runner = $this->executeCommand($command);

        $list = preg_split('/[\n\r]+/', $runner->getOutput());

        return array_filter($list);
    }

    /**
     * @param array $arguments
     * @return array
     */
    public function ls($arguments = [])
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

        $arguments['format'] = '{{'.implode('}},{{', $placeholders).'}}';

        $command = new Command('ls');
        $command->addArguments($arguments);

        $runner = $this->executeCommand($command);
        $output = $runner->getOutput();

        $lines = preg_split('/[\n\r]+/', $output);
        $list = [];

        foreach (array_filter($lines) as $line) {
            $values = explode(',', $line, count($placeholders));
            $name = $values[0];
            $values = array_combine(array_keys($placeholders), $values);
            $list[$name] = $values;
        }

        return $list;
    }

  /**
   * @param string $machineName
   * @return string
   */
    public function provision($machineName)
    {
        $command = new Command('provision');
        $command->addParam($machineName);

        $runner = $this->executeCommand($command);

        return $runner->getOutput();
    }

  /**
   * @param string $machineName
   * @return string
   */
    public function regenerateCerts($machineName)
    {
        $command = new Command('regenerate-certs');
        $command->addParam($machineName);
        // Force generation.
        $command->addFlag('f');

        $runner = $this->executeCommand($this->env);

        return $runner->getOutput();
    }

  /**
   * @param string $machineName
   * @return string
   */
    public function restart($machineName)
    {
        $command = new Command('restart');
        $command->addParam($machineName);

        $runner = $this->executeCommand($command);

        return $runner->getOutput();
    }

  /**
   * @param string $machineName
   * @param bool $force
   * @return string
   */
    public function rm($machineName, $force = false)
    {
        $command = new Command('rm');
        $command->addFlag('y');
        if ($force) {
            $command->addFlag('f');
        }
        $command->addParam($machineName);

        $runner = $this->executeCommand($command);

        return $runner->getOutput();
    }

  /**
   * @param string $machineName
   * @param string $source
   * @param string $destination
   * @param bool $recursive
   * @return string
   */
    public function scp($machineName, $source, $destination, $recursive = false)
    {
        $command = new Command('scp');
        $command->addParam($machineName);
        if ($recursive) {
            $command->addFlag('r');
        }
        $command->addParam($source);
        $command->addParam($destination);

        $runner = $this->executeCommand($command);

        return $runner->getOutput();
    }

  /**
   * @param string $machineName
   * @param Command|string $commandLine
   * @return string
   */
    public function ssh($machineName, $commandLine)
    {
        $command = new Command('ssh');
        $command->addParam($machineName);
        $command->addArgument($commandLine);

        $runner = $this->executeCommand($command);

        return $runner->getOutput();
    }

  /**
   * @param string $machineName
   * @return string
   */
    public function start($machineName)
    {
        $command = new Command('start');
        $command->addParam($machineName);

        $runner = $this->executeCommand($command);

        return $runner->getOutput();
    }

  /**
   * @param string $machineName
   * @return string
   */
    public function status($machineName)
    {
        $command = new Command('status');
        $command->addParam($machineName);

        $runner = $this->executeCommand($command);

        return $runner->getOutput();
    }

  /**
   * @param string $machineName
   * @return string
   */
    public function stop($machineName)
    {
        $command = new Command('stop');
        $command->addParam($machineName);

        $runner = $this->executeCommand($command);

        return $runner->getOutput();
    }

  /**
   * @param string $machineName
   * @return string
   */
    public function upgrade($machineName)
    {
        $command = new Command('upgrade');
        $command->addParam($machineName);

        $runner = $this->executeCommand($command);

        return $runner->getOutput();
    }

    /**
     * @param string $machineName
     * @return string
     */
    public function url($machineName)
    {
        $command = new Command('url');
        $command->addParam($machineName);

        $runner = $this->executeCommand($command);

        return $runner->getOutput();
    }

    /**
     * @param \Smalot\Docker\Machine\Command $command
     * @param int $timeout
     * @return \Smalot\Commander\Runner\ProcOpen
     */
    protected function executeCommand(Command $command, $timeout = 15)
    {
        $runner = new ProcOpen();
        $runner->addEnvironmentVariables($this->env, $timeout);
        $runner->run($command);

        return $runner;
    }
}
