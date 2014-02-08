<?php

namespace Ydle\RoomBundle\Manager;

use Doctrine\ORM\EntityManager;
use Ydle\IhmBundle\Manager\BaseManager;

class RoomManager extends BaseManager
{

    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }
    
    public function retrieve($params = array())
    {
        return $this->getRepository()->retrieve($params);
    }

    public function findAllByName()
    {
        return $this->getRepository()->findAll();
    }

    public function getRepository()
    {
        return $this->em->getRepository('YdleRoomBundle:Room');
    }

}
