<?php

namespace Smalot\Docker\Machine;


use AdamBrett\ShellWrapper\Runners\Exec;
use AdamBrett\ShellWrapper\Runners\Passthru;
use AdamBrett\ShellWrapper\Runners\ShellExec;
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
        $command = $this->buildCommand('active');

        $runner = new Exec();
        $runner->run($command);

        if ($runner->getReturnValue() > 0) {
            return false;
        }

        return trim($runner->getOutput());
    }

    public function config($machineName, $arguments = [])
    {
        $command = $this->buildCommand('config');
        $command->addArguments($arguments);
        $command->addParam($machineName);

        $runner = new ShellExec();
        $result = $runner->run($command);

        if (preg_match('/Host not found/', $result)) {
            throw new HostNotFoundException($machineName, -1);
        }

        return $result;
    }

    public function create($machineName, BaseDriver $driver = null, $arguments = [])
    {
        $command = $this->buildCommand('create');

        if (!is_null($driver)) {
            $arguments['driver'] = $driver->getCode();
            $command->addArguments($driver->getArguments());
        }

        $command->addArguments($arguments);
        $command->addParam($machineName);

        $runner = new ShellExec();
        $result = $runner->run($command);

        return $result;
    }

    public function env($machineName, $arguments = [])
    {
        $command = $this->buildCommand('env');
        $command->addArguments($arguments);
        $command->addParam($machineName);

        $runner = new ShellExec();
        $result = $runner->run($command);

        if (preg_match('/Host not found/', $result)) {
            throw new HostNotFoundException($machineName, -1);
        }

        return $result;
    }

    public function inspect($machineName, $arguments = [])
    {
        $command = $this->buildCommand('inspect');
        $command->addArguments($arguments);
        $command->addParam($machineName);

        $runner = new ShellExec();
        $result = $runner->run($command);

        if (preg_match('/Host not found/', $result)) {
            throw new HostNotFoundException($machineName, -1);
        }

        if ($json = json_decode($result, true)) {
            return $json;
        }

        throw new \Exception($result, -1);
    }

    public function ip($machineName)
    {
        $command = $this->buildCommand('ip');
        $command->addParam($machineName);

        $runner = new Exec();
        $result = $runner->run($command);

        if (preg_match('/Host not found/', $result)) {
            throw new HostNotFoundException($machineName, -1);
        }

        return $result;
    }

    public function kill($machineName)
    {
        $command = $this->buildCommand('kill');
        $command->addParam($machineName);

        $runner = new Exec();
        $result = $runner->run($command);

        if (preg_match('/Host not found/', $result)) {
            throw new HostNotFoundException($machineName, -1);
        }

        if ($error = $runner->getReturnValue()) {
            throw new \Exception($result, $error);
        }

        return true;
    }

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

        $command = $this->buildCommand('ls');
        $command->addArguments($arguments);

        $runner = new Passthru();
        ob_start();
        $runner->run($command);
        $response = ob_get_contents();
        ob_end_clean();
        $lines = preg_split('/[\n\r]+/', $response);
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
        $command = $this->buildCommand('provision');
        $command->addParam($machineName);

        $runner = new Exec();
        $result = $runner->run($command);

        if (preg_match('/Host not found/', $result)) {
            throw new HostNotFoundException($machineName, -1);
        }

        if ($error = $runner->getReturnValue()) {
            throw new \Exception($result, $error);
        }

        return true;
    }

    public function regenerateCerts($machineName)
    {
        $command = $this->buildCommand('regenerate-certs');
        $command->addParam($machineName);
        // Force generation.
        $command->addFlag('f');

        $runner = new Exec();
        $result = $runner->run($command);

        if (preg_match('/Host not found/', $result)) {
            throw new HostNotFoundException($machineName, -1);
        }

        if ($error = $runner->getReturnValue()) {
            throw new \Exception($result, $error);
        }

        return true;
    }

    public function restart($machineName)
    {
        $command = $this->buildCommand('restart');
        $command->addParam($machineName);

        $runner = new Exec();
        $result = $runner->run($command);

        if (preg_match('/Host not found/', $result)) {
            throw new HostNotFoundException($machineName, -1);
        }

        if ($error = $runner->getReturnValue()) {
            throw new \Exception($result, $error);
        }

        return true;
    }

    public function rm($machineName, $force = false)
    {
        $command = $this->buildCommand('rm');
        $command->addFlag('y');
        if ($force) {
            $command->addFlag('f');
        }
        $command->addParam($machineName);

        $runner = new Exec();
        $result = $runner->run($command);

        if (preg_match('/Host not found/', $result)) {
            throw new HostNotFoundException($machineName, -1);
        }

        if ($error = $runner->getReturnValue()) {
            throw new \Exception($result, $error);
        }

        return true;
    }

    public function scp($machineName, $source, $destination, $recursive = false)
    {
        $command = $this->buildCommand('scp');
        $command->addParam($machineName);
        if ($recursive) {
            $command->addFlag('r');
        }
        $command->addParam($source);
        $command->addParam($destination);

        $runner = new Exec();
        $result = $runner->run($command);

        if (preg_match('/Host not found/', $result)) {
            throw new HostNotFoundException($machineName, -1);
        }

        if ($error = $runner->getReturnValue()) {
            throw new \Exception($result, $error);
        }

        return true;
    }

    public function ssh($machineName, $commandLine)
    {
        $command = $this->buildCommand('ssh');
        $command->addParam($machineName);
        $command->addArgument($commandLine);

        $runner = new ShellExec();
        $result = $runner->run($command);

        if (preg_match('/^(Host not found)/', $result)) {
            throw new HostNotFoundException($machineName, -1);
        }

        return $result;
    }

    public function start($machineName)
    {
        $command = $this->buildCommand('start');
        $command->addParam($machineName);

        $runner = new Exec();
        $result = $runner->run($command);

        if (preg_match('/Host not found/', $result)) {
            throw new HostNotFoundException($machineName, -1);
        }

        if ($error = $runner->getReturnValue()) {
            throw new \Exception($result, $error);
        }

        return true;
    }

    public function status($machineName)
    {
        $command = $this->buildCommand('status');
        $command->addParam($machineName);

        $runner = new Exec();
        $result = $runner->run($command);

        if (preg_match('/Host not found/', $result)) {
            throw new HostNotFoundException($machineName, -1);
        }

        if ($error = $runner->getReturnValue()) {
            throw new \Exception($result, $error);
        }

        return true;
    }

    public function stop($machineName)
    {
        $command = $this->buildCommand('stop');
        $command->addParam($machineName);

        $runner = new Exec();
        $result = $runner->run($command);

        if (preg_match('/Host not found/', $result)) {
            throw new HostNotFoundException($machineName, -1);
        }

        if ($error = $runner->getReturnValue()) {
            throw new \Exception($result, $error);
        }

        return true;
    }

    public function upgrade($machineName)
    {
        $command = $this->buildCommand('upgrade');
        $command->addParam($machineName);

        $runner = new Exec();
        $result = $runner->run($command);

        if (preg_match('/Host not found/', $result)) {
            throw new HostNotFoundException($machineName, -1);
        }

        if ($error = $runner->getReturnValue()) {
            throw new \Exception($result, $error);
        }

        return true;
    }

    public function url($machineName)
    {
        $command = $this->buildCommand('url');
        $command->addParam($machineName);

        $runner = new Exec();
        $result = $runner->run($command);

        if (preg_match('/Host not found/', $result)) {
            throw new HostNotFoundException($machineName, -1);
        }

        if ($error = $runner->getReturnValue()) {
            throw new \Exception($result, $error);
        }

        return $result;
    }
    
    protected function buildCommand($subCommand)
    {
        $command = new Command($subCommand);
        $command->setEnvVars($this->env);
        
        return $command;
    }
}
