<?php

namespace Masta\PlateFormeBundle\Entity\Report;

use Doctrine\ORM\Mapping as ORM;

/**
 * Report
 *
 * @ORM\Table(name="reports")
 * @ORM\Entity(repositoryClass="Masta\ReportBundle\Repository\Report\ReportRepository")
 */
class Report
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
     * @var integer
     *
     * @ORM\Column(name="reportStatus", type="integer")
     */
    private $reportStatus;

    /**
     * @var string
     *
     * @ORM\Column(name="authorMessage", type="string", length=255)
     */
    private $authorMessage;

    /**
     * @var string
     *
     * @ORM\Column(name="fixerMessage", type="string", length=255)
     */
    private $fixerMessage;


    /**
     * @ORM\ManyToOne(targetEntity="Masta\UserBundle\Entity\User")
     */
    private $author;

    /**
     * @ORM\ManyToOne(targetEntity="Masta\UserBundle\Entity\User")
     */
    private $fixer;

    public function __construct()
    {
        $this->publishedAt = new \DateTime();
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
     * @return Report
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
     * Set reportStatus
     *
     * @param integer $reportStatus
     *
     * @return Report
     */
    public function setReportStatus($reportStatus)
    {
        $this->reportStatus = $reportStatus;

        return $this;
    }

    /**
     * Get reportStatus
     *
     * @return integer
     */
    public function getReportStatus()
    {
        return $this->reportStatus;
    }

    /**
     * Set authorMessage
     *
     * @param string $authorMessage
     *
     * @return Report
     */
    public function setAuthorMessage($authorMessage)
    {
        $this->authorMessage = $authorMessage;

        return $this;
    }

    /**
     * Get authorMessage
     *
     * @return string
     */
    public function getAuthorMessage()
    {
        return $this->authorMessage;
    }

    /**
     * Set fixerMessage
     *
     * @param string $fixerMessage
     *
     * @return Report
     */
    public function setFixerMessage($fixerMessage)
    {
        $this->fixerMessage = $fixerMessage;

        return $this;
    }

    /**
     * Get fixerMessage
     *
     * @return string
     */
    public function getFixerMessage()
    {
        return $this->fixerMessage;
    }

    /**
     * Set author
     *
     * @param \Masta\UserBundle\Entity\User $author
     *
     * @return Report
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
     * Set fixer
     *
     * @param \Masta\UserBundle\Entity\User $fixer
     *
     * @return Report
     */
    public function setFixer(\Masta\UserBundle\Entity\User $fixer = null)
    {
        $this->fixer = $fixer;

        return $this;
    }

    /**
     * Get fixer
     *
     * @return \Masta\UserBundle\Entity\User
     */
    public function getFixer()
    {
        return $this->fixer;
    }
}
