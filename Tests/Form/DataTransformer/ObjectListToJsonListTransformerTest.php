<?php
/**
 * User: bogdan
 */
namespace Btanase\MultiSelectAutocompleteBundle\Tests\Form\DataTransformer;
use Btanase\MultiSelectAutocompleteBundle\Entity\User;
use Btanase\MultiSelectAutocompleteBundle\Form\DataTransformer\ObjectListToJsonListTransformer;

class ObjectListToJsonListTransformerTest extends \PHPUnit_Framework_TestCase {

    private $im;
    private $data;

    public function setUp()
    {
        $user = new User();
        $user->setId(1);
        $user->setUsername('user1');

        $user2 = new User();
        $user2->setId(2);
        $user2->setUsername('user2');

        $this->data[] = $user;
        $this->data[] = $user2;


        $this->im = $this->getMock('Btanase\MultiSelectAutocompleteBundle\Persistence\Manager\ItemManagerInterface');

    }

    public function testTransform()
    {

        $transformer = new ObjectListToJsonListTransformer($this->im, 'username');
        $json = $transformer->transform($this->data);

        $this->assertEquals('[{"value":1,"label":"user1"},{"value":2,"label":"user2"}]', $json);
    }

    public function testReverseTransform()
    {
        $this->im->expects($this->once())
            ->method('getListByIds')
            ->with($this->equalTo(array(1,2)))
            ->will($this->returnValue($this->data));


        $input = '[{"value":1,"label":"user1"},{"value":2,"label":"user2"}]';
        $transformer = new ObjectListToJsonListTransformer($this->im, 'username');
        $objects = $transformer->reverseTransform($input);

        $this->assertEquals($this->data, $objects);
    }


}
