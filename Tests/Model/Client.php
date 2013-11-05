<?php

namespace Btanase\MultiSelectAutocompleteBundle\Tests\Model;


/**
 * @author Bogdan Tanase <bftanase@gmail.com>
 */
class Client {

    private $name;

    private $age;
    /**
     * @var Address
     */
    private $address;

    /**
     * @param \Btanase\MultiSelectAutocompleteBundle\Tests\Model\Address $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return \Btanase\MultiSelectAutocompleteBundle\Tests\Model\Address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $age
     */
    public function setAge($age)
    {
        $this->age = $age;
    }

    /**
     * @return mixed
     */
    public function getAge()
    {
        return $this->age;
    }

}