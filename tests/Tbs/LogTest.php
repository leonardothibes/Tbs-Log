<?php
/**
 * @category Tests
 * @package Tbs
 * @subpackage Version
 * @author Leonardo Thibes <eu@leonardothibes.com>
 * @copyright Copyright (c) The Authors
 */

namespace Tbs;
use \Tbs\Log          as log;
use \Tbs\Log\File     as file;
use \Tbs\Log\LogLevel as Level;
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'Bootstrap.php';

/**
 * @category Tests
 * @package Tbs
 * @subpackage Version
 * @author Leonardo Thibes <eu@leonardothibes.com>
 * @copyright Copyright (c) The Authors
 */
class LogTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Tbs\Log
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
        log::getInstance()->resetInstance();
        $this->logfile = sprintf('%s/test.log', STUFF_PATH);
    	$this->object  = log::getInstance()->setLogger(
    	    new file($this->logfile)
        );
    }

    /**
     * TearDown.
     */
    protected function tearDown()
    {
        log::getInstance()->resetInstance();
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
            array('this is a log message', Level::EMERGENCY),
            array('this is a log message', Level::ALERT),
            array('this is a log message', Level::CRITICAL),
            array('this is a log message', Level::ERROR),
            array('this is a log message', Level::WARNING),
            array('this is a log message', Level::NOTICE),
            array('this is a log message', Level::INFO),
            array('this is a log message', Level::DEBUG),
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
        log::getInstance()->resetInstance();
        $rs = $this->object->getLogger();
        $this->assertNull($rs);

        $rs = log::getInstance()->resetInstance()->getLogger();
        $this->assertNull($rs);
    }

    /**
     * @see \Tbs\Log:__callStatic()
     * @dataProvider providerLogMessages
     */
    public function testLogOneLine($message, $level)
    {
        $line = sprintf('%s [%s]: %s', date('Y-m-d H:i:s'), strtoupper($level), $message) . "\n";
        log::log($level, $message);

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
        log::$level($message);

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
            log::log($level, $message . "($i)");
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
            log::$level($message . "($i)");
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

        log::log($level, $message, $context);
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

        log::$level($message, $context);
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
        log::log($level, $array);

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
        log::$level($array);

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
        log::log($level, $array, $context);

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
        log::$level($array, $context);

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
     * @expectedException \Tbs\Log\Exception
     */
    public function test__callStaticException($message, $level)
    {
        log::getInstance()->resetInstance();
        log::log($level, $message);
    }

    /**
     * @see \Tbs\Log:__callStatic()
     * @dataProvider providerLogMessages
     * @expectedException \Tbs\Log\Exception
     */
    public function test__callStaticLevelException($message, $level)
    {
        log::getInstance()->resetInstance();
        log::$level($message);
    }
}
