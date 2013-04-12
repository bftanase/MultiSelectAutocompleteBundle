<?php
/**
 * User: bogdan
 * 
 */

namespace Btanase\MultiSelectAutocompleteBundle\Persistence\Manager;


interface ItemManagerInterface {

    /**
     * The implementation should return a list of objects based on the passed $ids
     * @param $ids array - object ids
     * @return array Object
     */
    public function getListByIds($ids);
}