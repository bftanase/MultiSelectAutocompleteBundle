<?php
/**
 * User: bogdan
 */
namespace Btanase\MultiSelectAutocompleteBundle\Tests\Form\DataTransformer;
use Btanase\MultiSelectAutocompleteBundle\Entity\User;
use Btanase\MultiSelectAutocompleteBundle\Form\DataTransformer\ObjectListToJsonListTransformer;

class ObjectListToJsonListTransformerTest extends \PHPUnit_Framework_TestCase {

    private $om;

    public function setUp()
    {
        $this->om = $this->getMock('Doctrine\Common\Persistence\ObjectManager');

    }

    public function testTransform()
    {
        $user = new User();
        $user->setId(1);
        $user->setUsername('user1');

        $user2 = new User();
        $user2->setId(2);
        $user2->setUsername('user2');

        $data[] = $user;
        $data[] = $user2;

        $transformer = new ObjectListToJsonListTransformer($this->om, 'BtanaseMultiSelectAutocompleteBundle:User', 'username');
        $json = $transformer->transform($data);

        $this->assertEquals('[{"value":1,"label":"user1"},{"value":2,"label":"user2"}]', $json);
    }
}
