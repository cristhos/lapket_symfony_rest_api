<?php

namespace Masta\PlateFormeBundle\Entity\Deal;

use Doctrine\ORM\Mapping as ORM;

/**
 * Deal
 *
 * @ORM\Table(name="dealsMessages")
 * @ORM\Entity(repositoryClass="Masta\PlateFormeBundle\Repository\Deal\DealRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Deal
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
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     */
    private $content;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_seen", type="boolean")
     */
    private $isSeen;

    /**
     * @ORM\ManyToOne(targetEntity="Masta\UserBundle\Entity\User")
     */
    private $author;

    /**
     * @ORM\ManyToOne(targetEntity="Masta\UserBundle\Entity\User")
     */
    private $destinathor;

    /**
     * @ORM\ManyToOne(targetEntity="Masta\PlateFormeBundle\Entity\Product\Product")
     */
    private $product;

    //speciales
    public $isAuthor;


    public function __construct()
    {
        $this->publishedAt = new \DateTime();
        $this->isSeen = false;
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
     * @return Deal
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
     * Set content
     *
     * @param string $content
     *
     * @return Deal
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set isSeen
     *
     * @param boolean $isSeen
     *
     * @return Deal
     */
    public function setIsSeen($isSeen)
    {
        $this->isSeen = $isSeen;

        return $this;
    }

    /**
     * Get isSeen
     *
     * @return boolean
     */
    public function getIsSeen()
    {
        return $this->isSeen;
    }

    /**
     * Set author
     *
     * @param \Masta\UserBundle\Entity\User $author
     *
     * @return Deal
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
     * Set destinathor
     *
     * @param \Masta\UserBundle\Entity\User $destinathor
     *
     * @return Deal
     */
    public function setDestinathor(\Masta\UserBundle\Entity\User $destinathor = null)
    {
        $this->destinathor = $destinathor;

        return $this;
    }

    /**
     * Get destinathor
     *
     * @return \Masta\UserBundle\Entity\User
     */
    public function getDestinathor()
    {
        return $this->destinathor;
    }

    /**
     * Set product
     *
     * @param \Masta\PlateFormeBundle\Entity\Product\Product $product
     *
     * @return Deal
     */
    public function setProduct(\Masta\PlateFormeBundle\Entity\Product\Product $product = null)
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
