<?php

namespace Ydle\RoomBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * RoomTypeRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class RoomTypeRepository extends EntityRepository
{
	/**
	 * Return all types ordered by names
	 * @Return Doctrine_Collection
	 */
    public function findAllOrderedByName()
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT rt FROM YdleRoomBundle:RoomType rt ORDER BY rt.name ASC'
            )
            ->getResult();
    }
}
