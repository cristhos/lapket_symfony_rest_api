<?php

namespace Masta\PlateFormeBundle\Entity\Category;

use Doctrine\ORM\Mapping as ORM;

/**
 * CategoryFollower
 *
 * @ORM\Table(name="category_followers")
 * @ORM\Entity(repositoryClass="Masta\PlateFormeBundle\Repository\Category\CategoryFollowerRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class CategoryFollower
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
    * @ORM\Column(type="decimal", scale=2)
    */
    private $rank=100;

    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="categoryFollowers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    /**
     * @ORM\ManyToOne(targetEntity="Masta\UserBundle\Entity\User", inversedBy="categoryFollows")
     */
    private $author;

    public function __construct()
    {
        $this->publishedAt = new \DateTime();
    }

    /**
     * toString
     * @return string
     */
    public function __toString()
    {
        return $this->getAuthor()->getUsername();
    }


    /**
     * @ORM\PreUpdate
     * Callback pour mettre à jour la date d'édition à chaque modification de l'entité
     */
    public function updateDate()
    {
        //$this->ranking();
        $this->setUpdatedAt(new \Datetime());
    }

    /**
     * @ORM\PrePersist
     */
    public function increase()
    {
      //$this->ranking();
      //$compteur = $this->getCategory()->getNbFollowers();
      $compteur = $this->getCategory()->getCategoryFollowers()->count();
      if($compteur < 0)
      {
        $initialise = 0;
        $this->getCategory()->setNbFollowers($initialise + 1);
      }else{
        $this->getCategory()->setNbFollowers($compteur+1);
      }

      //$compteur = $this->getAuthor()->getNbCategoryFollows();
       $compteur = $this->getAuthor()->getCategoryFollows()->count();
      if($compteur < 0)
      {
        $initialise = 0;
        $this->getAuthor()->setNbCategoryFollows($initialise + 1);
      }else{
        $this->getAuthor()->setNbCategoryFollows($compteur+1);
      }
    }

    /**
     * @ORM\PreRemove
     */
    public function decrease()
    {
      //$compteur = $this->getCategory()->getNbFollowers();
      $compteur = $this->getCategory()->getCategoryFollowers()->count();
      if($compteur <= 0 )
      {
        $initialise = 0;
        $this->getCategory()->setNbFollowers($initialise);
      }else{
        $this->getCategory()->setNbFollowers($compteur-1);
      }

      //$compteur = $this->getAuthor()->getNbCategoryFollows();
       $compteur = $this->getAuthor()->getCategoryFollows()->count();
      if($compteur <= 0 )
      {
        $initialise = 0;
        $this->getAuthor()->setNbCategoryFollows($initialise);
      }else{
        $this->getAuthor()->setNbCategoryFollows($compteur-1);
      }
    }
     
    public function ranking()
    {

        $nbTotalProducVotes =  $this->getAuthor()->getProductVotes()->count();
        $nbProductCategoryVotes = 0;

        foreach ($this->getAuthor()->getProductVotes() as $pv) 
        {
            //compter le nombre vote de produit dans une category
           if($pv->getProduct()->getAlbum()->getCategory()->getId() == $this->getCategory()->getId())
           {
               $nbProductCategoryVotes ++; 
           }    
        }

        $rank = ($nbProductCategoryVotes * 100)/ $nbTotalProducVotes;

        $this->setRank($rank);
         
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
     * @return CategoryFollower
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
     * @return CategoryFollower
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
     * Set rank
     *
     * @param float $rank
     *
     * @return CategoryFollower
     */
    public function setRank($rank)
    {
        $this->rank = $rank;

        return $this;
    }

    /**
     * Get rank
     *
     * @return float
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
     * @return CategoryFollower
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
     * @return CategoryFollower
     */
    public function setAuthor(\Masta\UserBundle\Entity\User $author)
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
}
