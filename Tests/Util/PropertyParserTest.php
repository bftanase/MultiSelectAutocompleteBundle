<?php
/**
 * File Description
 * 
 */

namespace Btanase\MultiSelectAutocompleteBundle\Tests\Util;


use Btanase\MultiSelectAutocompleteBundle\Util\PropertyParser;

class PropertyParserTest extends \PHPUnit_Framework_TestCase {


    public function testBuildPropertyTree()
    {
        $property = 'fullName';

        $parser = new PropertyParser($property);

        var_dump($parser->getPropertyTree());

    }
}
 