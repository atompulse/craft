<?php namespace Craft\Tests\Data\Container;

use Craft\Tests\DataContainer\ArrayItemContainer;
use Craft\Tests\DataContainer\ArrayItemContainerNotNullableWithoutSetter;
use Craft\Tests\DataContainer\ArrayItemContainerNotNullableWithSetter;

class DataContainerArrayPropsTest extends \Codeception\Test\Unit
{

    public function testPopulatingNotNullableWithSetter()
    {
        $data = new ArrayItemContainerNotNullableWithSetter();
        $output = $data->toArray();

        $this->assertIsArray($output);
        $this->assertArrayNotHasKey('items', $output);

        // custom setter will skip setting empty array
        $data = new ArrayItemContainerNotNullableWithSetter(['items' => []]);
        $output = $data->toArray();
        $this->assertIsArray($output);
        $this->assertArrayNotHasKey('items', $output);

        // custom setter will skip setting NULL value
        $data = new ArrayItemContainerNotNullableWithSetter(['items' => null]);
        $output = $data->toArray();
        $this->assertIsArray($output);
        $this->assertArrayNotHasKey('items', $output);
    }

    public function testPopulatingNotNullableWithoutSetter()
    {
        $data = new ArrayItemContainerNotNullableWithoutSetter();
        $output = $data->toArray();

        $this->assertIsArray($output);
        $this->assertArrayNotHasKey('items', $output);

        // api method will initialize empty array
        $data = new ArrayItemContainerNotNullableWithoutSetter(['items' => []]);
        $output = $data->toArray();
        $this->assertIsArray($output);
        $this->assertArrayHasKey('items', $output);
        $this->assertEmpty($output['items']);

        // api method will throw exception
        $this->expectException(\Craft\Data\Container\Exception\PropertyValueNotValidException::class);
        $data = new ArrayItemContainerNotNullableWithoutSetter(['items' => null]);
    }

    public function testPopulatingNotNullableFromConstructorWithMissingProp()
    {
        $this->expectException(\Craft\Data\Container\Exception\PropertyMissingException::class);
        new ArrayItemContainerNotNullableWithSetter(['someValue']);
    }

    public function testPopulatingNotNullableFromPublicApi()
    {
        $data = new ArrayItemContainerNotNullableWithSetter();
        $this->expectException(\Craft\Data\Container\Exception\PropertyValueNotValidException::class);
        $data->addPropertyValue('items', null);
    }

    public function testPopulatingFromConstructor()
    {
        $items = [
            'item 1',
            'item 2',
            'item 3'
        ];

        $data = new ArrayItemContainer(['items' => $items]);

        $output = $data->toArray();

        $this->assertIsArray($output);
        $this->assertArrayHasKey('items', $output);

        $this->assertIsArray($output['items']);
        $this->assertCount(3, $output['items']);
        $this->assertContains('item 1', $output['items']);
        $this->assertContains('item 2', $output['items']);
        $this->assertContains('item 3', $output['items']);

        // test public access
        $this->assertIsArray($data->items);
        $this->assertCount(3, $data->items);
        $this->assertContains('item 1', $data->items);
        $this->assertContains('item 2', $data->items);
        $this->assertContains('item 3', $data->items);

        // test api access
        $this->assertIsArray($data->getPropertyValue('items'));
        $this->assertCount(3, $data->getPropertyValue('items'));
        $this->assertContains('item 1', $data->getPropertyValue('items'));
        $this->assertContains('item 2', $data->getPropertyValue('items'));
        $this->assertContains('item 3', $data->getPropertyValue('items'));

        // test push with custom public method
        $data->addItem('item 4');
        $output = $data->toArray();

        $this->assertIsArray($output);
        $this->assertArrayHasKey('items', $output);

        $this->assertIsArray($output['items']);
        $this->assertCount(4, $output['items']);
        $this->assertContains('item 1', $output['items']);
        $this->assertContains('item 2', $output['items']);
        $this->assertContains('item 3', $output['items']);
        $this->assertContains('item 4', $output['items']);

        // test push with api method
        $data->addPropertyValue('items', 'item 5');
        $output = $data->toArray();

        $this->assertIsArray($output);
        $this->assertArrayHasKey('items', $output);

        $this->assertIsArray($output['items']);
        $this->assertCount(5, $output['items']);
        $this->assertContains('item 1', $output['items']);
        $this->assertContains('item 2', $output['items']);
        $this->assertContains('item 3', $output['items']);
        $this->assertContains('item 4', $output['items']);
        $this->assertContains('item 5', $output['items']);

    }

    public function testPopulatingFromSetter()
    {
        $items = [
            'item 1',
            'item 2',
            'item 3'
        ];

        $data = new ArrayItemContainer();
        $data->setItems($items);

        $output = $data->toArray();

        $this->assertIsArray($output);
        $this->assertArrayHasKey('items', $output);

        $this->assertIsArray($output['items']);
        $this->assertCount(3, $output['items']);
        $this->assertContains('item 1', $output['items']);
        $this->assertContains('item 2', $output['items']);
        $this->assertContains('item 3', $output['items']);

        // test public access
        $this->assertIsArray($data->items);
        $this->assertCount(3, $data->items);
        $this->assertContains('item 1', $data->items);
        $this->assertContains('item 2', $data->items);
        $this->assertContains('item 3', $data->items);

        // test api access
        $this->assertIsArray($data->getPropertyValue('items'));
        $this->assertCount(3, $data->getPropertyValue('items'));
        $this->assertContains('item 1', $data->getPropertyValue('items'));
        $this->assertContains('item 2', $data->getPropertyValue('items'));
        $this->assertContains('item 3', $data->getPropertyValue('items'));


        // test push with custom public method
        $data->addItem('item 4');
        $output = $data->toArray();

        $this->assertIsArray($output);
        $this->assertArrayHasKey('items', $output);

        $this->assertIsArray($output['items']);
        $this->assertCount(4, $output['items']);
        $this->assertContains('item 1', $output['items']);
        $this->assertContains('item 2', $output['items']);
        $this->assertContains('item 3', $output['items']);
        $this->assertContains('item 4', $output['items']);

        // test push with api method
        $data->addPropertyValue('items', 'item 5');
        $output = $data->toArray();

        $this->assertIsArray($output);
        $this->assertArrayHasKey('items', $output);

        $this->assertIsArray($output['items']);
        $this->assertCount(5, $output['items']);
        $this->assertContains('item 1', $output['items']);
        $this->assertContains('item 2', $output['items']);
        $this->assertContains('item 3', $output['items']);
        $this->assertContains('item 4', $output['items']);
        $this->assertContains('item 5', $output['items']);
    }

    public function testPopulatingFromPublicProperty()
    {
        $items = [
            'item 1',
            'item 2',
            'item 3'
        ];

        $data = new ArrayItemContainer();
        $data->items = $items;

        $output = $data->toArray();

        $this->assertIsArray($output);
        $this->assertArrayHasKey('items', $output);

        $this->assertIsArray($output['items']);
        $this->assertCount(3, $output['items']);
        $this->assertContains('item 1', $output['items']);
        $this->assertContains('item 2', $output['items']);
        $this->assertContains('item 3', $output['items']);

        // test public access
        $this->assertIsArray($data->items);
        $this->assertCount(3, $data->items);
        $this->assertContains('item 1', $data->items);
        $this->assertContains('item 2', $data->items);
        $this->assertContains('item 3', $data->items);

        // test api access
        $this->assertIsArray($data->getPropertyValue('items'));
        $this->assertCount(3, $data->getPropertyValue('items'));
        $this->assertContains('item 1', $data->getPropertyValue('items'));
        $this->assertContains('item 2', $data->getPropertyValue('items'));
        $this->assertContains('item 3', $data->getPropertyValue('items'));

        // test push with custom public method
        $data->addItem('item 4');
        $output = $data->toArray();

        $this->assertIsArray($output);
        $this->assertArrayHasKey('items', $output);

        $this->assertIsArray($output['items']);
        $this->assertCount(4, $output['items']);
        $this->assertContains('item 1', $output['items']);
        $this->assertContains('item 2', $output['items']);
        $this->assertContains('item 3', $output['items']);
        $this->assertContains('item 4', $output['items']);

        // test push with api method
        $data->addPropertyValue('items', 'item 5');
        $output = $data->toArray();

        $this->assertIsArray($output);
        $this->assertArrayHasKey('items', $output);

        $this->assertIsArray($output['items']);
        $this->assertCount(5, $output['items']);
        $this->assertContains('item 1', $output['items']);
        $this->assertContains('item 2', $output['items']);
        $this->assertContains('item 3', $output['items']);
        $this->assertContains('item 4', $output['items']);
        $this->assertContains('item 5', $output['items']);
    }

    public function testNullValueHandling()
    {
        // test NULL with constructor
        $data = new ArrayItemContainer(['items' => null]);
        $output = $data->toArray();
        // since there is a setter(setItems) which avoids setting the value if input is null
        // the output of toArray should not contain the 'items' prop
        $this->assertIsArray($output);
        $this->assertArrayNotHasKey('items', $output);

        // test NULL with api method
        $data = new ArrayItemContainer();
        $data->addPropertyValue('items', null);
        // since the api method is used explicitly the 'items' prop should be present with NULL value
        $output = $data->toArray();
        $this->assertIsArray($output);
        $this->assertArrayHasKey('items', $output);

        // test NULL with public property
        $data = new ArrayItemContainer();
        $data->items = null;
        $output = $data->toArray();
        // on public property population if there's a setter method
        // then that method will be used to set the actual value
        // and since setItems avoids setting the value if input is null
        // the output of toArray should not contain the 'items' prop
        $this->assertIsArray($output);
        $this->assertArrayNotHasKey('items', $output);

        // test NULL with setter
        $data = new ArrayItemContainer();
        $data->setItems(null);
        // and since setItems avoids setting the value if input is null
        // the output of toArray should not contain the 'items' prop
        $output = $data->toArray();
        $this->assertIsArray($output);
        $this->assertArrayNotHasKey('items', $output);
    }

    protected function _before()
    {
    }

    protected function _after()
    {
    }

}