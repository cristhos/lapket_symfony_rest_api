<?php
namespace Masta\PlateFormeBundle\Entity\Product;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Product
 *
 * @ORM\Table(name="products")
 * @ORM\Entity(repositoryClass="Masta\PlateFormeBundle\Repository\Product\ProductRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Product
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
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
    * @ORM\Column(type="decimal", scale=2)
    */
    protected $price;

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
     * @var boolean
     *
     * @ORM\Column(name="is_Expired", type="boolean")
     */
    private $isExpired = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_blocked", type="boolean")
     */
    private $isBlocked = false;


    /**
     * @var integer
     *
     * @ORM\Column(name="nb_votes", type="integer")
     */
    private $nbVotes = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="nb_Followers", type="integer")
     */
    private $nbFollowers = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="nb_views", type="integer")
     */
    private $nbViews = 0;

    /**
    * @ORM\OneToOne(targetEntity="Masta\PlateFormeBundle\Entity\Picture\Picture",cascade={"persist", "remove"})
    */
    private $picture;


    /**
     * @ORM\OneToMany(
     *      targetEntity="ProductVote",
     *      mappedBy="product",
     *      orphanRemoval=true,
     * )
     * @ORM\OrderBy({"publishedAt" = "DESC"})
     */
    private $votes;



    /**
     * @ORM\OneToMany(
     *      targetEntity="ProductView",
     *      mappedBy="product",
     *      orphanRemoval=true
     * )
     * @ORM\OrderBy({"publishedAt" = "DESC"})
     */
    private $productViews;


    /**
     * @ORM\ManyToOne(targetEntity="Masta\PlateFormeBundle\Entity\Category\Category", inversedBy="products")
     */
    private $category;

    /**
     * @ORM\ManyToOne(targetEntity="Masta\UserBundle\Entity\User", inversedBy="products")
     */
    protected $author;

    /**
     * @ORM\ManyToOne(targetEntity="Masta\PlateFormeBundle\Entity\Stat\Stat", inversedBy="products")
     */
    private $stat;

    /**
    * @ORM\Column(type="decimal", scale=3)
    */
    protected $rank = 1;

    protected $isAuthor;
    protected $isVoted;
    protected $isFollow;
    protected $isNotify;

    public function __construct()
    {
        $this->publishedAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->votes = new ArrayCollection();
        $this->productViews = new ArrayCollection();
        $this->isBlocked = false;
        //$this->rank = $this->ranking();
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
      $compteur = $this->getStat()->getProducts()->count();
      $this->getStat()->setNbProducts($compteur+1);

      $compteur = $this->getAuthor()->getProducts()->count();
      $this->getAuthor()->setNbProducts($compteur+1);
      
      $compteur = $this->getCategory()->getProducts()->count();
      $this->getCategory()->setNbProducts($compteur+1);
    }

    /**
     * @ORM\PreRemove
     */
    public function decrease()
    {
      $compteur = $this->getStat()->getProducts()->count();
      $this->getStat()->setNbProducts($compteur-1);

      $compteur = $this->getAuthor()->getProducts()->count();
      $this->getAuthor()->setNbProducts($compteur-1);
      
      $compteur = $this->getCategory()->getProducts()->count();
      $this->getCategory()->setNbProducts($compteur-1);
     
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
  
     public function setIsVoted($isVoted)
     {
         $this->isVoted = $isVoted;
         return $this;
     }
     public function getIsVoted()
     {
         return $this->isVoted;
     }
 
     public function setIsFollow($isFollow)
     {
         $this->isFollow = $isFollow;
         return $this;
     }
     public function getIsFollow()
     {
         return $this->isFollow;
     }
     public function setIsNotify($isNotify)
     {
         $this->isNotify = $isNotify;
         return $this;
     }
     public function getIsNotify()
     {
         return $this->isNotify;
     }
 
     public function ranking()
     {
         $date_time = date("d M Y H:i:s");
         $date_timestamp = strtotime($date_time);
         $date_published_at = $this->getPublishedAt();
         $product_published_at_timestamp = $this->getPublishedAt()->getTimestamp();         
         $product_published_at_rank = $product_published_at_timestamp/$date_timestamp;

         $category_rank = $this->getCategory()->getRank();
         $user_rank = $this->getAuthor()->getRank();

         $nb_total_products = $this->getStat()->getNbProducts();
         $nb_product_views = $this->getNbViews();
         $nb_product_votes = $this->getNbVotes();
         
         if($nb_product_views > 0)
         {
            $vote_probability = 1/$nb_product_views;
            $vote_fc= $nb_product_votes/$nb_product_views;
         }
         else
         {
            $vote_probability=0;
            $vote_fc = 0;

         }

         if($nb_total_products > 0)
             $product_probability = 1/$nb_total_products;
         else
            $product_probability=0;
         
        
        $rank = $category_rank * (($product_published_at_rank * $vote_probability) + ($vote_fc*$vote_probability)+($vote_fc*$category_rank) );
              
        return $rank;
     }

         /**
     * toString
     * @return string
     */
    public function __toString()
    {
        return $this->getDescription();
    }

     //end function special

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
     * Set description
     *
     * @param string $description
     *
     * @return Product
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
     * Set price
     *
     * @param string $price
     *
     * @return Product
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set publishedAt
     *
     * @param \DateTime $publishedAt
     *
     * @return Product
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
     * @return Product
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
     * Set isExpired
     *
     * @param boolean $isExpired
     *
     * @return Product
     */
    public function setIsExpired($isExpired)
    {
        $this->isExpired = $isExpired;

        return $this;
    }

    /**
     * Get isExpired
     *
     * @return boolean
     */
    public function getIsExpired()
    {
        return $this->isExpired;
    }

    /**
     * Set isBlocked
     *
     * @param boolean $isBlocked
     *
     * @return Product
     */
    public function setIsBlocked($isBlocked)
    {
        $this->isBlocked = $isBlocked;

        return $this;
    }

    /**
     * Get isBlocked
     *
     * @return boolean
     */
    public function getIsBlocked()
    {
        return $this->isBlocked;
    }

    /**
     * Set nbVotes
     *
     * @param integer $nbVotes
     *
     * @return Product
     */
    public function setNbVotes($nbVotes)
    {
        $this->nbVotes = $nbVotes;

        return $this;
    }

    /**
     * Get nbVotes
     *
     * @return integer
     */
    public function getNbVotes()
    {
        return $this->nbVotes;
    }

    /**
     * Set nbFollowers
     *
     * @param integer $nbFollowers
     *
     * @return Product
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
     * Set nbViews
     *
     * @param integer $nbViews
     *
     * @return Product
     */
    public function setNbViews($nbViews)
    {
        $this->nbViews = $nbViews;

        return $this;
    }

    /**
     * Get nbViews
     *
     * @return integer
     */
    public function getNbViews()
    {
        return $this->nbViews;
    }

    /**
     * Set rank
     *
     * @param string $rank
     *
     * @return Product
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
     * Set picture
     *
     * @param \Masta\PlateFormeBundle\Entity\Picture\Picture $picture
     *
     * @return Product
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

    /**
     * Add vote
     *
     * @param \Masta\PlateFormeBundle\Entity\Product\ProductVote $vote
     *
     * @return Product
     */
    public function addVote(\Masta\PlateFormeBundle\Entity\Product\ProductVote $vote)
    {
        $this->votes[] = $vote;

        return $this;
    }

    /**
     * Remove vote
     *
     * @param \Masta\PlateFormeBundle\Entity\Product\ProductVote $vote
     */
    public function removeVote(\Masta\PlateFormeBundle\Entity\Product\ProductVote $vote)
    {
        $this->votes->removeElement($vote);
    }

    /**
     * Get votes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVotes()
    {
        return $this->votes;
    }


    /**
     * Add productView
     *
     * @param \Masta\PlateFormeBundle\Entity\Product\ProductView $productView
     *
     * @return Product
     */
    public function addProductView(\Masta\PlateFormeBundle\Entity\Product\ProductView $productView)
    {
        $this->productViews[] = $productView;

        return $this;
    }

    /**
     * Remove productView
     *
     * @param \Masta\PlateFormeBundle\Entity\Product\ProductView $productView
     */
    public function removeProductView(\Masta\PlateFormeBundle\Entity\Product\ProductView $productView)
    {
        $this->productViews->removeElement($productView);
    }

    /**
     * Get productViews
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProductViews()
    {
        return $this->productViews;
    }

    /**
     * Set category
     *
     * @param \Masta\PlateFormeBundle\Entity\Category\Category $category
     *
     * @return Product
     */
    public function setCategory(\Masta\PlateFormeBundle\Entity\Category\Category $category = null)
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
     * @return Product
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
     * @return Product
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
