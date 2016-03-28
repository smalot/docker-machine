<?php

namespace Smalot\Docker\Machine\Driver;

/**
 * Class Generic
 * @package Smalot\Docker\Machine\Driver
 */
class Generic extends BaseDriver
{
    /**
     * Generic constructor.
     */
    public function __construct()
    {
        parent::__construct('generic');
    }
}
