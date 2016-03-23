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

        $runner = new ProcOpen();
        $runner->addEnvironmentVariables($this->env);
        $runner->run($command);

        if ($runner->getReturnCode() <> 0) {
            return false;
        }

        return trim($runner->getOutput());
    }

    /**
     * @param string $machineName
     * @param array $arguments
     * @return string
     * @throws \Smalot\Docker\Machine\HostNotFoundException
     */
    public function config($machineName, $arguments = [])
    {
        $command = new Command('config');
        $command->addArguments($arguments);
        $command->addParam($machineName);

        $runner = new ProcOpen();
        $runner->run($command);
        $output = $runner->getOutput();

        if (preg_match('/Host not found/', $output)) {
            throw new HostNotFoundException($machineName, $runner->getReturnCode());
        }

        return $output;
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

        if (!is_null($driver)) {
            $arguments['driver'] = $driver->getCode();
            $command->addArguments($driver->getArguments());
        }

        $command->addArguments($arguments);
        $command->addParam($machineName);

//        var_dump($_POST);
//        echo((string) $command);die;

        $runner = new ProcOpen();
        $runner->run($command);

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

        $runner = new ProcOpen();
        $runner->run($command);
        $output = $runner->getOutput();

        if (preg_match('/Host not found/', $output)) {
            throw new HostNotFoundException($machineName, -1);
        }

        // Todo: generate an array with envs.

        return array();//$output;
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

        $runner = new ProcOpen();
        $runner->run($command);
        $output = $runner->getOutput();

        if (preg_match('/Host not found/', $output)) {
            throw new HostNotFoundException($machineName, -1);
        }

        if ($json = json_decode($output, true)) {
            return $json;
        }

        throw new \Exception('Invalid response.', -1);
    }

    
    public function ip($machineName)
    {
        $command = new Command('ip');
        $command->addParam($machineName);

        $runner = new ProcOpen();
        $output = $runner->run($command);

        if (preg_match('/Host not found/', $output)) {
            throw new HostNotFoundException($machineName, -1);
        }

        return $output;
    }

    public function kill($machineName)
    {
        $command = new Command('kill');
        $command->addParam($machineName);

        $runner = new ProcOpen();
        $result = $runner->run($command);

        if (preg_match('/Host not found/', $result)) {
            throw new HostNotFoundException($machineName, -1);
        }

        if ($error = $runner->getReturnCode()) {
            throw new \Exception($result, $error);
        }

        return true;
    }

    /**
     * @return array
     */
    public function machines()
    {
        $command = new Command('ls');
        $command->addFlag('q');

        $runner = new ProcOpen();
        $runner->run($command);

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

        $runner = new ProcOpen();
        $runner->run($command);

        $output = $runner->run($command);
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

    public function provision($machineName)
    {
        $command = new Command('provision');
        $command->addParam($machineName);

        $runner = new ProcOpen();
        $result = $runner->run($command);

        if (preg_match('/Host not found/', $result)) {
            throw new HostNotFoundException($machineName, -1);
        }

        if ($error = $runner->getReturnCode()) {
            throw new \Exception($result, $error);
        }

        return true;
    }

    public function regenerateCerts($machineName)
    {
        $command = new Command('regenerate-certs');
        $command->addParam($machineName);
        // Force generation.
        $command->addFlag('f');

        $runner = new ProcOpen();
        $result = $runner->run($command);

        if (preg_match('/Host not found/', $result)) {
            throw new HostNotFoundException($machineName, -1);
        }

        if ($error = $runner->getReturnCode()) {
            throw new \Exception($result, $error);
        }

        return true;
    }

    public function restart($machineName)
    {
        $command = new Command('restart');
        $command->addParam($machineName);

        $runner = new ProcOpen();
        $result = $runner->run($command);

        if (preg_match('/Host not found/', $result)) {
            throw new HostNotFoundException($machineName, -1);
        }

        if ($error = $runner->getReturnCode()) {
            throw new \Exception($result, $error);
        }

        return true;
    }

    public function rm($machineName, $force = false)
    {
        $command = new Command('rm');
        $command->addFlag('y');
        if ($force) {
            $command->addFlag('f');
        }
        $command->addParam($machineName);

        $runner = new ProcOpen();
        $result = $runner->run($command);

        if (preg_match('/Host not found/', $result)) {
            throw new HostNotFoundException($machineName, -1);
        }

        if ($error = $runner->getReturnCode()) {
            throw new \Exception($result, $error);
        }

        return true;
    }

    public function scp($machineName, $source, $destination, $recursive = false)
    {
        $command = new Command('scp');
        $command->addParam($machineName);
        if ($recursive) {
            $command->addFlag('r');
        }
        $command->addParam($source);
        $command->addParam($destination);

        $runner = new ProcOpen();
        $result = $runner->run($command);

        if (preg_match('/Host not found/', $result)) {
            throw new HostNotFoundException($machineName, -1);
        }

        if ($error = $runner->getReturnCode()) {
            throw new \Exception($result, $error);
        }

        return true;
    }

    public function ssh($machineName, $commandLine)
    {
        $command = new Command('ssh');
        $command->addParam($machineName);
        $command->addArgument($commandLine);

        $runner = new ProcOpen();
        $result = $runner->run($command);

        if (preg_match('/^(Host not found)/', $result)) {
            throw new HostNotFoundException($machineName, -1);
        }

        return $output;
    }

    public function start($machineName)
    {
        $command = new Command('start');
        $command->addParam($machineName);

        $runner = new ProcOpen();
        $result = $runner->run($command);

        if (preg_match('/Host not found/', $result)) {
            throw new HostNotFoundException($machineName, -1);
        }

        if ($error = $runner->getReturnCode()) {
            throw new \Exception($result, $error);
        }

        return true;
    }

    public function status($machineName)
    {
        $command = new Command('status');
        $command->addParam($machineName);

        $runner = new ProcOpen();
        $result = $runner->run($command);

        if (preg_match('/Host not found/', $result)) {
            throw new HostNotFoundException($machineName, -1);
        }

        if ($error = $runner->getReturnCode()) {
            throw new \Exception($result, $error);
        }

        return true;
    }

    public function stop($machineName)
    {
        $command = new Command('stop');
        $command->addParam($machineName);

        $runner = new ProcOpen();
        $result = $runner->run($command);

        if (preg_match('/Host not found/', $result)) {
            throw new HostNotFoundException($machineName, -1);
        }

        if ($error = $runner->getReturnCode()) {
            throw new \Exception($result, $error);
        }

        return true;
    }

    public function upgrade($machineName)
    {
        $command = new Command('upgrade');
        $command->addParam($machineName);

        $runner = new ProcOpen();
        $result = $runner->run($command);

        if (preg_match('/Host not found/', $result)) {
            throw new HostNotFoundException($machineName, -1);
        }

        if ($error = $runner->getReturnCode()) {
            throw new \Exception($result, $error);
        }

        return true;
    }

    public function url($machineName)
    {
        $command = new Command('url');
        $command->addParam($machineName);

        $runner = new ProcOpen();
        $result = $runner->run($command);

        if (preg_match('/Host not found/', $result)) {
            throw new HostNotFoundException($machineName, -1);
        }

        if ($error = $runner->getReturnCode()) {
            throw new \Exception($result, $error);
        }

        return $output;
    }
}
