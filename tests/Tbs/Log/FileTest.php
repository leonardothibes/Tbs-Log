<?php
/**
 * @category Tests
 * @package Tbs
 * @subpackage Log
 * @author Leonardo Thibes <eu@leonardothibes.com>
 * @copyright Copyright (c) The Authors
 */

namespace Tbs\Log;
use \Tbs\Log\File     as Log;
use \Tbs\Log\LogLevel as Level;
require_once dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'Bootstrap.php';

/**
 * @category Tests
 * @package Tbs
 * @subpackage Log
 * @author Leonardo Thibes <eu@leonardothibes.com>
 * @copyright Copyright (c) The Authors
 */
class FileTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Tbs\Log\File
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
        $this->logfile = sprintf('%s/test.log', STUFF_PATH);
    	$this->object  = new Log($this->logfile);
    }

    /**
     * TearDown.
     */
    protected function tearDown()
    {
        @unlink($this->logfile);
    	unset($this->object);
    }

    /**
     * Test if implements the right interface.
     */
    public function testInterface()
    {
        $this->assertInstanceOf('\Tbs\Log\Interfaces\LoggerInterface', $this->object);
    }

    /**
     * @see \Tbs\Log\File::__construct()
     */
    public function testLogFileExists()
    {
        $this->assertTrue(file_exists($this->logfile));
    }

    /**
     * @see \Tbs\Log\File::__construct()
     */
    public function testLogFileIsWritable()
    {
        $this->assertTrue(is_writable($this->logfile));
    }

    /**
     * @see \Tbs\Log\File::getLogFile()
     */
    public function testGetLogFile()
    {
        $rs = $this->object->getLogFile();
        $this->assertEquals($this->logfile, $rs);
        $this->assertTrue(file_exists($rs));
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
     * @see \Tbs\Log\File::log()
     * @dataProvider providerLogMessages
     */
    public function testLogOneLine($message, $level)
    {
        $line = sprintf('%s [%s]: %s', date('Y-m-d H:i:s'), strtoupper($level), $message) . "\n";
        $this->object->log($level, $message);
        $rs = file_get_contents($this->logfile);
        $this->assertEquals($line, $rs);
    }

    /**
     * @see \Tbs\Log\File::emergency()
     * @dataProvider providerLogMessages
     */
    public function testLogLevelOneLine($message, $level)
    {
        $line = sprintf('%s [%s]: %s', date('Y-m-d H:i:s'), strtoupper($level), $message) . "\n";
        $this->object->{$level}($message);
        $rs = file_get_contents($this->logfile);
        $this->assertEquals($line, $rs);
    }

    /**
     * @see \Tbs\Log\File::log()
     * @dataProvider providerLogMessages
     */
    public function testLogMultiLines($message, $level)
    {
        $total = rand(5,10);
        for ($i = 1; $i <= $total; $i++) {
            $line = sprintf('%s [%s]: %s', date('Y-m-d H:i:s'), strtoupper($level), $message) . "\n";
            $this->object->log($level, $message . "($i)");
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
     * @see \Tbs\Log\File::log()
     * @dataProvider providerLogMessages
     */
    public function testLogLevelMultiLines($message, $level)
    {
        $total = rand(5,10);
        for ($i = 1; $i <= $total; $i++) {
            $line = sprintf('%s [%s]: %s', date('Y-m-d H:i:s'), strtoupper($level), $message) . "\n";
            $this->object->{$level}($message . "($i)");
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
     * @see \Tbs\Log\File::log()
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

        $this->object->log($level, $message, $context);
        $rs = file_get_contents($this->logfile);

        $newmess = 'this is a log message with tags: this is a tag1 context this is a tag2 context this is a tag3 context';
        $newline = sprintf('%s [%s]: %s', date('Y-m-d H:i:s'), strtoupper($level), $newmess) . "\n";

        $this->assertEquals($newline, $rs);
    }

    /**
     * @see \Tbs\Log\File::log()
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

        $this->object->{$level}($message, $context);
        $rs = file_get_contents($this->logfile);

        $newmess = 'this is a log message with tags: this is a tag1 context this is a tag2 context this is a tag3 context';
        $newline = sprintf('%s [%s]: %s', date('Y-m-d H:i:s'), strtoupper($level), $newmess) . "\n";

        $this->assertEquals($newline, $rs);
    }

    /**
     * @see \Tbs\Log\File::debug()
     * @dataProvider providerLogMessages
     */
    public function testLogDebug($message, $level)
    {
        $array = array(
            'id1' => $message . '(1)',
            'id2' => $message . '(2)',
            'id3' => $message . '(3)',
        );
        $this->object->log($level, $array);

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
     * @see \Tbs\Log\File::debug()
     * @dataProvider providerLogMessages
     */
    public function testLogLevelDebug($message, $level)
    {
        $array = array(
            'id1' => $message . '(1)',
            'id2' => $message . '(2)',
            'id3' => $message . '(3)',
        );
        $this->object->{$level}($array);

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
     * @see \Tbs\Log\File::debug()
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
        $this->object->log($level, $array, $context);

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
     * @see \Tbs\Log\File::debug()
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
        $this->object->{$level}($array, $context);

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
}
