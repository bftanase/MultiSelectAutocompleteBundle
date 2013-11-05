<?php

namespace Btanase\MultiSelectAutocompleteBundle\Tests\Model;


/**
 * @author Bogdan Tanase <bftanase@gmail.com>
 */
class Address {
    private $town;

    /**
     * @param string $town
     */
    public function setTown($town)
    {
        $this->town = $town;
    }

    /**
     * @return string
     */
    public function getTown()
    {
        return $this->town;
    }


} 