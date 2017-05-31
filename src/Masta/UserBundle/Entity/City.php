<?php

namespace Masta\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * City
 *
 * @ORM\Table(name="user_cities")
 * @ORM\Entity(repositoryClass="Masta\UserBundle\Repository\CityRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class City
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="publishedAt", type="datetime")
     */
    private $publishedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updatedAt", type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=30)
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="nb_users", type="integer")
     */
    private $nbUsers = 0;

    /**
     * @ORM\ManyToOne(targetEntity="Country", inversedBy="cities")
     * @ORM\JoinColumn(nullable=false)
     */
    private $country;

    /**
     * @ORM\OneToMany(
     *      targetEntity="User",
     *      mappedBy="city",
     *      orphanRemoval=true
     * )
     */
    private $users;

    /**
     * @ORM\ManyToOne(targetEntity="Masta\PlateFormeBundle\Entity\Stat\Stat", inversedBy="cities")
     */
    private $stat;

    /**
    * @ORM\Column(type="decimal", scale=3)
    */
    protected $rank = 1;

    public function __construct()
    {
        $this->publishedAt = new \DateTime();
        $this->users = new ArrayCollection();
    }

    //fonctions special
    public function ranking()
    {
        $country_rank = $this->getCountry()->getRank();
        $nb_cities = $this->country->getNbCities();
        $nb_users = $this->getNbUsers();
        $nb_users_in_country = $this->getCountry()->getNbUsers();
        $nb_cities_in_country = $this->getCountry()->getNbCities();


        if($nb_cities>0)
            $city_probability = 1/$nb_cities; 
        else
            $city_probability=0;

        if($nb_users>0)
            $user_probaility = 1/$nb_users;
        else
            $user_probaility=0;

        if($nb_cities>0)
            $city_fc = $nb_cities_in_country/$nb_cities;
        else
            $city_fc=0;

        if($nb_users_in_country>0)
            $user_fc = $nb_users/$nb_users_in_country;
        else
            $user_fc=0;

        $rank = ($user_fc * $city_probability) + ($city_fc * $city_probability)  ;
       
        return $rank;
    }
    //endfonctions

    /**
     * @ORM\PreUpdate
     * Callback pour mettre à jour la date d'édition à chaque modification de l'entité
     */
    public function updateDate()
    {
        $this->setUpdatedAt(new \Datetime());
        $this->setRank($this->ranking());
    }

    /**
     * @ORM\PrePersist
     */
    public function increase()
    {
    
      $compteur = $this->getStat()->getCities()->count();
      $this->getStat()->setNbCities($compteur-1);

      $compteur = $this->getCountry()->getCities()->count();
      $this->getCountry()->setNbCities($compteur+1);

    }
    /**
     * @ORM\PreRemove
     */
    public function decrease()
    {
      $compteur = $this->getStat()->getCities()->count();
      $this->getStat()->setNbCities($compteur-1);

      $compteur = $this->getCountry()->getCities()->count();
      $this->getCountry()->setNbCities($compteur-1);
    }

    /**
     * toString
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }




    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set publishedAt
     *
     * @param \DateTime $publishedAt
     *
     * @return City
     */
    public function setPublishedAt($publishedAt)
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    /**
     * Get publishedAt
     *
     * @return \DateTime
     */
    public function getPublishedAt()
    {
        return $this->publishedAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return City
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return City
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set nbUsers
     *
     * @param integer $nbUsers
     *
     * @return City
     */
    public function setNbUsers($nbUsers)
    {
        $this->nbUsers = $nbUsers;

        return $this;
    }

    /**
     * Get nbUsers
     *
     * @return integer
     */
    public function getNbUsers()
    {
        return $this->nbUsers;
    }

    /**
     * Set rank
     *
     * @param string $rank
     *
     * @return City
     */
    public function setRank($rank)
    {
        $this->rank = $rank;

        return $this;
    }

    /**
     * Get rank
     *
     * @return string
     */
    public function getRank()
    {
        return $this->rank;
    }

    /**
     * Set country
     *
     * @param \Masta\UserBundle\Entity\Country $country
     *
     * @return City
     */
    public function setCountry(\Masta\UserBundle\Entity\Country $country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return \Masta\UserBundle\Entity\Country
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Add user
     *
     * @param \Masta\UserBundle\Entity\User $user
     *
     * @return City
     */
    public function addUser(\Masta\UserBundle\Entity\User $user)
    {
        $this->users[] = $user;

        return $this;
    }

    /**
     * Remove user
     *
     * @param \Masta\UserBundle\Entity\User $user
     */
    public function removeUser(\Masta\UserBundle\Entity\User $user)
    {
        $this->users->removeElement($user);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Set stat
     *
     * @param \Masta\PlateFormeBundle\Entity\Stat\Stat $stat
     *
     * @return City
     */
    public function setStat(\Masta\PlateFormeBundle\Entity\Stat\Stat $stat = null)
    {
        $this->stat = $stat;

        return $this;
    }

    /**
     * Get stat
     *
     * @return \Masta\PlateFormeBundle\Entity\Stat\Stat
     */
    public function getStat()
    {
        return $this->stat;
    }
}
