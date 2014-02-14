<?php
/*
  This file is part of Ydle.

    Ydle is free software: you can redistribute it and/or modify
    it under the terms of the GNU  Lesser General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    Ydle is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU  Lesser General Public License for more details.

    You should have received a copy of the GNU Lesser General Public License
    along with Ydle.  If not, see <http://www.gnu.org/licenses/>.
 */
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
