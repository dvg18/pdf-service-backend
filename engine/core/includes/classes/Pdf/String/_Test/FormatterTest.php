<?php

namespace Test\Pdf\String;

use \Pdf\String\Formatter;
use \Pdf\String\Formatter\Options;

class FormatterTest extends \PHPUnit_Framework_TestCase
{

    protected static $_formatter;

    /**
     *
     * @var array $reg mapping char code to width for regular font
     */
    protected static $reg = array(
        32 => 7, /*   */ 33 => 8, /* ! */ 34 => 9, /* " */ 72 => 17, /* H */
        80 => 16, /* P */ 85 => 17, /* U */ 105 => 5, /* i */ 107 => 12, /* k */
        110 => 14, /* n */ 111 => 13, /* o */ 114 => 8, /* r */ 115 => 12, /* s */
        116 => 7, /* t */ 119 => 17 /* w */
    );

    /**
     *
     * @var array $bold mapping char code to width for bold font
     */
    protected static $bold = array(
        32 => 7, /*   */ 33 => 7, /* ! */ 34 => 11, /* " */ 72 => 17, /* H */
        80 => 16, /* P */ 85 => 17, /* U */ 105 => 7, /* i */ 107 => 13, /* k */
        110 => 15, /* n */ 111 => 15, /* o */ 114 => 9, /* r */ 115 => 13, /* s */
        116 => 8, /* t */ 119 => 19 /* w */
    );

    // &quot;PHPUnit&notExists;-&nbsp;works!&quot;
    static function setUpBeforeClass()
    {
        $entities = array(
            '&nbsp;' => '32',
            '&quot;' => '34',
        );
        self::$_formatter = new Formatter($entities);
    }

    function dataForGetStringWidthTest()
    {
        return array(
            array(194, '&quot;PHPUnit&notExists;-&nbsp;works!&quot;'),
            array(187, '&quot;PHPUnit&nbsp;works!&quot;'),
            array(187, '"PHPUnit works!"'),
            array(208, '<b>&quot;PHPUnit&notExists;-&nbsp;works!&quot;</b>'),
            array(198, '&quot;<b>PHPUnit</b>&notExists;-&nbsp;works!&quot;'),
            array(198, '&quot;<b>PHPUnit&notExists;-</b>&nbsp;works!&quot;'),
            array(191, '&quot;<b>PHPUnit</b>&nbsp;works!&quot;'),
            array(191, '"<b>PHPUnit</b> works!"'),
            array(201, '<b>&quot;PHPUnit&nbsp;works!&quot;</b>'),
            array(201, '<b>"PHPUnit works!"</b>'),
            array(201, '&quot;<b>PHP</b>Unit<b>&notExists;-&nbsp;works</b>!&quot;'),
            array(201, '"<b>PHP</b>Unit<b>&notExists;- works</b>!"'),
            array(194, '&quot;<b>PHP</b>Unit<b>&nbsp;works</b>!&quot;'),
            array(194, '"<b>PHP</b>Unit<b> works</b>!"'),
        );
    }

    function dataForProcessingSTringTest()
    {
        return array(
            array(array(200, 200),
                '&quot;PHPUnit&notExists;-&nbsp;works!&quot;',
                array('"PHPUnit  works!"')),
            array(array(100, 100),
                '&quot;PHPUnit&notExists;-&nbsp;works!&quot;',
                array('"PHPUnit', 'works!"')),
            array(array(100, 10),
                '&quot;PHPUnit&notExists;-&nbsp;works!&quot;',
                array('"PHPUnit', '')),
            array(array(10, 10),
                '&quot;PHPUnit&notExists;-&nbsp;works!&quot;',
                array('"PHPUnit', '')),
            array(array(100, 0),
                '&quot;PHPUnit&notExists;-&nbsp;works!&quot;',
                array('"PHPUnit', '')),
            array(array(10, 0),
                '&quot;PHPUnit&notExists;-&nbsp;works!&quot;',
                array('"PHPUnit', '')),
            array(array(0, 0),
                '&quot;PHPUnit&notExists;-&nbsp;works!&quot;',
                array('"PHPUnit', ''))
        );
    }

    function testConstructor()
    {
        $this->assertTrue(self::$_formatter != NULL);
    }

    function testCallbacks()
    {
        $options = new Options(self::$reg, self::$bold, 0);
        $cb = $this->getMock('FormatterCallback',
            array('callbackUE', 'callbackUC'));
        $cb->expects($this->once())
            ->method('callbackUE')
            ->with('&notExists;', '"PHPUnit&notExists;- works!"');
        $cb->expects($this->once())
            ->method('callbackUC')
            ->with('-', '"PHPUnit - works!"');
        self::$_formatter->setCallbackOnUndefinedEntity(array($cb, 'callbackUE'));
        self::$_formatter->setCallbackOnUndefinedChar(array($cb, 'callbackUC'));

        self::$_formatter->getStringWidth(
            '&quot;PHPUnit&nbsp;works!&quot;', $options
        );
        self::$_formatter->getStringWidth(
            '&quot;PHPUnit&notExists;-&nbsp;works!&quot;', $options
        );

        self::$_formatter->setCallbackOnUndefinedEntity(FALSE);
        self::$_formatter->setCallbackOnUndefinedChar(FALSE);
    }

    /**
     * @dataProvider dataForGetStringWidthTest
     */
    function testGetStringWidth($expectedWidth, $testString)
    {
        $options = new Options(self::$reg, self::$bold, 0);
        $this->assertEquals(
            $expectedWidth,
            self::$_formatter->getStringWidth(
                $testString, $options
            )
        );
    }

    /**
     * @dataProvider dataForProcessingSTringTest
     */
    function testProcessString($lineWidthSets, $string, $expectedResult)
    {
        $options = new Options(
            self::$reg, self::$bold, $lineWidthSets
        );
        $test = self::$_formatter->processString($string, $options);
        $this->assertEquals($test, $expectedResult);
    }

}
