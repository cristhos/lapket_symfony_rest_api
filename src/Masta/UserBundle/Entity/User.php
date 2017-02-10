<?php
namespace Masta\UserBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * User
 *
 * @ORM\Table(name="user_users")
 * @ORM\Entity(repositoryClass="Masta\UserBundle\Repository\UserRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /** @ORM\Column(name="facebook_id", type="string", length=255, nullable=true) */
    protected $facebook_id;

    /** @ORM\Column(name="facebook_access_token", type="string", length=255, nullable=true) */
    protected $facebook_access_token;

    /** @ORM\Column(name="google_id", type="string", length=255, nullable=true) */
    protected $google_id;

    /** @ORM\Column(name="google_access_token", type="string", length=255, nullable=true) */
    protected $google_access_token;

    /**
     * @var string
     *
     * @ORM\Column(name="full_name", type="string", length=20, nullable=true)
     */
    private $fullName;

    /**
     * @var string
     *
     * @ORM\Column(name="gender", type="string", length=6, nullable=true)
     */
    private $gender;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="birthday", type="datetime", nullable=true)
     */
    private $birthday;

    /**
     * @var string
     *
     * @ORM\Column(name="language", type="string", length=4, nullable=true)
     */
    private $language;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=100, nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="phone_number", type="string", length=15, nullable=true)
     */
    private $phoneNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="web_site", type="string", length=125, nullable=true)
     */
    private $webSite;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_private", type="boolean")
     */
    private $isPrivate = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_mail_notify", type="boolean")
     */
    private $isMailNotify = true;

    /**
     * @var integer
     *
     * @ORM\Column(name="nb_followers", type="integer")
     */
    private $nbFollowers = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="nb_follows", type="integer")
     */
    private $nbFollows = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="nb_products", type="integer")
     */
    private $nbProducts = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="nb_product_votes", type="integer")
     */
    private $nbProductVotes = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="nb_product_views", type="integer")
     */
    private $nbProductViews = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="nb_category_follows", type="integer")
     */
    private $nbCategoryFollows = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="nb_notifications", type="integer")
     */
    private $nbNotifications = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="nb_send_messages", type="integer")
     */
    private $nbSendMessages = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="nb_received_messages", type="integer")
     */
    private $nbReceivedMessages = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="nb_conversations", type="integer")
     */
    private $nbConversations = 0;

    /**
     * @ORM\ManyToOne(targetEntity="Country", inversedBy="users")
     */
    private $country;

    /**
     * @ORM\ManyToOne(targetEntity="City", inversedBy="users")
     */
    private $city;

    /**
     * @ORM\OneToOne(targetEntity="Masta\PlateFormeBundle\Entity\Picture\Picture",cascade={"persist", "remove"})
     */
    private $profilePicture;

    /**
     * @ORM\OneToMany(
     *      targetEntity="Follower",
     *      mappedBy="author",
     *      orphanRemoval=true
     * )
     * @ORM\OrderBy({"publishedAt" = "DESC"})
     */
    private $followers;

    /**
     * @ORM\OneToMany(
     *      targetEntity="Follower",
     *      mappedBy="userFollowed",
     *      orphanRemoval=true
     * )
     * @ORM\OrderBy({"publishedAt" = "DESC"})
     */
    private $follows;

    /**
     * @ORM\OneToMany(
     *      targetEntity="Masta\PlateFormeBundle\Entity\Category\CategoryFollower",
     *      mappedBy="author",
     *      orphanRemoval=true
     * )
     * @ORM\OrderBy({"publishedAt" = "DESC"})
     */
    private $categoryFollows;

    /**
     * @ORM\OneToMany(
     *      targetEntity="Masta\PlateFormeBundle\Entity\Product\Product",
     *      mappedBy="author",
     *      orphanRemoval=true
     * )
     * @ORM\OrderBy({"publishedAt" = "DESC"})
     */
    private $products;

    /**
     * @ORM\OneToMany(
     *      targetEntity="Masta\PlateFormeBundle\Entity\Product\ProductVote",
     *      mappedBy="author",
     *      orphanRemoval=true
     * )
     * @ORM\OrderBy({"publishedAt" = "DESC"})
     */
    private $productVotes;

    /**
     * @ORM\OneToMany(
     *      targetEntity="Masta\PlateFormeBundle\Entity\Product\ProductView",
     *      mappedBy="author",
     *      orphanRemoval=true
     * )
     * @ORM\OrderBy({"publishedAt" = "DESC"})
     */
    private $productViews;

    /**
     * @ORM\OneToMany(
     *      targetEntity="Masta\PlateFormeBundle\Entity\Notification\Notification",
     *      mappedBy="destinator",
     *      orphanRemoval=true
     * )
     * @ORM\OrderBy({"publishedAt" = "DESC"})
     */
    private $notifications;


    /**
     * @ORM\OneToMany(
     *      targetEntity="Masta\PlateFormeBundle\Entity\Deal\Conversation",
     *      mappedBy="author",
     *      orphanRemoval=true
     * )
     * @ORM\OrderBy({"publishedAt" = "DESC"})
     */
    private $conversations;

    /**
     * @ORM\OneToMany(
     *      targetEntity="Masta\PlateFormeBundle\Entity\Deal\Message",
     *      mappedBy="author",
     *      orphanRemoval=true
     * )
     * @ORM\OrderBy({"publishedAt" = "DESC"})
     */
    private $sendMessages;


    /**
     * @ORM\OneToMany(
     *      targetEntity="Masta\PlateFormeBundle\Entity\Deal\Message",
     *      mappedBy="receiver",
     *      orphanRemoval=true
     * )
     * @ORM\OrderBy({"publishedAt" = "DESC"})
     */
    private $receivedMessages;

    /**
     * @ORM\ManyToOne(targetEntity="Masta\PlateFormeBundle\Entity\Stat\Stat", inversedBy="users")
     */
    private $stat;

    /**
    * @ORM\Column(type="decimal", scale=3)
    */
    protected $rank = 1;


    protected $isAuthor;
    protected $isFollowIt;
    protected $isFollowMe;
    protected $compteState;

    public function __construct()
    {
        parent::__construct();
        $this->followers = new ArrayCollection();
        $this->follows = new ArrayCollection();
        $this->categoryFollows = new ArrayCollection();
        $this->products = new ArrayCollection();
        $this->productVotes = new ArrayCollection();
        $this->productViews = new ArrayCollection();
        $this->notifications = new ArrayCollection();
        $this->sendMessages = new ArrayCollection();
        $this->receivedMessages = new ArrayCollection();
        $this->conversations = new ArrayCollection();
        $this->clients = new ArrayCollection();

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

    public function setIsFollowIt($isFollowIt)
    {
        $this->isFollowIt = $isFollowIt;
        return $this;
    }
    public function getIsFollowIt()
    {
        return $this->isFollowIt;
    }

    public function setIsFollowMe($isFollowMe)
    {
        $this->isFollowMe = $isFollowMe;
        return $this;
    }

    public function getIsFollowMe()
    {
        return $this->isFollowMe;
    }


    public function setCompteState($compteState)
    {
        $this->compteState = $compteState;
        return $this;
    }

    public function getCompteState()
    {
        return $this->compteState;
    }
    
    //Cette fonction calcul le score 
    public function ranking()
    {
      $city_rank = $this->getCity()->getRank();


      $rank=2.5;
      return $rank;
    }

     /**
     * @ORM\PrePersist
     */
    public function increase()
    {
      $this->getStat()->setNbUsers($compteur+1);
      if($this->getStat() != NULL)
      {
        $compteur = $this->getStat()->getUsers()->count();
        $this->getStat()->setNbUsers($compteur+1);
      }
    }

    /**
     * @ORM\PreRemove
     */
    public function decrease()
    {
      $compteur = $this->getStat()->getUsers()->count();
      $this->getStat()->setNbUsers($compteur-1);

      $compteur = $this->getCountry()->getUsers()->count();
      $this->getCountry()->setNbUsers($compteur-1);

      $compteur = $this->getCountry()->getUsers()->count();
      $this->getCountry()->setNbUsers($compteur-1);
    }

    //end Verification function

    /**
     * @ORM\PreUpdate
     * Callback pour mettre à jour la date d'édition à chaque modification de l'entité
     */
    public function updateDate()
    {
        $this->setRank($this->ranking());
    }

    /**
     * Set facebookId
     *
     * @param string $facebookId
     *
     * @return User
     */
    public function setFacebookId($facebookId)
    {
        $this->facebook_id = $facebookId;

        return $this;
    }

    /**
     * Get facebookId
     *
     * @return string
     */
    public function getFacebookId()
    {
        return $this->facebook_id;
    }

    /**
     * Set facebookAccessToken
     *
     * @param string $facebookAccessToken
     *
     * @return User
     */
    public function setFacebookAccessToken($facebookAccessToken)
    {
        $this->facebook_access_token = $facebookAccessToken;

        return $this;
    }

    /**
     * Get facebookAccessToken
     *
     * @return string
     */
    public function getFacebookAccessToken()
    {
        return $this->facebook_access_token;
    }

    /**
     * Set googleId
     *
     * @param string $googleId
     *
     * @return User
     */
    public function setGoogleId($googleId)
    {
        $this->google_id = $googleId;

        return $this;
    }

    /**
     * Get googleId
     *
     * @return string
     */
    public function getGoogleId()
    {
        return $this->google_id;
    }

    /**
     * Set googleAccessToken
     *
     * @param string $googleAccessToken
     *
     * @return User
     */
    public function setGoogleAccessToken($googleAccessToken)
    {
        $this->google_access_token = $googleAccessToken;

        return $this;
    }

    /**
     * Get googleAccessToken
     *
     * @return string
     */
    public function getGoogleAccessToken()
    {
        return $this->google_access_token;
    }

    /**
     * Set fullName
     *
     * @param string $fullName
     *
     * @return User
     */
    public function setFullName($fullName)
    {
        $this->fullName = $fullName;

        return $this;
    }

    /**
     * Get fullName
     *
     * @return string
     */
    public function getFullName()
    {
        return $this->fullName;
    }

    /**
     * Set gender
     *
     * @param string $gender
     *
     * @return User
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get gender
     *
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set birthday
     *
     * @param \DateTime $birthday
     *
     * @return User
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;

        return $this;
    }

    /**
     * Get birthday
     *
     * @return \DateTime
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * Set language
     *
     * @param string $language
     *
     * @return User
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Get language
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return User
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
     * Set phoneNumber
     *
     * @param string $phoneNumber
     *
     * @return User
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    /**
     * Get phoneNumber
     *
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * Set webSite
     *
     * @param string $webSite
     *
     * @return User
     */
    public function setWebSite($webSite)
    {
        $this->webSite = $webSite;

        return $this;
    }

    /**
     * Get webSite
     *
     * @return string
     */
    public function getWebSite()
    {
        return $this->webSite;
    }

    /**
     * Set isPrivate
     *
     * @param boolean $isPrivate
     *
     * @return User
     */
    public function setIsPrivate($isPrivate)
    {
        $this->isPrivate = $isPrivate;

        return $this;
    }

    /**
     * Get isPrivate
     *
     * @return boolean
     */
    public function getIsPrivate()
    {
        return $this->isPrivate;
    }

    /**
     * Set isMailNotify
     *
     * @param boolean $isMailNotify
     *
     * @return User
     */
    public function setIsMailNotify($isMailNotify)
    {
        $this->isMailNotify = $isMailNotify;

        return $this;
    }

    /**
     * Get isMailNotify
     *
     * @return boolean
     */
    public function getIsMailNotify()
    {
        return $this->isMailNotify;
    }

    /**
     * Set nbFollowers
     *
     * @param integer $nbFollowers
     *
     * @return User
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
     * Set nbFollows
     *
     * @param integer $nbFollows
     *
     * @return User
     */
    public function setNbFollows($nbFollows)
    {
        $this->nbFollows = $nbFollows;

        return $this;
    }

    /**
     * Get nbFollows
     *
     * @return integer
     */
    public function getNbFollows()
    {
        return $this->nbFollows;
    }

    /**
     * Set nbProducts
     *
     * @param integer $nbProducts
     *
     * @return User
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
     * Set nbProductVotes
     *
     * @param integer $nbProductVotes
     *
     * @return User
     */
    public function setNbProductVotes($nbProductVotes)
    {
        $this->nbProductVotes = $nbProductVotes;

        return $this;
    }

    /**
     * Get nbProductVotes
     *
     * @return integer
     */
    public function getNbProductVotes()
    {
        return $this->nbProductVotes;
    }

    /**
     * Set nbProductViews
     *
     * @param integer $nbProductViews
     *
     * @return User
     */
    public function setNbProductViews($nbProductViews)
    {
        $this->nbProductViews = $nbProductViews;

        return $this;
    }

    /**
     * Get nbProductViews
     *
     * @return integer
     */
    public function getNbProductViews()
    {
        return $this->nbProductViews;
    }

    /**
     * Set nbCategoryFollows
     *
     * @param integer $nbCategoryFollows
     *
     * @return User
     */
    public function setNbCategoryFollows($nbCategoryFollows)
    {
        $this->nbCategoryFollows = $nbCategoryFollows;

        return $this;
    }

    /**
     * Get nbCategoryFollows
     *
     * @return integer
     */
    public function getNbCategoryFollows()
    {
        return $this->nbCategoryFollows;
    }

    /**
     * Set nbNotifications
     *
     * @param integer $nbNotifications
     *
     * @return User
     */
    public function setNbNotifications($nbNotifications)
    {
        $this->nbNotifications = $nbNotifications;

        return $this;
    }

    /**
     * Get nbNotifications
     *
     * @return integer
     */
    public function getNbNotifications()
    {
        return $this->nbNotifications;
    }

    /**
     * Set nbSendMessages
     *
     * @param integer $nbSendMessages
     *
     * @return User
     */
    public function setNbSendMessages($nbSendMessages)
    {
        $this->nbSendMessages = $nbSendMessages;

        return $this;
    }

    /**
     * Get nbSendMessages
     *
     * @return integer
     */
    public function getNbSendMessages()
    {
        return $this->nbSendMessages;
    }

    /**
     * Set nbReceivedMessages
     *
     * @param integer $nbReceivedMessages
     *
     * @return User
     */
    public function setNbReceivedMessages($nbReceivedMessages)
    {
        $this->nbReceivedMessages = $nbReceivedMessages;

        return $this;
    }

    /**
     * Get nbReceivedMessages
     *
     * @return integer
     */
    public function getNbReceivedMessages()
    {
        return $this->nbReceivedMessages;
    }

    /**
     * Set nbConversations
     *
     * @param integer $nbConversations
     *
     * @return User
     */
    public function setNbConversations($nbConversations)
    {
        $this->nbConversations = $nbConversations;

        return $this;
    }

    /**
     * Get nbConversations
     *
     * @return integer
     */
    public function getNbConversations()
    {
        return $this->nbConversations;
    }

    /**
     * Set rank
     *
     * @param string $rank
     *
     * @return User
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
     * Set country
     *
     * @param \Masta\UserBundle\Entity\Country $country
     *
     * @return User
     */
    public function setCountry(\Masta\UserBundle\Entity\Country $country = null)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return \Masta\UserBundle\Entity\Country
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set city
     *
     * @param \Masta\UserBundle\Entity\City $city
     *
     * @return User
     */
    public function setCity(\Masta\UserBundle\Entity\City $city = null)
    {
        $this->city = $city;
        return $this;
    }

    /**
     * Get city
     *
     * @return \Masta\UserBundle\Entity\City
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set profilePicture
     *
     * @param \Masta\PlateFormeBundle\Entity\Picture\Picture $profilePicture
     *
     * @return User
     */
    public function setProfilePicture(\Masta\PlateFormeBundle\Entity\Picture\Picture $profilePicture = null)
    {
        $this->profilePicture = $profilePicture;

        return $this;
    }

    /**
     * Get profilePicture
     *
     * @return \Masta\PlateFormeBundle\Entity\Picture\Picture
     */
    public function getProfilePicture()
    {
        return $this->profilePicture;
    }

    /**
     * Add follower
     *
     * @param \Masta\UserBundle\Entity\Follower $follower
     *
     * @return User
     */
    public function addFollower(\Masta\UserBundle\Entity\Follower $follower)
    {
        $this->followers[] = $follower;

        return $this;
    }

    /**
     * Remove follower
     *
     * @param \Masta\UserBundle\Entity\Follower $follower
     */
    public function removeFollower(\Masta\UserBundle\Entity\Follower $follower)
    {
        $this->followers->removeElement($follower);
    }

    /**
     * Get followers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFollowers()
    {
        return $this->followers;
    }

    /**
     * Add follow
     *
     * @param \Masta\UserBundle\Entity\Follower $follow
     *
     * @return User
     */
    public function addFollow(\Masta\UserBundle\Entity\Follower $follow)
    {
        $this->follows[] = $follow;

        return $this;
    }

    /**
     * Remove follow
     *
     * @param \Masta\UserBundle\Entity\Follower $follow
     */
    public function removeFollow(\Masta\UserBundle\Entity\Follower $follow)
    {
        $this->follows->removeElement($follow);
    }

    /**
     * Get follows
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFollows()
    {
        return $this->follows;
    }

    /**
     * Add categoryFollow
     *
     * @param \Masta\PlateFormeBundle\Entity\Category\CategoryFollower $categoryFollow
     *
     * @return User
     */
    public function addCategoryFollow(\Masta\PlateFormeBundle\Entity\Category\CategoryFollower $categoryFollow)
    {
        $this->categoryFollows[] = $categoryFollow;

        return $this;
    }

    /**
     * Remove categoryFollow
     *
     * @param \Masta\PlateFormeBundle\Entity\Category\CategoryFollower $categoryFollow
     */
    public function removeCategoryFollow(\Masta\PlateFormeBundle\Entity\Category\CategoryFollower $categoryFollow)
    {
        $this->categoryFollows->removeElement($categoryFollow);
    }

    /**
     * Get categoryFollows
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCategoryFollows()
    {
        return $this->categoryFollows;
    }

    /**
     * Add product
     *
     * @param \Masta\PlateFormeBundle\Entity\Product\Product $product
     *
     * @return User
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

    /**
     * Add productVote
     *
     * @param \Masta\PlateFormeBundle\Entity\Product\ProductVote $productVote
     *
     * @return User
     */
    public function addProductVote(\Masta\PlateFormeBundle\Entity\Product\ProductVote $productVote)
    {
        $this->productVotes[] = $productVote;

        return $this;
    }

    /**
     * Remove productVote
     *
     * @param \Masta\PlateFormeBundle\Entity\Product\ProductVote $productVote
     */
    public function removeProductVote(\Masta\PlateFormeBundle\Entity\Product\ProductVote $productVote)
    {
        $this->productVotes->removeElement($productVote);
    }

    /**
     * Get productVotes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProductVotes()
    {
        return $this->productVotes;
    }

    /**
     * Add productView
     *
     * @param \Masta\PlateFormeBundle\Entity\Product\ProductView $productView
     *
     * @return User
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
     * Add notification
     *
     * @param \Masta\PlateFormeBundle\Entity\Notification\Notification $notification
     *
     * @return User
     */
    public function addNotification(\Masta\PlateFormeBundle\Entity\Notification\Notification $notification)
    {
        $this->notifications[] = $notification;

        return $this;
    }

    /**
     * Remove notification
     *
     * @param \Masta\PlateFormeBundle\Entity\Notification\Notification $notification
     */
    public function removeNotification(\Masta\PlateFormeBundle\Entity\Notification\Notification $notification)
    {
        $this->notifications->removeElement($notification);
    }

    /**
     * Get notifications
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNotifications()
    {
        return $this->notifications;
    }

    /**
     * Add conversation
     *
     * @param \Masta\PlateFormeBundle\Entity\Deal\Conversation $conversation
     *
     * @return User
     */
    public function addConversation(\Masta\PlateFormeBundle\Entity\Deal\Conversation $conversation)
    {
        $this->conversations[] = $conversation;

        return $this;
    }

    /**
     * Remove conversation
     *
     * @param \Masta\PlateFormeBundle\Entity\Deal\Conversation $conversation
     */
    public function removeConversation(\Masta\PlateFormeBundle\Entity\Deal\Conversation $conversation)
    {
        $this->conversations->removeElement($conversation);
    }

    /**
     * Get conversations
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getConversations()
    {
        return $this->conversations;
    }

    /**
     * Add sendMessage
     *
     * @param \Masta\PlateFormeBundle\Entity\Deal\Message $sendMessage
     *
     * @return User
     */
    public function addSendMessage(\Masta\PlateFormeBundle\Entity\Deal\Message $sendMessage)
    {
        $this->sendMessages[] = $sendMessage;

        return $this;
    }

    /**
     * Remove sendMessage
     *
     * @param \Masta\PlateFormeBundle\Entity\Deal\Message $sendMessage
     */
    public function removeSendMessage(\Masta\PlateFormeBundle\Entity\Deal\Message $sendMessage)
    {
        $this->sendMessages->removeElement($sendMessage);
    }

    /**
     * Get sendMessages
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSendMessages()
    {
        return $this->sendMessages;
    }

    /**
     * Add receivedMessage
     *
     * @param \Masta\PlateFormeBundle\Entity\Deal\Message $receivedMessage
     *
     * @return User
     */
    public function addReceivedMessage(\Masta\PlateFormeBundle\Entity\Deal\Message $receivedMessage)
    {
        $this->receivedMessages[] = $receivedMessage;

        return $this;
    }

    /**
     * Remove receivedMessage
     *
     * @param \Masta\PlateFormeBundle\Entity\Deal\Message $receivedMessage
     */
    public function removeReceivedMessage(\Masta\PlateFormeBundle\Entity\Deal\Message $receivedMessage)
    {
        $this->receivedMessages->removeElement($receivedMessage);
    }

    /**
     * Get receivedMessages
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReceivedMessages()
    {
        return $this->receivedMessages;
    }

    /**
     * Set stat
     *
     * @param \Masta\PlateFormeBundle\Entity\Stat\Stat $stat
     *
     * @return User
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
