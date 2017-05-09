<?php

namespace Masta\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Follower
 *
 * @ORM\Table(name="followers")
 * @ORM\Entity(repositoryClass="Masta\UserBundle\Repository\FollowerRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Follower
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
     * @var boolean
     *
     * @ORM\Column(name="is_request", type="boolean")
     */
    private $isRequest;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_blocked", type="boolean")
     */
    private $isBlocked;

    /**
    * @ORM\Column(type="decimal", scale=2)
    */
    private $rank=100;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="followers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="follows")
     * @ORM\JoinColumn(nullable=false)
     */
    private $userFollowed;

    public function __construct()
    {
        $this->publishedAt = new \DateTime();
        $this->isRequest = false;
        $this->isBlocked = false;
        $this->followStat = 1;
    }

    //fonctions special
    public function ranking()
    {
        $rank = 100;
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
      $nb_follows =  $this->getAuthor()->getFollows()->count();
      $nb_followers = $this->getUserFollowed()->getFollowers()->count();
      
      $this->getAuthor()->setNbFollows($nb_follows+1);
      $this->getUserFollowed()->setNbFollowers($nb_followers+1);
    }

    /**
     * @ORM\PreRemove
     */
    public function decrease()
    {
      $nb_follows = $this->getAuthor()->getFollows()->count();
      $nb_followers = $this->getUserFollowed()->getFollowers()->count();
      
      $this->getAuthor()->setNbFollows($nb_follows-1);
      $this->getUserFollowed()->setNbFollowers($nb_followers-1);
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
     * @return Follower
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
     * @return Follower
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
     * Set isRequest
     *
     * @param boolean $isRequest
     *
     * @return Follower
     */
    public function setIsRequest($isRequest)
    {
        $this->isRequest = $isRequest;

        return $this;
    }

    /**
     * Get isRequest
     *
     * @return boolean
     */
    public function getIsRequest()
    {
        return $this->isRequest;
    }

    /**
     * Set isBlocked
     *
     * @param boolean $isBlocked
     *
     * @return Follower
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
     * Set rank
     *
     * @param string $rank
     *
     * @return Follower
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
     * Set author
     *
     * @param \Masta\UserBundle\Entity\User $author
     *
     * @return Follower
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

    /**
     * Set userFollowed
     *
     * @param \Masta\UserBundle\Entity\User $userFollowed
     *
     * @return Follower
     */
    public function setUserFollowed(\Masta\UserBundle\Entity\User $userFollowed)
    {
        $this->userFollowed = $userFollowed;

        return $this;
    }

    /**
     * Get userFollowed
     *
     * @return \Masta\UserBundle\Entity\User
     */
    public function getUserFollowed()
    {
        return $this->userFollowed;
    }
}
