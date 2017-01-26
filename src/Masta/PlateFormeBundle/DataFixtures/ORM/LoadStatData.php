<?php
namespace Masta\PlateFormeBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Masta\PlateFormeBundle\Entity\Stat\Stat;

class LoadStatData implements FixtureInterface
{
  // Dans l'argument de la méthode load, l'objet $manager est l'EntityManager
  public function load(ObjectManager $manager)
  {
      $stat = new Stat();
      $stat->setName('statistique');
      $manager->persist($stat);
      $manager->flush();
  }
}
