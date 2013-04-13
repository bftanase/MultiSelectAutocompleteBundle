<?php
/**
 * Created by JetBrains PhpStorm.
 * User: bogdan
 * Date: 4/13/13
 * Time: 7:21 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Btanase\MultiSelectAutocompleteBundle\Entity;


use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository {

    public function searchUsers($searchString) {
        $qb = $this->createQueryBuilder('u');
        $qb->where($qb->expr()->like('u.username', ':username'));

        $searchString = '%'.$searchString.'%';
        $qb->setParameter('username', $searchString);

        return $qb->getQuery()->execute();
    }
}