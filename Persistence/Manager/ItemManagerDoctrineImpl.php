<?php
/**
 * User: bogdan
 * 
 */

namespace Btanase\MultiSelectAutocompleteBundle\Persistence\Manager;

use Symfony\Component\DependencyInjection\ContainerAware;

class ItemManagerDoctrineImpl extends ContainerAware implements ItemManagerInterface {

    private $entityName;
    private $em;

    function __construct($entityName, $em)
    {
        $this->entityName = $entityName;
        $this->em = $em;
    }

    /**
     * The implementation should return a list of objects based on the passed $ids
     * @param $ids array - object ids
     * @return array Object
     */
    public function getListByIds($ids)
    {

        $qb = $this->em->createQueryBuilder();

        $qb->select('o')
            ->from($this->entityName, 'o')
            ->where($qb->expr()->in('o.id', $ids));

        return $qb->getQuery()->getResult();

    }
}