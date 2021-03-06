<?php

namespace Tbs;

use \Tbs\Log;
use \Tbs\Log\File;
use \Tbs\Log\LogLevel;

require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'bootstrap.php';

/**
 * @package Tbs\Log
 * @author Leonardo Thibes <leonardothibes@gmail.com>
 * @copyright Copyright (c) The Authors
 */
class LogTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Log
     */
    protected $object = null;

    /**
     * @var string
     */
    protected $logfile = null;

    /**
     * Setup.
     */
    protected function setUp()
    {
        Log::getInstance()->resetInstance();
        $this->logfile = sprintf('%s/test.log', STUFF_PATH);
    	$this->object  = Log::getInstance()->setLogger(
    	    new File($this->logfile)
        );
    }

    /**
     * TearDown.
     */
    protected function tearDown()
    {
        Log::getInstance()->resetInstance();
        @unlink($this->logfile);
    	unset($this->object);
    }

    /**
     * Provider of log messages.
     * @return array
     */
    public function providerLogMessages()
    {
        return array(
            array('this is a log message', LogLevel::EMERGENCY),
            array('this is a log message', LogLevel::ALERT),
            array('this is a log message', LogLevel::CRITICAL),
            array('this is a log message', LogLevel::ERROR),
            array('this is a log message', LogLevel::WARNING),
            array('this is a log message', LogLevel::NOTICE),
            array('this is a log message', LogLevel::INFO),
            array('this is a log message', LogLevel::DEBUG),
        );
    }

    /**
     * Test if implements the right interface.
     */
    public function testInterface()
    {
        $this->assertInstanceOf('\Tbs\Log\Interfaces\LoggerAwareInterface', $this->object);
    }

    /**
     * @see \Tbs\Log::setLogger()
     * @see \Tbs\Log::getLogger()
     */
    public function testSetGetLogger()
    {
        //Setting the logger.
        $logger = new file($this->logfile);
        $rs     = $this->object->setLogger($logger);
        $this->assertInstanceOf('\Tbs\Log', $rs);

        //Getting the logger.
        $rs = $this->object->getLogger();
        $this->assertInstanceOf('\Tbs\Log\File', $rs);
    }

    /**
     * @see \Tbs\Log::resetInstance()
     */
    public function testResetInstance()
    {
        Log::getInstance()->resetInstance();
        $rs = $this->object->getLogger();
        $this->assertNull($rs);

        $rs = Log::getInstance()->resetInstance()->getLogger();
        $this->assertNull($rs);
    }

    /**
     * @see \Tbs\Log:__callStatic()
     * @dataProvider providerLogMessages
     */
    public function testLogOneLine($message, $level)
    {
        $line = sprintf('%s [%s]: %s', date('Y-m-d H:i:s'), strtoupper($level), $message) . "\n";
        Log::log($level, $message);

        $rs = file_get_contents($this->logfile);
        $this->assertEquals($line, $rs);
    }

    /**
     * @see \Tbs\Log:__callStatic()
     * @dataProvider providerLogMessages
     */
    public function testLogLevelOneLine($message, $level)
    {
        $line = sprintf('%s [%s]: %s', date('Y-m-d H:i:s'), strtoupper($level), $message) . "\n";
        Log::$level($message);

        $rs = file_get_contents($this->logfile);
        $this->assertEquals($line, $rs);
    }

    /**
     * @see \Tbs\Log:__callStatic()
     * @dataProvider providerLogMessages
     */
    public function testLogMultiLines($message, $level)
    {
        $total = rand(5,10);
        for ($i = 1; $i <= $total; $i++) {
            $line = sprintf('%s [%s]: %s', date('Y-m-d H:i:s'), strtoupper($level), $message) . "\n";
            Log::log($level, $message . "($i)");
        }

        $rs    = file_get_contents($this->logfile);
        $lines = @explode("\n", $rs);
        $this->assertEquals($total+1, count($lines));

        foreach ($lines as $line) {
            if (strlen($line)) {
                $regexp = '/^' . date('Y-m-d H:i:s') . ' \['.strtoupper($level).'\]: this is a log message\([0-9]{1,2}\)$/';
                $this->assertRegExp($regexp, $line);
            }
        }
    }

    /**
     * @see \Tbs\Log:__callStatic()
     * @dataProvider providerLogMessages
     */
    public function testLogLevelMultiLines($message, $level)
    {
        $total = rand(5,10);
        for ($i = 1; $i <= $total; $i++) {
            $line = sprintf('%s [%s]: %s', date('Y-m-d H:i:s'), strtoupper($level), $message) . "\n";
            Log::$level($message . "($i)");
        }

        $rs    = file_get_contents($this->logfile);
        $lines = @explode("\n", $rs);
        $this->assertEquals($total+1, count($lines));

        foreach ($lines as $line) {
            if (strlen($line)) {
                $regexp = '/^[0-9]{4}\-[0-9]{2}\-[0-9]{2} [0-9]{2}\:[0-9]{2}\:[0-9]{2} \['.strtoupper($level).'\]: this is a log message\([0-9]{1,2}\)$/';
                $this->assertRegExp($regexp, $line);
            }
        }
    }

    /**
     * @see \Tbs\Log:__callStatic()
     * @dataProvider providerLogMessages
     */
    public function testLogInterpolate($message, $level)
    {
        $message .= ' with tags: {tag1} {tag2} {tag3}';
        $line     = sprintf('%s [%s]: %s', date('Y-m-d H:i:s'), strtoupper($level), $message) . "\n";
        $context  = array(
            'tag1' => 'this is a tag1 context',
            'tag2' => 'this is a tag2 context',
            'tag3' => 'this is a tag3 context',
        );

        Log::log($level, $message, $context);
        $rs = file_get_contents($this->logfile);

        $newmess = 'this is a log message with tags: this is a tag1 context this is a tag2 context this is a tag3 context';
        $newline = sprintf('%s [%s]: %s', date('Y-m-d H:i:s'), strtoupper($level), $newmess) . "\n";

        $this->assertEquals($newline, $rs);
    }

    /**
     * @see \Tbs\Log:__callStatic()
     * @dataProvider providerLogMessages
     */
    public function testLogLevelInterpolate($message, $level)
    {
        $message .= ' with tags: {tag1} {tag2} {tag3}';
        $line     = sprintf('%s [%s]: %s', date('Y-m-d H:i:s'), strtoupper($level), $message) . "\n";
        $context  = array(
            'tag1' => 'this is a tag1 context',
            'tag2' => 'this is a tag2 context',
            'tag3' => 'this is a tag3 context',
        );

        Log::$level($message, $context);
        $rs = file_get_contents($this->logfile);

        $newmess = 'this is a log message with tags: this is a tag1 context this is a tag2 context this is a tag3 context';
        $newline = sprintf('%s [%s]: %s', date('Y-m-d H:i:s'), strtoupper($level), $newmess) . "\n";

        $this->assertEquals($newline, $rs);
    }

    /**
     * @see \Tbs\Log:__callStatic()
     * @dataProvider providerLogMessages
     */
    public function testLogDebug($message, $level)
    {
        $array = array(
            'id1' => $message . '(1)',
            'id2' => $message . '(2)',
            'id3' => $message . '(3)',
        );
        Log::log($level, $array);

        $rs = file_get_contents($this->logfile);
        $rs = @explode("\n", $rs);
        unset($rs[6], $rs[7]);

        $regexp = '/^[0-9]{4}\-[0-9]{2}\-[0-9]{2} [0-9]{2}\:[0-9]{2}\:[0-9]{2} \['. strtoupper($level) .'\]: Array$/';
        $this->assertRegExp($regexp, trim($rs[0]));

        $this->assertEquals('(', $rs[1]);
        $this->assertEquals('    [id1] => this is a log message(1)', $rs[2]);
        $this->assertEquals('    [id2] => this is a log message(2)', $rs[3]);
        $this->assertEquals('    [id3] => this is a log message(3)', $rs[4]);
        $this->assertEquals(')', $rs[5]);
    }

    /**
     * @see \Tbs\Log:__callStatic()
     * @dataProvider providerLogMessages
     */
    public function testLogLevelDebug($message, $level)
    {
        $array = array(
            'id1' => $message . '(1)',
            'id2' => $message . '(2)',
            'id3' => $message . '(3)',
        );
        Log::$level($array);

        $rs = file_get_contents($this->logfile);
        $rs = @explode("\n", $rs);
        unset($rs[6], $rs[7]);

        $regexp = '/^[0-9]{4}\-[0-9]{2}\-[0-9]{2} [0-9]{2}\:[0-9]{2}\:[0-9]{2} \['. strtoupper($level) .'\]: Array$/';
        $this->assertRegExp($regexp, trim($rs[0]));

        $this->assertEquals('(', $rs[1]);
        $this->assertEquals('    [id1] => this is a log message(1)', $rs[2]);
        $this->assertEquals('    [id2] => this is a log message(2)', $rs[3]);
        $this->assertEquals('    [id3] => this is a log message(3)', $rs[4]);
        $this->assertEquals(')', $rs[5]);
    }

    /**
     * @see \Tbs\Log:__callStatic()
     * @dataProvider providerLogMessages
     */
    public function testLogContextDebug($message, $level)
    {
        $array = array(
            'id1' => $message . ' {tag1}(1)',
            'id2' => $message . ' {tag2}(2)',
            'id3' => $message . ' {tag3}(3)',
        );
        $context = array(
            'tag1' => 'tag 1 content',
            'tag2' => 'tag 2 content',
            'tag3' => 'tag 3 content',
        );
        Log::log($level, $array, $context);

        $rs = file_get_contents($this->logfile);
        $rs = @explode("\n", $rs);
        unset($rs[6], $rs[7]);

        $regexp = '/^[0-9]{4}\-[0-9]{2}\-[0-9]{2} [0-9]{2}\:[0-9]{2}\:[0-9]{2} \['. strtoupper($level) .'\]: Array$/';
        $this->assertRegExp($regexp, trim($rs[0]));

        $this->assertEquals('(', $rs[1]);
        $this->assertEquals('    [id1] => this is a log message tag 1 content(1)', $rs[2]);
        $this->assertEquals('    [id2] => this is a log message tag 2 content(2)', $rs[3]);
        $this->assertEquals('    [id3] => this is a log message tag 3 content(3)', $rs[4]);
        $this->assertEquals(')', $rs[5]);
    }

    /**
     * @see \Tbs\Log:__callStatic()
     * @dataProvider providerLogMessages
     */
    public function testLogLevelContextDebug($message, $level)
    {
        $array = array(
            'id1' => $message . ' {tag1}(1)',
            'id2' => $message . ' {tag2}(2)',
            'id3' => $message . ' {tag3}(3)',
        );
        $context = array(
            'tag1' => 'tag 1 content',
            'tag2' => 'tag 2 content',
            'tag3' => 'tag 3 content',
        );
        Log::$level($array, $context);

        $rs = file_get_contents($this->logfile);
        $rs = @explode("\n", $rs);
        unset($rs[6], $rs[7]);

        $regexp = '/^[0-9]{4}\-[0-9]{2}\-[0-9]{2} [0-9]{2}\:[0-9]{2}\:[0-9]{2} \['. strtoupper($level) .'\]: Array$/';
        $this->assertRegExp($regexp, trim($rs[0]));

        $this->assertEquals('(', $rs[1]);
        $this->assertEquals('    [id1] => this is a log message tag 1 content(1)', $rs[2]);
        $this->assertEquals('    [id2] => this is a log message tag 2 content(2)', $rs[3]);
        $this->assertEquals('    [id3] => this is a log message tag 3 content(3)', $rs[4]);
        $this->assertEquals(')', $rs[5]);
    }

    /**
     * @see \Tbs\Log:__callStatic()
     * @dataProvider providerLogMessages
     * @expectedException \Tbs\Log\LogException
     */
    public function test__callStaticException($message, $level)
    {
        Log::getInstance()->resetInstance();
        Log::log($level, $message);
    }

    /**
     * @see \Tbs\Log:__callStatic()
     * @dataProvider providerLogMessages
     * @expectedException \Tbs\Log\LogException
     */
    public function test__callStaticLevelException($message, $level)
    {
        Log::getInstance()->resetInstance();
        Log::$level($message);
    }
}
