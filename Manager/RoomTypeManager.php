<?php

namespace Ydle\RoomBundle\Manager;

use Doctrine\ORM\EntityManager;
use Ydle\IhmBundle\Manager\BaseManager;

class RoomTypeManager extends BaseManager
{

    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function findAllByName()
    {
        return $this->getRepository()->findAllOrderedByName();
    }

    public function getRepository()
    {
        return $this->em->getRepository('YdleRoomBundle:RoomType');
    }

}
