<?php

namespace Test\FlexiPeeHP\Bricks;

use \FlexiPeeHP\Bricks\Convertor;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2019-02-21 at 12:27:19.
 */
class ConvertorTest extends \Test\Ease\SandTest
{
    /**
     * @var Convertor
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() : void
    {
        $this->object = new Convertor();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() : void
    {
        
    }

    /**
     * Test Constructor
     *
     * @covers FlexiPeeHP\Bricks\Convertor::__construct
     */
    public function testConstructor()
    {
        $classname = get_class($this->object);
        $evidence  = $this->object->getEvidence();

        // Get mock, without the constructor being called
        $mock = $this->getMockBuilder($classname)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $mock->__construct(new \FlexiPeeHP\FakturaVydana(),new \FlexiPeeHP\FakturaPrijata());
    }
    
    
    
    /**
     * @covers FlexiPeeHP\Bricks\Convertor::setSource
     */
    public function testSetSource()
    {
        $sourcer = new \FlexiPeeHP\FakturaVydana();
        $this->object->setSource($sourcer);
        $this->assertEquals($sourcer, $this->object->getInput());
    }

    /**
     * @covers FlexiPeeHP\Bricks\Convertor::setDestination
     */
    public function testSetDestination()
    {
        $dester = new \FlexiPeeHP\FakturaPrijata();
        $this->object->setDestination($dester);
        $this->assertEquals($dester, $this->object->getOutput());
    }

    /**
     * @covers FlexiPeeHP\Bricks\Convertor::conversion
     * @expectedException \Ease\Exception
     */
    public function testConversion()
    {
        $this->object->conversion();
    }

    /**
     * @covers FlexiPeeHP\Bricks\Convertor::baseClassName
     */
    public function testBaseClassName()
    {
        $this->assertEquals('FakturaVydana',
            $this->object->baseClassName(new \FlexiPeeHP\FakturaVydana()));
    }

    /**
     * @covers FlexiPeeHP\Bricks\Convertor::prepareRules
     * @todo   Implement testPrepareRules().
     */
    public function testPrepareRules()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers FlexiPeeHP\Bricks\Convertor::convertDocument
     * @todo   Implement testConvertDocument().
     */
    public function testConvertDocument()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers FlexiPeeHP\Bricks\Convertor::convertSubitems
     * @todo   Implement testConvertSubitems().
     */
    public function testConvertSubitems()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers FlexiPeeHP\Bricks\Convertor::convertItems
     * @todo   Implement testConvertItems().
     */
    public function testConvertItems()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers FlexiPeeHP\Bricks\Convertor::removeRoColumns
     * @todo   Implement testRemoveRoColumns().
     */
    public function testRemoveRoColumns()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers FlexiPeeHP\Bricks\Convertor::commonItems
     * @todo   Implement testCommonItems().
     */
    public function testCommonItems()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers FlexiPeeHP\Bricks\Convertor::getInput
     */
    public function testGetInput()
    {
        $test = new \FlexiPeeHP\Adresar();
        $this->object->setSource($test);
        $this->assertEquals($test, $this->object->getInput());
    }

    /**
     * @covers FlexiPeeHP\Bricks\Convertor::getOutput
     */
    public function testGetOutput()
    {
        $test = new \FlexiPeeHP\Adresar();
        $this->object->setDestination($test);
        $this->assertEquals($test, $this->object->getOutput());
    }
}
