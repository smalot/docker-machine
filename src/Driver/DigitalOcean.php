<?php

namespace Smalot\Docker\Machine\Driver;

/**
 * Class DigitalOcean
 * @package Smalot\Docker\Machine\Driver
 */
class DigitalOcean extends BaseDriver
{
    /**
     * Generic constructor.
     */
    public function __construct()
    {
        parent::__construct('digitalocean');
    }
}
