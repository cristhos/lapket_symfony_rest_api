<?php

namespace Masta\PlateFormeBundle\Entity\Deal;

use Doctrine\ORM\Mapping as ORM;

/**
 * ConversationReply
 *
 * @ORM\Table(name="deal_messages")
 * @ORM\Entity(repositoryClass="Masta\PlateFormeBundle\Repository\Deal\MessageRepository")
  * @ORM\HasLifecycleCallbacks()
 */
class Message
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
     * @var bool
     *
     * @ORM\Column(name="is_seen", type="boolean")
     */
    private $isSeen;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     */
    private $content;

    /**
     * @ORM\ManyToOne(targetEntity="Masta\UserBundle\Entity\User",inversedBy="sendMessages")
     */
    private $author;

    /**
     * @ORM\ManyToOne(targetEntity="Masta\UserBundle\Entity\User",inversedBy="receivedMessages")
     */
    private $receiver;

    /**
     * @ORM\ManyToOne(targetEntity="Masta\PlateFormeBundle\Entity\Deal\Conversation")
     */
    private $conversation;

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
     * @ORM\prePersist
     */
    public function increase()
    {
      if($this->getAuthor() == $this->getConversation()->getAuthor())
      {
        $compteur = $this->getConversation()
                         ->getProduct()
                         ->getAuthor()
                         ->getNbMessages();
        if($compteur < 0)
        {
          $this->getConversation()
                           ->getProduct()
                           ->getAuthor()
                           ->setNbMessages(0);
        }
        else
        {
          $this->getConversation()
                           ->getProduct()
                           ->getAuthor()
                           ->setNbMessages($compteur + 1);
        }
      }
      else
      {
        $compteur = $this->getConversation()->getAuthor()->getNbMessages();
        if($compteur < 0)
        {
          $this->getConversation()->getAuthor()->setNbMessages(0);
        }else{
          $this->getConversation()->getAuthor()->setNbMessages($compteur+1);
        }
      }


    }

    /**
     * @ORM\preRemove
     */
    public function decreaseToRemove()
    {
      $this->decrement();
    }

    /**
     * @ORM\preUpdate
     */
    public function decreaseToUpdate()
    {
      $this->decrement();
    }
    //common function to decrement
    public function decrement()
    {
      if($this->getAuthor()->getId() != $this->getConversation()->getAuthor()->getId())
      {
        $compteur = $this->getConversation()
                         ->getProduct()
                         ->getAuthor()
                         ->getNbMessages();
        if($compteur <= 0 )
        {
          $this->getConversation()
                           ->getProduct()
                           ->getAuthor()
                           ->setNbMessages(0);
        }else{
          $this->getConversation()
                           ->getProduct()
                           ->getAuthor()
                           ->setNbMessages($compteur - 1);
        }
      }
      else
      {
        $compteur = $this->getConversation()->getAuthor()->getNbMessages();
        if($compteur <= 0)
        {
          $this->getConversation()->getAuthor()->setNbMessages(0);
        }else{
          $this->getConversation()->getAuthor()->setNbMessages($compteur-1);
        }
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
     * @return Message
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
     * @return Message
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
     * Set content
     *
     * @param string $content
     *
     * @return Message
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
     * Set author
     *
     * @param \Masta\UserBundle\Entity\User $author
     *
     * @return Message
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
     * Set conversation
     *
     * @param \Masta\PlateFormeBundle\Entity\Deal\Conversation $conversation
     *
     * @return Message
     */
    public function setConversation(\Masta\PlateFormeBundle\Entity\Deal\Conversation $conversation = null)
    {
        $this->conversation = $conversation;

        return $this;
    }

    /**
     * Get conversation
     *
     * @return \Masta\PlateFormeBundle\Entity\Deal\Conversation
     */
    public function getConversation()
    {
        return $this->conversation;
    }

    /**
     * Set receiver
     *
     * @param \Masta\UserBundle\Entity\User $receiver
     *
     * @return Message
     */
    public function setReceiver(\Masta\UserBundle\Entity\User $receiver = null)
    {
        $this->receiver = $receiver;

        return $this;
    }

    /**
     * Get receiver
     *
     * @return \Masta\UserBundle\Entity\User
     */
    public function getReceiver()
    {
        return $this->receiver;
    }
}