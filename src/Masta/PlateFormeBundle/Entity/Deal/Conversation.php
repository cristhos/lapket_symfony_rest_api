<?php

namespace Masta\PlateFormeBundle\Entity\Deal;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * DealConversation
 *
 * @ORM\Table(name="deal_conversations")
 * @ORM\Entity(repositoryClass="Masta\PlateFormeBundle\Repository\Deal\ConversationRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Conversation
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
     * @ORM\Column(name="updatedAt", type="datetime")
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity="Masta\PlateFormeBundle\Entity\Product\Product", inversedBy="productConversations")
     */
    private $product;

    /**
     * @ORM\ManyToOne(targetEntity="Masta\UserBundle\Entity\User", inversedBy="conversations")
     */
    private $author;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_seen", type="boolean")
     */
    private $isSeen;

    /**
     * @var integer
     *
     * @ORM\Column(name="nb_receiver_messages", type="integer",nullable=true)
     */
    private $nbReceiverMessages=0;

    /**
     * @var integer
     *
     * @ORM\Column(name="nb_author_messages", type="integer",nullable=true)
     */
    private $nbAuthorMessages=0;
    

        /**
     * @ORM\OneToMany(
     *      targetEntity="Masta\PlateFormeBundle\Entity\Deal\Message",
     *      mappedBy="conversation",
     *      orphanRemoval=true
     * )
     * @ORM\OrderBy({"publishedAt" = "DESC"})
     */
    private $messages;

    //speciales
    public $isAuthor;


    public function __construct()
    {
        $this->publishedAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->isSeen = false;
    }


    /**
     * @ORM\prePersist
     */
    public function increase()
    {
      $compteur = $this->getProduct()->getNbConversations();
      if($compteur < 0)
      {
        $initialise = 0;
        $this->getProduct()->setNbConversations($initialise + 1);
      }else{
        $this->getProduct()->setNbConversations($compteur+1);
      }
    }

    /**
     * @ORM\preRemove
     */
    public function decrease()
    {
      $compteur = $this->getProduct()->getNbConversations();
      if($compteur <= 0 )
      {
        $initialise = 0;
        $this->getProduct()->setNbConversations($initialise);
      }else{
        $this->getProduct()->setNbConversations($compteur-1);
      }
    }

    /**
     * @ORM\PreUpdate
     * Callback pour mettre à jour la date d'édition à chaque modification de l'entité
     */
    public function updateDate()
    {
        $this->setUpdatedAt(new \Datetime());
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
     * @return Conversation
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
     * Set isSeen
     *
     * @param boolean $isSeen
     *
     * @return Conversation
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
     * Set nbReceiverMessages
     *
     * @param integer $nbReceiverMessages
     *
     * @return Conversation
     */
    public function setNbReceiverMessages($nbReceiverMessages)
    {
        $this->nbReceiverMessages = $nbReceiverMessages;

        return $this;
    }

    /**
     * Get nbReceiverMessages
     *
     * @return integer
     */
    public function getNbReceiverMessages()
    {
        return $this->nbReceiverMessages;
    }

    /**
     * Set nbAuthorMessages
     *
     * @param integer $nbAuthorMessages
     *
     * @return Conversation
     */
    public function setNbAuthorMessages($nbAuthorMessages)
    {
        $this->nbAuthorMessages = $nbAuthorMessages;

        return $this;
    }

    /**
     * Get nbAuthorMessages
     *
     * @return integer
     */
    public function getNbAuthorMessages()
    {
        return $this->nbAuthorMessages;
    }

    /**
     * Set product
     *
     * @param \Masta\PlateFormeBundle\Entity\Product\Product $product
     *
     * @return Conversation
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

    /**
     * Set author
     *
     * @param \Masta\UserBundle\Entity\User $author
     *
     * @return Conversation
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
     * Add message
     *
     * @param \Masta\PlateFormeBundle\Entity\Deal\Message $message
     *
     * @return Conversation
     */
    public function addMessage(\Masta\PlateFormeBundle\Entity\Deal\Message $message)
    {
        $this->messages[] = $message;

        return $this;
    }

    /**
     * Remove message
     *
     * @param \Masta\PlateFormeBundle\Entity\Deal\Message $message
     */
    public function removeMessage(\Masta\PlateFormeBundle\Entity\Deal\Message $message)
    {
        $this->messages->removeElement($message);
    }

    /**
     * Get messages
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Conversation
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
}
