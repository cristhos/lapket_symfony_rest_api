<?php

namespace Masta\PlateFormeBundle\Entity\Notification;

use Doctrine\ORM\Mapping as ORM;

/**
 * Notification
 *
 * @ORM\Table(name="notifications")
 * @ORM\Entity(repositoryClass="Masta\PlateFormeBundle\Repository\Notification\NotificationRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Notification
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
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="object_id", type="integer")
     */
    private $object_id;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_seen", type="boolean")
     */
    private $isSeen;

    /**
     * @ORM\ManyToOne(targetEntity="Masta\UserBundle\Entity\User")
     */
    private $author;

    /**
     * @ORM\ManyToOne(targetEntity="Masta\UserBundle\Entity\User",inversedBy="notifications")
     */
    private $destinator;

    public function __construct()
    {
        $this->publishedAt = new \DateTime();
        $this->isSeen = false;
    }


    /**
     * @ORM\PrePersist
     */
    public function increase()
    {
      $compteur = 0;
      foreach ($this->getDestinator()->getNotifications() as $notification) 
      {
         if($notification->getIsSeen() == false) $compteur ++;
      }
      $this->getDestinator()->setNbNotifications($compteur+1);
    }

    /**
     * @ORM\PreRemove
     */
    public function removing()
    {
       $compteur = 0;
       foreach ($this->getDestinator()->getNotifications() as $notification) 
       {
         if($notification->getIsSeen() == false) $compteur ++;
       }
       if($compteur>0)
           $this->getDestinator()->setNbNotifications($compteur+1);
       else
           $this->getDestinator()->setNbNotifications(0);
    }

    /**
     * @ORM\PostUpdate
     */
    public function decreaseToUpdate()
    {
      
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
     * @return Notification
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
     * Set type
     *
     * @param string $type
     *
     * @return Notification
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set objectId
     *
     * @param integer $objectId
     *
     * @return Notification
     */
    public function setObjectId($objectId)
    {
        $this->object_id = $objectId;

        return $this;
    }

    /**
     * Get objectId
     *
     * @return integer
     */
    public function getObjectId()
    {
        return $this->object_id;
    }

    /**
     * Set isSeen
     *
     * @param boolean $isSeen
     *
     * @return Notification
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
     * @return Notification
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
     * Set destinator
     *
     * @param \Masta\UserBundle\Entity\User $destinator
     *
     * @return Notification
     */
    public function setDestinator(\Masta\UserBundle\Entity\User $destinator = null)
    {
        $this->destinator = $destinator;

        return $this;
    }

    /**
     * Get destinator
     *
     * @return \Masta\UserBundle\Entity\User
     */
    public function getDestinator()
    {
        return $this->destinator;
    }
}
