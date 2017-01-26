<?php

namespace Masta\PlateFormeBundle\Entity\Category;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation\Expose;

/**
 * Category
 *
 * @ORM\Table(name="category_categories")
 * @ORM\Entity(repositoryClass="Masta\PlateFormeBundle\Repository\Category\CategoryRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Category
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
     * @ORM\Column(name="name", type="string", length=30)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="is_published", type="boolean", nullable=true)
     */
    private $isPublished;

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
     * @var integer
     *
     * @ORM\Column(name="nb_albums", type="integer",nullable=true)
     */
    private $nbAlbums;

    /**
     * @ORM\OneToMany(
     *      targetEntity="Masta\PlateFormeBundle\Entity\Album\Album",
     *      mappedBy="category",
     *      orphanRemoval=true
     * )
     * @ORM\OrderBy({"publishedAt" = "DESC"})
     */
    private $albums;

    /**
     * @var integer
     *
     * @ORM\Column(name="nb_followers", type="integer",nullable=true)
     */
    private $nbFollowers;

    /**
     * @ORM\OneToMany(
     *      targetEntity="CategoryFollower",
     *      mappedBy="category",
     *      orphanRemoval=true
     * )
     * @ORM\OrderBy({"publishedAt" = "DESC"})
     */
    private $categoryFollowers;

    /**
     * @ORM\ManyToOne(targetEntity="Masta\PlateFormeBundle\Entity\Stat\Stat", inversedBy="categories")
     */
    private $stat;

    /**
    * @ORM\Column(type="decimal", scale=3)
    */
    protected $rank = 1;

    /**
     * @Expose
     */
    protected $isFollow;
    /**
     * @Expose
     */
    protected $isAuthent;

    public function __construct()
    {
        $this->publishedAt = new \DateTime();
        $this->albums = new ArrayCollection();
        $this->categoryFollowers = new ArrayCollection();
    }


    //Verification Function
    public function setIsFollow($isFollow)
    {
        $this->isFollow = $isFollow;
        return $this;
    }
    public function getIsFollow()
    {
        return $this->isFollow;
    }
    public function setIsAuthent($isAuthent)
    {
        $this->isAuthent = $isAuthent;
        return $this;
    }
    public function getIsAuthent()
    {
        return $this->isAuthent;
    }

    public function ranking()
    {
        $nb_total_categories = $this->getStat()->getNbCategories();
        $nb_total_albums = $this->getStat()->getNbAlbums();
        $nb_total_users = $this->getStat()->getNbUsers();
        
        $nb_followers = $this->getNbFollowers();
        $nb_albums = $this->getNbAlbums();


        if($nb_total_categories>0)
            $category_probability = 1/$nb_total_categories; 
        else
            $category_probability =0;

        if($nb_albums>0)
            $album_probability = 1/$nb_albums;
        else
            $album_probability = 0;

        if($nb_total_albums)
            $album_fc = $nb_albums/$nb_total_albums;
        else
            $album_fc=0;

        if($nb_total_users)
            $follower_fc = $nb_followers/$nb_total_users; 
        else
            $follower_fc=0;
         

        $rank = ($album_fc*$album_probability)+($album_fc*$category_probability)+
        ($follower_fc*$album_probability)+($follower_fc*$category_probability);
        return $rank;
    }


    /**
     * toString
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
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
      $compteur = $this->getStat()->getCategories()->count();
      $this->getStat()->setNbCategories($compteur+1);
    }
    
    /**
     * @ORM\PreRemove
     */
    public function decrease()
    {
      $compteur = $this->getStat()->getCategories()->count();
      $this->getStat()->setNbCategories($compteur-1);
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
     * Set name
     *
     * @param string $name
     *
     * @return Category
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set publishedAt
     *
     * @param \DateTime $publishedAt
     *
     * @return Category
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
     * @return Category
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
     * Set nbAlbums
     *
     * @param integer $nbAlbums
     *
     * @return Category
     */
    public function setNbAlbums($nbAlbums)
    {
        $this->nbAlbums = $nbAlbums;

        return $this;
    }

    /**
     * Get nbAlbums
     *
     * @return integer
     */
    public function getNbAlbums()
    {
        return $this->nbAlbums;
    }

    /**
     * Set nbFollowers
     *
     * @param integer $nbFollowers
     *
     * @return Category
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
     * Add album
     *
     * @param \Masta\PlateFormeBundle\Entity\Album\Album $album
     *
     * @return Category
     */
    public function addAlbum(\Masta\PlateFormeBundle\Entity\Album\Album $album)
    {
        $this->albums[] = $album;

        return $this;
    }

    /**
     * Remove album
     *
     * @param \Masta\PlateFormeBundle\Entity\Album\Album $album
     */
    public function removeAlbum(\Masta\PlateFormeBundle\Entity\Album\Album $album)
    {
        $this->albums->removeElement($album);
    }

    /**
     * Get albums
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAlbums()
    {
        return $this->albums;
    }

    /**
     * Add categoryFollower
     *
     * @param \Masta\PlateFormeBundle\Entity\Category\CategoryFollower $categoryFollower
     *
     * @return Category
     */
    public function addCategoryFollower(\Masta\PlateFormeBundle\Entity\Category\CategoryFollower $categoryFollower)
    {
        $this->categoryFollowers[] = $categoryFollower;

        return $this;
    }

    /**
     * Remove categoryFollower
     *
     * @param \Masta\PlateFormeBundle\Entity\Category\CategoryFollower $categoryFollower
     */
    public function removeCategoryFollower(\Masta\PlateFormeBundle\Entity\Category\CategoryFollower $categoryFollower)
    {
        $this->categoryFollowers->removeElement($categoryFollower);
    }

    /**
     * Get categoryFollowers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCategoryFollowers()
    {
        return $this->categoryFollowers;
    }
    /**
     * Set isPublished
     *
     * @param boolean $isPublished
     *
     * @return Category
     */
    public function setIsPublished($isPublished)
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    /**
     * Get isPublished
     *
     * @return boolean
     */
    public function getIsPublished()
    {
        return $this->isPublished;
    }

    /**
     * Set rank
     *
     * @param string $rank
     *
     * @return Category
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
     * Set stat
     *
     * @param \Masta\PlateFormeBundle\Entity\Stat\Stat $stat
     *
     * @return Category
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
