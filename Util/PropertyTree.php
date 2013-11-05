<?php

namespace Btanase\MultiSelectAutocompleteBundle\Util;


/**
 * @author Bogdan Tanase <bftanase@gmail.com>
 */
class PropertyTree {

    /* @var string */
    private $propertyName;

    /* @var \Btanase\MultiSelectAutocompleteBundle\Util\PropertyTree */
    private $childProperty;

    function __construct($propertyName)
    {
        $this->propertyName = $propertyName;
    }

    /**
     * @param mixed $childProperty
     */
    public function setChildProperty(PropertyTree $childProperty)
    {
        $this->childProperty = $childProperty;
    }

    /**
     * @return PropertyTree
     */
    public function getChildProperty()
    {
        return $this->childProperty;
    }

    /**
     * @param string $propertyName
     */
    public function setPropertyName($propertyName)
    {
        $this->propertyName = $propertyName;
    }

    /**
     * @return string
     */
    public function getPropertyName()
    {
        return $this->propertyName;
    }


} 