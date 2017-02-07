<?php

namespace Masta\UserBundle\Entity\OAuth;

use FOS\OAuthServerBundle\Entity\Client as BaseClient;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table("oauth2_clients")
 * @ORM\Entity
 */
class Client extends BaseClient
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    public function __construct()
    {
        parent::__construct();
        // your own logic
    }

    /**
     * Set user
     *
     * @param \Masta\UserBundle\Entity\User $user
     *
     * @return Client
     */
    public function setUser(\Masta\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Masta\UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
}
