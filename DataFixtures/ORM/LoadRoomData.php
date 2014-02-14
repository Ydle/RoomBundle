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
namespace Ydle\RoomBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Ydle\RoomBundle\Entity\Room;
use Ydle\RoomBundle\Entity\RoomType;

class LoadRoomData implements FixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $typeBedroom = new RoomType();
        $typeBedroom->setName('Chambre');
        $typeBedroom->setDescription('Les chambres à coucher');
        $typeBedroom->setIsActive(true);
        
        $typeLivingRoom = new RoomType();
        $typeLivingRoom->setName('Salon');
        $typeLivingRoom->setDescription('Salon ou pièce à vivre');
        $typeLivingRoom->setIsActive(true);
        
        $bedroom = new Room();
        $bedroom->setName('Ma chambre');
        $bedroom->setDescription('Chambre de test');
        $bedroom->setType($typeBedroom);
        $bedroom->setIsActive(true);

        $manager->persist($typeBedroom);
        $manager->persist($typeLivingRoom);
        $manager->persist($bedroom);
        $manager->flush();
    }
}