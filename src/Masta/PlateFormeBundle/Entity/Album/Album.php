<?php

namespace Masta\PlateFormeBundle\Entity\Album;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Album
 *
 * @ORM\Table(name="album_albums")
 * @ORM\Entity(repositoryClass="Masta\PlateFormeBundle\Repository\Album\AlbumRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Album
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
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="publishedAt", type="datetime")
     */
    private $publishedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updatedAt", type="datetime",nullable=true)
     */
    private $updatedAt;

    /**
     * @var integer
     *
     * @ORM\Column(name="nb_products", type="integer",nullable=true)
     */
    private $nbProducts = 0;

    /**
     * @ORM\OneToMany(
     *      targetEntity="Masta\PlateFormeBundle\Entity\Product\Product",
     *      mappedBy="album",
     *      orphanRemoval=true
     * )
     */
    private $products;

    /**
     * @ORM\ManyToOne(targetEntity="Masta\PlateFormeBundle\Entity\Category\Category", inversedBy="albums")
     * @ORM\JoinColumn(nullable = false)
     */
    private $category;

    /**
     * @ORM\ManyToOne(targetEntity="Masta\UserBundle\Entity\User", inversedBy="albums")
     */
    private $author;

    /**
     * @ORM\ManyToOne(targetEntity="Masta\PlateFormeBundle\Entity\Stat\Stat", inversedBy="albums")
     */
    private $stat;

    /**
    * @ORM\Column(type="decimal", scale=3)
    */
    protected $rank = 1;

    //proprieté speciale
    public $isAuthor;

    public function __construct()
    {
        $this->publishedAt = new \DateTime();
        $this->albumFollowers = new ArrayCollection();
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
     * @ORM\prePersist
     */
    public function increase()
    {
      $compteur = $this->getStat()->getAlbums()->count();
      $this->getStat()->setNbAlbums($compteur+1);

      $compteur = $this->getAuthor()->getAlbums()->count();
      $this->getAuthor()->setNbAlbums($compteur+1);

      $compteur = $this->getCategory()->getAlbums()->count();
      $this->getCategory()->setNbAlbums($compteur+1);

    }

    /**
     * @ORM\preRemove
     */
    public function decrease()
    {
       $compteur = $this->getStat()->getAlbums()->count();
       $this->getStat()->setNbAlbums($compteur+1);

        $compteur = $this->getAuthor()->setAlbums()->count();
        $this->getAuthor()->setNbAlbums($compteur-1);

        $compteur = $this->getCategory()->getAlbums()->count();
        $this->getCategory()->setNbAlbums($compteur-1);
    }

    public function ranking()
    {
        $user_rank = $this->getAuthor()->getRank();
        $category_rank = $this->getCategory()->getRank();

        $nb_total_products = $this->getStat()->getNbProducts();
        $nb_total_albums = $this->getStat()->getNbAlbums();
        $nb_products = $this->getNbProducts();
        $nb_total_author_producs = $this->getAuthor()->getNbProducts();

        if($nb_total_albums>0)
             $album_probability = 1/$nb_total_albums;
        else
            $album_probability=0;

        if($nb_total_author_producs>0)
            $product_probability = 1/$nb_total_author_producs;
        else
            $product_probability=0;
        
        if(($nb_total_products>0) && ($nb_total_author_producs>0))
            $product_fc = ($nb_products/$nb_total_products) +($nb_products / $nb_total_author_producs);
        else
            $product_fc = 0;
         
        $rank = ($product_fc*$product_probability)+($product_fc*$album_probability) +($product_fc*$category_rank)+($product_fc*$user_rank);
        return $rank;
    }

    //Verification function
    public function setIsAuthor($isAuthor)
    {
        $this->isAuthor = $isAuthor;
        return $this;
    }

    public function getIsAuthor()
    {
        return $this->isAuthor;
    }

    //endfonction special
   

   

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
     * @return Album
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
     * Set description
     *
     * @param string $description
     *
     * @return Album
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set publishedAt
     *
     * @param \DateTime $publishedAt
     *
     * @return Album
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
     * @return Album
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
     * @return Album
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
     * Set rank
     *
     * @param string $rank
     *
     * @return Album
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
     * Set category
     *
     * @param \Masta\PlateFormeBundle\Entity\Category\Category $category
     *
     * @return Album
     */
    public function setCategory(\Masta\PlateFormeBundle\Entity\Category\Category $category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \Masta\PlateFormeBundle\Entity\Category\Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set author
     *
     * @param \Masta\UserBundle\Entity\User $author
     *
     * @return Album
     */
    public function setAuthor(\Masta\UserBundle\Entity\User $author = null)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return \Masta\UserBundle\Entity\User
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set stat
     *
     * @param \Masta\PlateFormeBundle\Entity\Stat\Stat $stat
     *
     * @return Album
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
     * Add product
     *
     * @param \Masta\PlateFormeBundle\Entity\Product\Product $product
     *
     * @return Album
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
}
