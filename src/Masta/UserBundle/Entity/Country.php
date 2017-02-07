<?php

namespace Masta\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Country
 *
 * @ORM\Table(name="user_countries")
 * @ORM\Entity(repositoryClass="Masta\UserBundle\Repository\CountryRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Country
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
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="indicative", type="string", length=6, unique=true)
     */
    private $indicative;

    /**
     * @var integer
     *
     * @ORM\Column(name="nb_cities", type="integer")
     */
    private $nbCities = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="nb_users", type="integer")
     */
    private $nbUsers = 0;

    /**
     * @ORM\OneToMany(
     *      targetEntity="City",
     *      mappedBy="country",
     *      orphanRemoval=true
     * )
     * @ORM\OrderBy({"publishedAt" = "DESC"})
     */
    private $cities;


    /**
     * @ORM\OneToMany(
     *      targetEntity="User",
     *      mappedBy="country",
     *      orphanRemoval=true
     * )
     */
    private $users;

    /**
     * @ORM\ManyToOne(targetEntity="Masta\PlateFormeBundle\Entity\Stat\Stat", inversedBy="countries")
     */
    private $stat;

    /**
    * @ORM\Column(type="decimal", scale=3)
    */
    protected $rank = 1;

    public function __construct()
    {
        $this->publishedAt = new \DateTime();
        $this->cities = new ArrayCollection();
    }

    //fonctions special

    /**
     * toString
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }

    public function ranking()
    {
        $nb_total_cities = $this->getStat()->getNbCities();
        $nb_total_users = $this->getStat()->getNbUsers();
        $nb_total_countries = $this->getStat()->getNbCountries();

        $nb_cities = $this->getNbCities();
        $nb_users = $this->getNbUsers();

        if($nb_cities>0)
            $city_probability = 1/$nb_cities;
        else
            $city_probability=0;

        if($nb_total_countries>0)
            $country_probability = 1/$nb_total_countries;
        else
            $country_probability=0;

        if($nb_total_cities>0)
           $city_fc = $nb_cities/$nb_total_cities;
        else
          $city_fc=0;

        if($nb_total_users>0)
            $user_fc = $nb_users/$nb_total_users;
        else
            $user_fc=0;
        
        $rank = ($city_fc * $city_probability) + ($user_fc * $country_probability);
        
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
      $compteur = $this->getStat()->getCountries()->count();
      $this->getStat()->setNbCountries($compteur+1);
    }
    /**
     * @ORM\PreRemove
     */
    public function decrease()
    {
      $compteur = $this->getStat()->getCountries()->count();
      $this->getStat()->setNbCountries($compteur-1);
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
     * @return Country
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
     * @return Country
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
     * @return Country
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
     * Set indicative
     *
     * @param string $indicative
     *
     * @return Country
     */
    public function setIndicative($indicative)
    {
        $this->indicative = $indicative;

        return $this;
    }

    /**
     * Get indicative
     *
     * @return string
     */
    public function getIndicative()
    {
        return $this->indicative;
    }

    /**
     * Set nbCities
     *
     * @param integer $nbCities
     *
     * @return Country
     */
    public function setNbCities($nbCities)
    {
        $this->nbCities = $nbCities;

        return $this;
    }

    /**
     * Get nbCities
     *
     * @return integer
     */
    public function getNbCities()
    {
        return $this->nbCities;
    }

    /**
     * Set nbUsers
     *
     * @param integer $nbUsers
     *
     * @return Country
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
     * Add city
     *
     * @param \Masta\UserBundle\Entity\City $city
     *
     * @return Country
     */
    public function addCity(\Masta\UserBundle\Entity\City $city)
    {
        $this->cities[] = $city;

        return $this;
    }

    /**
     * Remove city
     *
     * @param \Masta\UserBundle\Entity\City $city
     */
    public function removeCity(\Masta\UserBundle\Entity\City $city)
    {
        $this->cities->removeElement($city);
    }

    /**
     * Get cities
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCities()
    {
        return $this->cities;
    }

    /**
     * Add user
     *
     * @param \Masta\UserBundle\Entity\User $user
     *
     * @return Country
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
     * @return Country
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

    /**
     * Set rank
     *
     * @param string $rank
     *
     * @return Country
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
}
