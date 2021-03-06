<?php

namespace Masta\PlateFormeBundle\Entity\Category;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation\Expose;

/**
 * Category
 *
 * @ORM\Table(name="category_categories")
 * @ORM\Entity(repositoryClass="Masta\PlateFormeBundle\Repository\Category\CategoryRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Category
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=30)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="is_published", type="boolean", nullable=true)
     */
    private $isPublished;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="publishedAt", type="datetime")
     */
    private $publishedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="UpdatedAt", type="datetime",nullable=true)
     */
    private $updatedAt;

    /**
     * @var integer
     *
     * @ORM\Column(name="nb_products", type="integer",nullable=true)
     */
    private $nbProducts=0;

    /**
     * @ORM\OneToMany(
     *      targetEntity="Masta\PlateFormeBundle\Entity\Product\Product",
     *      mappedBy="category",
     *      orphanRemoval=true
     * )
     * @ORM\OrderBy({"publishedAt" = "DESC"})
     */
    private $products;

    /**
     * @var integer
     *
     * @ORM\Column(name="nb_followers", type="integer",nullable=true)
     */
    private $nbFollowers;

    /**
     * @ORM\OneToMany(
     *      targetEntity="CategoryFollower",
     *      mappedBy="category",
     *      orphanRemoval=true
     * )
     * @ORM\OrderBy({"publishedAt" = "DESC"})
     */
    private $categoryFollowers;

    /**
     * @ORM\ManyToOne(targetEntity="Masta\PlateFormeBundle\Entity\Stat\Stat", inversedBy="categories")
     */
    private $stat;

    /**
    * @ORM\OneToOne(targetEntity="Masta\PlateFormeBundle\Entity\Picture\Picture",cascade={"persist", "remove"})
    */
    private $picture;

    /**
    * @ORM\Column(type="decimal", scale=3)
    */
    protected $rank = 1;

    /**
     * @Expose
     */
    protected $isFollow;
    /**
     * @Expose
     */
    protected $isAuthent;

    public function __construct()
    {
        $this->publishedAt = new \DateTime();
        $this->products = new ArrayCollection();
        $this->categoryFollowers = new ArrayCollection();
    }


    //Verification Function
    public function setIsFollow($isFollow)
    {
        $this->isFollow = $isFollow;
        return $this;
    }
    public function getIsFollow()
    {
        return $this->isFollow;
    }
    public function setIsAuthent($isAuthent)
    {
        $this->isAuthent = $isAuthent;
        return $this;
    }
    public function getIsAuthent()
    {
        return $this->isAuthent;
    }

    public function ranking()
    {
        $nb_total_categories = $this->getStat()->getNbCategories();
        $nb_total_products = $this->getStat()->getNbProducts();
        $nb_total_users = $this->getStat()->getNbUsers();
        
        $nb_followers = $this->getNbFollowers();
        $nb_products = $this->getNbProducts();


        if($nb_total_categories>0)
            $category_probability = 1/$nb_total_categories; 
        else
            $category_probability =0;

        if($nb_products>0)
            $product_probability = 1/$nb_total_products;
        else
            $product_probability = 0;

        if($nb_total_products)
            $product_fc = $nb_products/$nb_total_products;
        else
            $product_fc=0;

        if($nb_total_users)
            $follower_fc = $nb_followers/$nb_total_users; 
        else
            $follower_fc=0;
         

        $rank = ($product_fc*$product_probability)+($follower_fc*$category_probability);
        return $rank;
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
      $compteur = $this->getStat()->getCategories()->count();
      $this->getStat()->setNbCategories($compteur+1);
    }
    
    /**
     * @ORM\PreRemove
     */
    public function decrease()
    {
      $compteur = $this->getStat()->getCategories()->count();
      $this->getStat()->setNbCategories($compteur-1);
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
     * Set name
     *
     * @param string $name
     *
     * @return Category
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
     * Set isPublished
     *
     * @param boolean $isPublished
     *
     * @return Category
     */
    public function setIsPublished($isPublished)
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    /**
     * Get isPublished
     *
     * @return boolean
     */
    public function getIsPublished()
    {
        return $this->isPublished;
    }

    /**
     * Set publishedAt
     *
     * @param \DateTime $publishedAt
     *
     * @return Category
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
     * @return Category
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
     * Set nbProducts
     *
     * @param integer $nbProducts
     *
     * @return Category
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
     * Set nbFollowers
     *
     * @param integer $nbFollowers
     *
     * @return Category
     */
    public function setNbFollowers($nbFollowers)
    {
        $this->nbFollowers = $nbFollowers;

        return $this;
    }

    /**
     * Get nbFollowers
     *
     * @return integer
     */
    public function getNbFollowers()
    {
        return $this->nbFollowers;
    }

    /**
     * Set rank
     *
     * @param string $rank
     *
     * @return Category
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
     * Add product
     *
     * @param \Masta\PlateFormeBundle\Entity\Product\Product $product
     *
     * @return Category
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
     * Add categoryFollower
     *
     * @param \Masta\PlateFormeBundle\Entity\Category\CategoryFollower $categoryFollower
     *
     * @return Category
     */
    public function addCategoryFollower(\Masta\PlateFormeBundle\Entity\Category\CategoryFollower $categoryFollower)
    {
        $this->categoryFollowers[] = $categoryFollower;

        return $this;
    }

    /**
     * Remove categoryFollower
     *
     * @param \Masta\PlateFormeBundle\Entity\Category\CategoryFollower $categoryFollower
     */
    public function removeCategoryFollower(\Masta\PlateFormeBundle\Entity\Category\CategoryFollower $categoryFollower)
    {
        $this->categoryFollowers->removeElement($categoryFollower);
    }

    /**
     * Get categoryFollowers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCategoryFollowers()
    {
        return $this->categoryFollowers;
    }

    /**
     * Set stat
     *
     * @param \Masta\PlateFormeBundle\Entity\Stat\Stat $stat
     *
     * @return Category
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
     * Set picture
     *
     * @param \Masta\PlateFormeBundle\Entity\Picture\Picture $picture
     *
     * @return Category
     */
    public function setPicture(\Masta\PlateFormeBundle\Entity\Picture\Picture $picture = null)
    {
        $this->picture = $picture;

        return $this;
    }

    /**
     * Get picture
     *
     * @return \Masta\PlateFormeBundle\Entity\Picture\Picture
     */
    public function getPicture()
    {
        return $this->picture;
    }
}
