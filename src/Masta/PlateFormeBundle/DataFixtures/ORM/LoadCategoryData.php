<?php
namespace Masta\PlateFormeBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Masta\PlateFormeBundle\Entity\Category\Category;

class LoadCategoryData implements FixtureInterface, ContainerAwareInterface
{

   /**
   * @var ContainerInterface
   */
   private $container;

  public function setContainer(ContainerInterface $container= Null)
  {
    $this->container = $container;
  }

  // Dans l'argument de la méthode load, l'objet $manager est l'EntityManager
  public function load(ObjectManager $manager)
  {
    
    // Liste des noms de catégorie à ajouter
    $names = array(
                    'Téléphone simple', 
                    'Smarthphone', 
                    'Ordinateur fixe', 
                    'Ordinateur Portable',
                    'Ecouteur avec fil',
                    'Ecouteur sans file',
                    'Radio',
                    'Cover',
                    'Console de jeu video',
                    'Jeux Video'
                    );
    
    $stat = $this->getStat();

    var_dump($stat);
    foreach($names as $i => $name)
    {
      $new_category = new Category();
      $new_category->setName($name);
      $new_category->setIsPublished(true);
      $new_category->setStat($stat);

      $manager->persist($new_category);
      $manager->flush();
    }
   
  }

  public function getStat()
  {
    $em = $this->container->get('doctrine.orm.entity_manager');
    $stat = $em->getRepository('MastaPlateFormeBundle:Category\Category')->findOneByName('statistique');
    return $stat;
  }

}
