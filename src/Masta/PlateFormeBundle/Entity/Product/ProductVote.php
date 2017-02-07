<?php

namespace Masta\PlateFormeBundle\Entity\Product;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProductVote
 *
 * @ORM\Table(name="product_votes")
 * @ORM\Entity(repositoryClass="Masta\PlateFormeBundle\Repository\Product\ProductVoteRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class ProductVote
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
     * @var boolean
     *
     * @ORM\Column(name="is_notify", type="boolean")
     */
    private $isNotify = true;

    /**
     * @ORM\ManyToOne(targetEntity="Masta\UserBundle\Entity\User", inversedBy="productVotes")
     */
    private $author;

    /**
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="votes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $product;


    public function __construct()
    {
        $this->publishedAt = new \DateTime();
    }

    /**
     * @ORM\prePersist
     */
    public function increase()
    {
      //$compteur = $this->getProduct()->getNbVotes();
      $compteur = $this->getProduct()->getVotes()->count();
      if($compteur < 0)
      {
        $initialise = 0;
        $this->getProduct()->setNbVotes($initialise + 1);
      }else{
        $this->getProduct()->setNbVotes($compteur+1);
      }

      //$compteur = $this->getAuthor()->getNbProductVotes();
      $compteur = $this->getAuthor()->getProductVotes()->count();
      if($compteur < 0)
      {
        $initialise = 0;
        $this->getAuthor()->setNbProductVotes($initialise + 1);
      }else{
        $this->getAuthor()->setNbProductVotes($compteur+1);
      }
    }

    /**
     * @ORM\preRemove
     */
    public function decrease()
    {
     //$compteur = $this->getProduct()->getNbVotes();
      $compteur = $this->getProduct()->getVotes()->count();
      if($compteur <= 0 )
      {
        $initialise = 0;
        $this->getProduct()->setNbVotes($initialise);
      }else{
        $this->getProduct()->setNbVotes($compteur-1);
      }

            //$compteur = $this->getAuthor()->getNbProductVotes();
      $compteur = $this->getAuthor()->getProductVotes()->count();
      if($compteur <= 0 )
      {
        $initialise = 0;
        $this->getAuthor()->setNbProductVotes($initialise);
      }else{
        $this->getAuthor()->setNbProductVotes($compteur-1);
      }
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
     * @return ProductVote
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
     * Set isNotify
     *
     * @param boolean $isNotify
     *
     * @return ProductVote
     */
    public function setIsNotify($isNotify)
    {
        $this->isNotify = $isNotify;

        return $this;
    }

    /**
     * Get isNotify
     *
     * @return boolean
     */
    public function getIsNotify()
    {
        return $this->isNotify;
    }

    /**
     * Set author
     *
     * @param \Masta\UserBundle\Entity\User $author
     *
     * @return ProductVote
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
     * Set product
     *
     * @param \Masta\PlateFormeBundle\Entity\Product\Product $product
     *
     * @return ProductVote
     */
    public function setProduct(\Masta\PlateFormeBundle\Entity\Product\Product $product)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * Get product
     *
     * @return \Masta\PlateFormeBundle\Entity\Product\Product
     */
    public function getProduct()
    {
        return $this->product;
    }
}
