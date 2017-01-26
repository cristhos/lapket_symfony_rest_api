<?php
namespace Masta\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Masta\UserBundle\Entity\Country;

class LoadCountryAndCityData implements FixtureInterface
{
  public function load(ObjectManager $manager,EntityManagerInterface $em)
  {
    $stat = $manager->getRepository('MastaPlateFormeBundle:Stat\Stat')->find(10);
    $coutries = array([
                       'pays_name' => 'Congo-Kinshasa',
                       'indicative' => '+243',
                       'cities'=> array('Baraka','Bandundu','Bikoro','Boende','Bukavu','Bamba','Bunia','Buta','Butembo','Beni',
                                       'Boma','Gbadolite','Goma','kalemi','Kananga','Kikwit','Kindu','kisangani','Kinshasa','Kolwezi',
                                       'Likasi','Lubumbashi','Matadi','Mbandaka','Mbuji-Mayi','Mwene-Ditu','Tshikapa','Zongo')
                     ],
                     );



    foreach($coutries as $country)
    {
      $new_country = new Country();
      $new_country->setName($country['pays_name']);
      $new_country->setIndicative($country['indicative']);
      $countries->setStat($stat);
      $manager->persist($new_country);
      $manager->flush();
      foreach($coutrie['cities'] as $v => $city)
      {
        $new_city = new Country();
        $new_city->setCountry($new_country);
        $new_city->setName($city[$v]);
        $new_city->setStat($stat);
        $manager->persist($new_city);
        $manager->flush();
      }
    }
   
  }
}