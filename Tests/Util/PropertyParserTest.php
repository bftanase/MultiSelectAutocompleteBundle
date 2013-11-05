<?php

namespace Btanase\MultiSelectAutocompleteBundle\Tests\Util;

use Btanase\MultiSelectAutocompleteBundle\Tests\Model\Address;
use Btanase\MultiSelectAutocompleteBundle\Tests\Model\Client;
use Btanase\MultiSelectAutocompleteBundle\Tests\Model\Order;
use Btanase\MultiSelectAutocompleteBundle\Util\PropertyParser;

class PropertyParserTest extends \PHPUnit_Framework_TestCase {

    private $order;

    public function setUp()
    {
        $address = new Address();
        $address->setTown('Bucharest');

        $client = new Client();
        $client->setName('ACME LTD');
        $client->setAddress($address);

        $this->order = new Order();
        $this->order->setPrice(100);
        $this->order->setClient($client);

    }

    public function testBuildPropertyTree()
    {
        $property = 'fullName';

        $parser = new PropertyParser($property);

        $propertyTreeList = $parser->getPropertyTreeList();

        $this->assertTrue(is_array($propertyTreeList));
        $this->assertNotEmpty($propertyTreeList);
        $this->assertCount(1, $propertyTreeList);

        $this->assertTrue($propertyTreeList[0]['propertyTree']->getPropertyName() == $property);
        $this->assertNull($propertyTreeList[0]['propertyTree']->getChildProperty());

        $property = '#{address.town} #{price} #{user.type.name}';

        $parser = new PropertyParser($property);

        $propertyTreeList = $parser->getPropertyTreeList();

        $this->assertCount(3, $propertyTreeList);
        $this->assertEquals('address', $propertyTreeList[0]['propertyTree']->getPropertyName());
        $this->assertEquals('town', $propertyTreeList[0]['propertyTree']->getChildProperty()->getPropertyName());
        $this->assertEquals('price', $propertyTreeList[1]['propertyTree']->getPropertyName());
        $this->assertEquals('user', $propertyTreeList[2]['propertyTree']->getPropertyName());
        $this->assertEquals('type', $propertyTreeList[2]['propertyTree']->getChildProperty()->getPropertyName());
        $this->assertEquals('name', $propertyTreeList[2]['propertyTree']->getChildProperty()->getChildProperty()->getPropertyName());

        $this->assertEquals('#{address.town}', $propertyTreeList[0]['matchedExpression']);
        $this->assertEquals('#{price}', $propertyTreeList[1]['matchedExpression']);
        $this->assertEquals('#{user.type.name}', $propertyTreeList[2]['matchedExpression']);

    }

    public function testBuildLabelValue()
    {
        $propertyExpression = 'Name: #{client.name}, Town: #{client.address.town}, Price: #{price}, Age: #{client.age}';

        $propertyParser = new PropertyParser($propertyExpression);

        $value = $propertyParser->getLabelValue($this->order);

        $this->assertEquals('Name: ACME LTD, Town: Bucharest, Price: 100, Age: ', $value);
    }
}
 