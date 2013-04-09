<?php
/**
 * Created by JetBrains PhpStorm.
 * User: bogdan
 * Date: 4/9/13
 * Time: 12:17 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Btanase\MultiSelectAutocompleteBundle\Form\Type;


use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\AbstractType;

class BTAutocomplete extends AbstractType {
    /**
     * @var ObjectManager
     */
    private $om;


    /**
     * @param ObjectManager $om
     */
    function __construct($om)
    {
        $this->om = $om;
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return "bt_autocomplete";
    }

    public function getParent()
    {
        return 'text';
    }


}