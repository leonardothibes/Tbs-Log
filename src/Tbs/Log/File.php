<?php

namespace Tbs\Log;

use \Tbs\Log\Abstraction as A;
use \Tbs\Log\File\LogFileException;

/**
 * Class of log in disc file.
 *
 * @package Tbs\Log
 * @author Leonardo Thibes <leonardothibes@gmail.com>
 * @copyright Copyright (c) The Authors
 * @link <http://www.php-fig.org/psr/3/>
 */
class File extends A
{
    /**
     * Log file.
     * @var string
     */
    protected $logfile = null;

    /**
     * Define the log file location.
     *
     * @param string $logfile
     * @param int    $mode
     *
     * @throws LogException
     * @throws LogFileException
     */
    public function __construct($logfile, $mode = 0777)
    {
        if (!strlen($logfile)) {
            throw new LogFileException('Log file could not be blank');
        }

        $logdir = dirname($logfile);
        if (!is_dir($logdir) or !is_writable($logdir)) {
            $message = sprintf('No write access to the log directory: %s', $logdir);
            throw new LogException($message);
        }

        if (!file_exists($logfile)) {
            @touch($logfile);
            @chmod($logfile, $mode);
        }

        $this->logfile = $logfile;
    }

    /**
     * Returns the path of log file.
     * @return string
     */
    public function getLogFile()
    {
        return (string)$this->logfile;
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed  $level
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function log($level, $message, array $context = array())
    {
        $message = $this->prepare($level, $message, $context);
        file_put_contents($this->logfile, $message . "\n", FILE_APPEND | LOCK_EX);
    }
}
