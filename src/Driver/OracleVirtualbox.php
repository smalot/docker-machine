<?php

namespace Smalot\Docker\Machine\Driver;

/**
 * Class OracleVirtualbox
 * @package Smalot\Docker\Machine\Driver
 */
class OracleVirtualbox extends BaseDriver
{
    /**
     * OracleVirtualbox constructor.
     */
    public function __construct()
    {
        parent::__construct('oracle_virtualbox');
    }
}
