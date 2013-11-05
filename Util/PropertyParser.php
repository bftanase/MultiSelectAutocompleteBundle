<?php

namespace Btanase\MultiSelectAutocompleteBundle\Util;


/**
 * @author Bogdan Tanase <bftanase@gmail.com>
 */
class PropertyParser
{

    private $propertyString;

    private $propertyTreeList = array();

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

        return $this;
    }

    public function getLabelValue($object)
    {
        $labelValue = $this->propertyString;

        foreach ($this->propertyTreeList as $elem) {
            /* @var $propertyTree PropertyTree */
            $propertyTree = $elem['propertyTree'];
            $tempObject = $object;
            do {
                if (!$tempObject || is_string($tempObject)) {
                    break;
                }

                $tempObject = $tempObject->{'get'.ucwords($propertyTree->getPropertyName())}();
            } while ($propertyTree = $propertyTree->getChildProperty());

            $labelValue = str_replace($elem['matchedExpression'], $tempObject, $labelValue);
        }

        return $labelValue;
    }


    /**
     * @return PropertyTree[]
     */
    public function getPropertyTreeList()
    {
        return $this->propertyTreeList;
    }

    private function buildPropertyTree()
    {
        $matches = array();
        // in case there's a simple property
        if (preg_match('/^\w+$/', $this->propertyString, $matches)) {
            $this->propertyTreeList[] = array(
                'matchedExpression' => $matches[0],
                'propertyTree' => new PropertyTree($this->propertyString)
            );

        } else if (preg_match_all('/(#\{([\w]+(?:\.\w+)*)\})/', $this->propertyString, $matches)) {
            foreach ($matches[0] as $key => $expression) {
                $propertyPath = $matches[2][$key];

                $properties = explode('.', $propertyPath);

                /* @var $propertyTree \Btanase\MultiSelectAutocompleteBundle\Util\PropertyTree */
                $propertyTree = null;
                $root = null;

                foreach ($properties as $property) {
                    if (!$propertyTree) {
                        $propertyTree = new PropertyTree($property);
                        $root = $propertyTree;
                    } else {
                        $child = new PropertyTree($property);
                        $propertyTree->setChildProperty($child);
                        $propertyTree = $child;
                    }
                }

                $this->propertyTreeList[] = array(
                    'matchedExpression' => $expression,
                    'propertyTree' => $root
                );
            }
        } else {
            throw new \InvalidArgumentException();
        }
    }


} 