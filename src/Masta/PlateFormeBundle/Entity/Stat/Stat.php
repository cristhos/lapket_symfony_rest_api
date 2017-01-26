<?php

namespace Masta\PlateFormeBundle\Entity\Stat;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Stat
 *
 * @ORM\Table(name="stat_stats")
 * @ORM\Entity(repositoryClass="Masta\PlateFormeBundle\Repository\Stat\StatRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Stat
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
     * @var int
     *
     * @ORM\Column(name="nbCountries", type="integer")
     */
    private $nbCountries=0;

    /**
     * @var int
     *
     * @ORM\Column(name="nbCities", type="integer")
     */
    private $nbCities=0;

    /**
     * @var int
     *
     * @ORM\Column(name="nbCategories", type="integer")
     */
    private $nbCategories=0;

    /**
     * @var int
     *
     * @ORM\Column(name="nbUsers", type="integer")
     */
    private $nbUsers=0;

    /**
     * @var int
     *
     * @ORM\Column(name="nbAlbums", type="integer")
     */
    private $nbAlbums=0;

    /**
     * @var int
     *
     * @ORM\Column(name="nbProducts", type="integer")
     */
    private $nbProducts=0;

    /**
     * @var int
     *
     * @ORM\Column(name="nbConversations", type="integer")
     */
    private $nbConversations=0;

    /**
     * @var int
     *
     * @ORM\Column(name="nbMessages", type="integer")
     */
    private $nbMessages=0;

    /**
     * @ORM\OneToMany(
     *      targetEntity="Masta\UserBundle\Entity\User",
     *      mappedBy="stat",
     *      orphanRemoval=true
     * )
     */
    private $users;

    /**
     * @ORM\OneToMany(
     *      targetEntity="Masta\UserBundle\Entity\Country",
     *      mappedBy="stat",
     *      orphanRemoval=true
     * )
     */
    private $countries;


    /**
     * @ORM\OneToMany(
     *      targetEntity="Masta\UserBundle\Entity\City",
     *      mappedBy="stat",
     *      orphanRemoval=true
     * )
     */
    private $cities;

    /**
     * @ORM\OneToMany(
     *      targetEntity="Masta\PlateFormeBundle\Entity\Category\Category",
     *      mappedBy="stat",
     *      orphanRemoval=true
     * )
     */
    private $categories;

    /**
     * @ORM\OneToMany(
     *      targetEntity="Masta\PlateFormeBundle\Entity\Album\Album",
     *      mappedBy="stat",
     *      orphanRemoval=true
     * )
     */
    private $albums;

        /**
     * @ORM\OneToMany(
     *      targetEntity="Masta\PlateFormeBundle\Entity\Product\Product",
     *      mappedBy="stat",
     *      orphanRemoval=true
     * )
     */
    private $products;
    
    /**
     * Constructor
     */
    public function __construct()
    {
         $this->publishedAt = new \DateTime();
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
        $this->countries = new \Doctrine\Common\Collections\ArrayCollection();
        $this->cities = new \Doctrine\Common\Collections\ArrayCollection();
        $this->categories = new \Doctrine\Common\Collections\ArrayCollection();
        $this->albums = new \Doctrine\Common\Collections\ArrayCollection();
        $this->products = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @ORM\PreUpdate
     * Callback pour mettre à jour la date d'édition à chaque modification de l'entité
     */
    public function updateDate()
    {
        $this->setUpdatedAt(new \Datetime());
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
     * @return Stat
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
     * @return Stat
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
     * Set nbCountries
     *
     * @param integer $nbCountries
     *
     * @return Stat
     */
    public function setNbCountries($nbCountries)
    {
        $this->nbCountries = $nbCountries;

        return $this;
    }

    /**
     * Get nbCountries
     *
     * @return integer
     */
    public function getNbCountries()
    {
        return $this->nbCountries;
    }

    /**
     * Set nbCities
     *
     * @param integer $nbCities
     *
     * @return Stat
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
     * Set nbCategories
     *
     * @param integer $nbCategories
     *
     * @return Stat
     */
    public function setNbCategories($nbCategories)
    {
        $this->nbCategories = $nbCategories;

        return $this;
    }

    /**
     * Get nbCategories
     *
     * @return integer
     */
    public function getNbCategories()
    {
        return $this->nbCategories;
    }

    /**
     * Set nbUsers
     *
     * @param integer $nbUsers
     *
     * @return Stat
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
     * Set nbAlbums
     *
     * @param integer $nbAlbums
     *
     * @return Stat
     */
    public function setNbAlbums($nbAlbums)
    {
        $this->nbAlbums = $nbAlbums;

        return $this;
    }

    /**
     * Get nbAlbums
     *
     * @return integer
     */
    public function getNbAlbums()
    {
        return $this->nbAlbums;
    }

    /**
     * Set nbProducts
     *
     * @param integer $nbProducts
     *
     * @return Stat
     */
    public function setNbProducts($nbProducts)
    {
        $this->nbProducts = $nbProducts;

        return $this;
    }

    /**
     * Get nbProducts
     *
     * @return integer
     */
    public function getNbProducts()
    {
        return $this->nbProducts;
    }

    /**
     * Set nbConversations
     *
     * @param integer $nbConversations
     *
     * @return Stat
     */
    public function setNbConversations($nbConversations)
    {
        $this->nbConversations = $nbConversations;

        return $this;
    }

    /**
     * Get nbConversations
     *
     * @return integer
     */
    public function getNbConversations()
    {
        return $this->nbConversations;
    }

    /**
     * Set nbMessages
     *
     * @param integer $nbMessages
     *
     * @return Stat
     */
    public function setNbMessages($nbMessages)
    {
        $this->nbMessages = $nbMessages;

        return $this;
    }

    /**
     * Get nbMessages
     *
     * @return integer
     */
    public function getNbMessages()
    {
        return $this->nbMessages;
    }

    /**
     * Add user
     *
     * @param \Masta\UserBundle\Entity\User $user
     *
     * @return Stat
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
     * Add country
     *
     * @param \Masta\UserBundle\Entity\Country $country
     *
     * @return Stat
     */
    public function addCountry(\Masta\UserBundle\Entity\Country $country)
    {
        $this->countries[] = $country;

        return $this;
    }

    /**
     * Remove country
     *
     * @param \Masta\UserBundle\Entity\Country $country
     */
    public function removeCountry(\Masta\UserBundle\Entity\Country $country)
    {
        $this->countries->removeElement($country);
    }

    /**
     * Get countries
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCountries()
    {
        return $this->countries;
    }

    /**
     * Add city
     *
     * @param \Masta\UserBundle\Entity\City $city
     *
     * @return Stat
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
     * Add category
     *
     * @param \Masta\PlateFormeBundle\Entity\Category\Category $category
     *
     * @return Stat
     */
    public function addCategory(\Masta\PlateFormeBundle\Entity\Category\Category $category)
    {
        $this->categories[] = $category;

        return $this;
    }

    /**
     * Remove category
     *
     * @param \Masta\PlateFormeBundle\Entity\Category\Category $category
     */
    public function removeCategory(\Masta\PlateFormeBundle\Entity\Category\Category $category)
    {
        $this->categories->removeElement($category);
    }

    /**
     * Get categories
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Add album
     *
     * @param \Masta\PlateFormeBundle\Entity\Album\Album $album
     *
     * @return Stat
     */
    public function addAlbum(\Masta\PlateFormeBundle\Entity\Album\Album $album)
    {
        $this->albums[] = $album;

        return $this;
    }

    /**
     * Remove album
     *
     * @param \Masta\PlateFormeBundle\Entity\Album\Album $album
     */
    public function removeAlbum(\Masta\PlateFormeBundle\Entity\Album\Album $album)
    {
        $this->albums->removeElement($album);
    }

    /**
     * Get albums
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAlbums()
    {
        return $this->albums;
    }

    /**
     * Add product
     *
     * @param \Masta\PlateFormeBundle\Entity\Product\Product $product
     *
     * @return Stat
     */
    public function addProduct(\Masta\PlateFormeBundle\Entity\Product\Product $product)
    {
        $this->products[] = $product;

        return $this;
    }

    /**
     * Remove product
     *
     * @param \Masta\PlateFormeBundle\Entity\Product\Product $product
     */
    public function removeProduct(\Masta\PlateFormeBundle\Entity\Product\Product $product)
    {
        $this->products->removeElement($product);
    }

    /**
     * Get products
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Stat
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
}
