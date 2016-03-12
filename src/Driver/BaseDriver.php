<?php

namespace Smalot\Docker\Machine\Driver;

use Symfony\Component\Yaml\Parser;

/**
 * Class BaseDriver
 * @package Smalot\Docker\Machine\Driver
 */
abstract class BaseDriver
{
    /**
     * @var string
     */
    protected $code;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * BaseDriver constructor.
     * @param string $code
     */
    public function __construct($code)
    {
        $this->code = $code;
    }

    /**
     *
     */
    public function loadConfig()
    {
        $filename = __DIR__ . '/../definitions/' . $this->getCode() . '.yml';

        if (file_exists($filename)) {
            $content = file_get_contents($filename);

            $parser = new Parser();
            $config = $parser->parse($content);

            $config += [
                'name' => $this->getCode(),
                'description' => '',
                'options' => [],
            ];

            $this->name = $config['name'];
            $this->description = $config['description'];
            $this->options = $config['options'];
        }
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @return array
     */
    public function buildArguments($values)
    {
        $arguments = [];

        foreach ($values as $code => $value) {
            if (isset($this->options[$code])) {
                if (is_bool($value)) {
                    $value = ($value ? 'true' : 'false');
                }

                if (!is_null($value)) {
                    $value = escapeshellarg($value);
                }

                $arguments[] = '--' . $this->options[$code]['argument'] . '=' . $value;
            }
        }

        return $arguments;
    }
}