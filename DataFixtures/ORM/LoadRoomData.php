<?php

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