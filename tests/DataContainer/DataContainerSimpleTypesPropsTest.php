<?php namespace Craft\Tests\Data\Container;

use Craft\Tests\DataContainer\ArrayItemContainer;
use Craft\Tests\DataContainer\SimpleTypesContainer;

class DataContainerSimpleTypesPropsTest extends \Codeception\Test\Unit
{

    public function testBasicFeatures()
    {
        $input = [
            'number' => 1000,
            'text' => 'test',
            'flag' => true
        ];

        $data = new SimpleTypesContainer($input);

        $output = $data->toArray();

        $this->assertIsArray($output);
        $this->assertArrayHasKey('number', $output);
        $this->assertArrayHasKey('text', $output);
        $this->assertArrayHasKey('flag', $output);

        // test public access types
        $this->assertIsInt($data->number);
        $this->assertIsString($data->text);
        $this->assertIsBool($data->flag);

        // test api access types
        $this->assertIsInt($data->getPropertyValue('number'));
        $this->assertIsString($data->getPropertyValue('text'));
        $this->assertIsBool($data->getPropertyValue('flag'));

        // test public access values
        $this->assertEquals(1000, $data->number);
        $this->assertEquals('test', $data->text);
        $this->assertEquals(true, $data->flag);

        // test api access values
        $this->assertEquals(1000, $data->getPropertyValue('number'));
        $this->assertEquals('test', $data->getPropertyValue('text'));
        $this->assertEquals(true, $data->getPropertyValue('flag'));

        // test mutations

        // test public mutation
        $data->number += 1000;
        $data->text .= ' mutation';
        $data->flag = false;

        $this->assertEquals(2000, $data->number);
        $this->assertEquals('test mutation', $data->text);
        $this->assertEquals(false, $data->flag);

        // test api mutation
        $data->addPropertyValue('number', 1000);
        $data->addPropertyValue('text', 'test');
        $data->addPropertyValue('flag', true);

        $this->assertEquals(1000, $data->getPropertyValue('number'));
        $this->assertEquals('test', $data->getPropertyValue('text'));
        $this->assertEquals(true, $data->getPropertyValue('flag'));
    }

    protected function _before()
    {
    }

    protected function _after()
    {
    }

}