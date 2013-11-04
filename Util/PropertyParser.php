<?php

namespace Btanase\MultiSelectAutocompleteBundle\Util;


/**
 * @author Bogdan Tanase <bftanase@gmail.com>
 */
class PropertyParser {

    private $propertyString;

    private $propertyTree = array();

    /**
     * property must be an expression in the format:
     *
     * '#{address.city} | #{familyName} #{givenName}'
     *
     * or
     *
     * a simple property name
     *
     * 'fullName'
     *
     * @param $propertyString
     */
    function __construct($propertyString)
    {
        $this->propertyString = $propertyString;

        $this->buildPropertyTree();
    }

    public function buildLabelValue($object)
    {

    }




    /**
     * @return mixed
     */
    public function getPropertyTree()
    {
        return $this->propertyTree;
    }

    private function buildPropertyTree()
    {
        $matches = array();
        // in case there's a simple property
        if (preg_match('/^\w+$/', $this->propertyString, $matches)) {
            $this->propertyTree[] = $this->propertyString;
        } else if (false) {

        } else {
            throw new \InvalidArgumentException();
        }
    }


} 