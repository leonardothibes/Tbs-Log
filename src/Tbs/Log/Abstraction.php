<?php
/**
 * @package Tbs\Log
 * @author Leonardo Thibes <eu@leonardothibes.com>
 * @copyright Copyright (c) The Authors
 */

namespace Tbs\Log;

use \Tbs\Log\Interfaces\LoggerInterface;
use \Tbs\Log\LogLevel;

/**
 * Abstract methods of log.
 *
 * @package Tbs\Log
 * @author Leonardo Thibes <eu@leonardothibes.com>
 * @copyright Copyright (c) The Authors
 * @link <http://www.php-fig.org/psr/3/>
 */
abstract class Abstraction implements LoggerInterface
{
    /**
     * System is unusable.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function emergency($message, array $context = array())
    {
        $this->log(LogLevel::EMERGENCY, $message, $context);
    }

    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
    */
    public function alert($message, array $context = array())
    {
        $this->log(LogLevel::ALERT, $message, $context);
    }

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
    */
    public function critical($message, array $context = array())
    {
        $this->log(LogLevel::CRITICAL, $message, $context);
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
    */
    public function error($message, array $context = array())
    {
        $this->log(LogLevel::ERROR, $message, $context);
    }

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
    */
    public function warning($message, array $context = array())
    {
        $this->log(LogLevel::WARNING, $message, $context);
    }

    /**
     * Normal but significant events.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
    */
    public function notice($message, array $context = array())
    {
        $this->log(LogLevel::NOTICE, $message, $context);
    }

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
    */
    public function info($message, array $context = array())
    {
        $this->log(LogLevel::INFO, $message, $context);
    }

    /**
     * Detailed debug information.
     *
     * @param mixed $message
     * @param array $context
     *
     * @return void
    */
    public function debug($message, array $context = array())
    {
        $this->log(LogLevel::DEBUG, $message, $context);
    }

    /**
     * Interpolates context values into the message placeholders.
     *
     * @param string $message
     * @param array  $context
     *
     * @return string
     */
    protected function interpolate($message, array $context = array())
    {
        if (count($context) > 0) {
            $message = strtr($message, $context);
            $message = str_replace(array('{', '}'), '', $message);
        }
        return $message;
    }

    /**
     * Format message to long.
     *
     * @param  string $message
     * @return string
     */
    protected function formatMessage($message, $level)
    {
        return sprintf('%s [%s]: %s', date('Y-m-d H:i:s'), strtoupper($level), $message);
    }

    /**
     * prepare the log message.
     *
     * @param mixed  $level
     * @param string $message
     * @param array  $context
     *
     * @return string
     */
    protected function prepare($level, $message, array $context = array())
    {
        //Intercepting the buffer content.
        ob_start();
        print_r($message);
        $message = ob_get_contents();
        ob_end_clean();
        //Intercepting the buffer content.

        //Preparing the string message.
        $message = $this->interpolate($message, $context);
        $message = $this->formatMessage($message, $level);
        //Preparing the string message.

        return $message;
    }
}
