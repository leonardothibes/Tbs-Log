<?php
/**
 * @category Library
 * @package Tbs
 * @subpackage Log
 * @author Leonardo Thibes <eu@leonardothibes.com>
 * @copyright Copyright (c) The Authors
 */

namespace Tbs;

use \Tbs\Log\Interfaces\LoggerInterface;
use \Tbs\Log\Interfaces\LoggerAwareInterface;

/**
 * Logger frontend class.
 *
 * @category Library
 * @package Tbs
 * @subpackage Log
 * @author Leonardo Thibes <eu@leonardothibes.com>
 * @copyright Copyright (c) The Authors
 */
class Log implements LoggerAwareInterface
{
    /**
     * Singleton instance.
     * @var \Tbs\Log
     */
    public static $instance = null;

    /**
     * Logger frontend component.
     * @var LoggerInterface
     */
    protected $logger = null;

    /**
     * Gets a singleton instance of class.
     * @return \Tbs\Log
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Block non singleton instance by visibility.
     */
    protected function __construct()
    {
        //Do nothing...yet
    }

    /**
     * Reset the singleton instance.
     * @return \Tbs\Log
     */
    public function resetInstance()
    {
        $this->logger   = null;
        self::$instance = null;
        return $this;
    }

    /**
     * Set the log frontend component.
     *
     * @param  LoggerInterface $logger
     * @return \Tbs\Log
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
        return $this;
    }

    /**
     * Get the log frontend component.
     * @return LoggerInterface|null
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * Frontend for methods of logger component.
     *
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     * @throws \Tbs\Log\Exception
     */
    public static function __callStatic($method, $args = array())
    {
        $logger = self::getInstance()->getLogger();
        if (is_null($logger)) {
            throw new \Tbs\Log\Exception('The logger object is not set');
        }
        $reflection = new \ReflectionObject($logger);
        return $reflection->getMethod($method)->invokeArgs($logger, $args);
    }
}
