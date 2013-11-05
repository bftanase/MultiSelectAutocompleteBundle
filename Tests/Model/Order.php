<?php
namespace Btanase\MultiSelectAutocompleteBundle\Tests\Model;


/**
 * @author Bogdan Tanase <bftanase@gmail.com>
 */
class Order {

    private $price;
    /**
     * @var Client
     */
    private $client;

    /**
     * @param \Btanase\MultiSelectAutocompleteBundle\Tests\Model\Client $client
     */
    public function setClient($client)
    {
        $this->client = $client;
    }

    /**
     * @return \Btanase\MultiSelectAutocompleteBundle\Tests\Model\Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param mixed $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }
}