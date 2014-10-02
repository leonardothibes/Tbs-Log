<?php
/**
 * @category Tests
 * @package Tbs
 * @subpackage Log
 * @author Leonardo Thibes <eu@leonardothibes.com>
 * @copyright Copyright (c) The Authors
 */

namespace Tbs\Log;
use \Tbs\Log\LogLevel;
require_once dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'Bootstrap.php';

/**
 * @category Tests
 * @package Tbs
 * @subpackage Log
 * @author Leonardo Thibes <eu@leonardothibes.com>
 * @copyright Copyright (c) The Authors
 */
class LogLevelTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Tbs\Log\LogLevel
     */
    protected $object = null;

    /**
     * Setup.
     */
    protected function setUp()
    {
        $this->object = new LogLevel;
    }

    /**
     * TearDown.
     */
    protected function tearDown()
    {
        unset($this->object);
    }

    /**
     * Provider of constants and values.
     * @return array
     */
    public function providerConstantsValues()
    {
        return array(
            array('EMERGENCY', 'emergency'),
            array('ALERT'    , 'alert'),
            array('CRITICAL' , 'critical'),
            array('ERROR'    , 'error'),
            array('WARNING'  , 'warning'),
            array('NOTICE'   , 'notice'),
            array('INFO'     , 'info'),
            array('DEBUG'    , 'debug'),
        );
    }

    /**
     * @see \Tbs\Log\LogLevel
     * @dataProvider providerConstantsValues
     */
    public function testConstantsValues($constantName, $constantValue)
    {
        $class      = get_class($this->object);
        $reflection = new \ReflectionClass($class);
        $constants  = $reflection->getConstants();

        $this->assertInternalType('array', $constants);
        $this->assertEquals(8, count($constants));
        $this->assertArrayHasKey($constantName, $constants);
        $this->assertInternalType('string', $constants[$constantName]);
        $this->assertEquals($constantValue, $constants[$constantName]);
    }
}
