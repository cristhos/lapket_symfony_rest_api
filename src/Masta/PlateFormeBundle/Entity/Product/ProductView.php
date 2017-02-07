<?php

namespace Masta\PlateFormeBundle\Entity\Product;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProductVote
 *
 * @ORM\Table(name="product_views")
 * @ORM\Entity(repositoryClass="Masta\PlateFormeBundle\Repository\Product\ProductViewRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class ProductView
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
     * @ORM\ManyToOne(targetEntity="Masta\UserBundle\Entity\User",inversedBy="productViews")
     */
    private $author;

    /**
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="productViews")
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
      $compteur = $this->getProduct()->getProductViews()->count();
      $this->getProduct()->setNbViews($compteur+1);
    }

    /**
     * @ORM\preRemove
     */
    public function decrease()
    {
      $compteur = $this->getProduct()->getProductViews()->count();
      $this->getProduct()->setNbViews($compteur-1);
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
     * @return ProductView
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
     * Set author
     *
     * @param \Masta\UserBundle\Entity\User $author
     *
     * @return ProductView
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
     * @return ProductView
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
