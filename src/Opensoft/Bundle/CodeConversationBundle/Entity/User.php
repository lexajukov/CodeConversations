<?php
/*
 *
 */

namespace Opensoft\Bundle\CodeConversationBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 *
 *
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 *
 * @ORM\Entity
 * @ORM\Table(name="users")
 */
class User extends BaseUser
{

    /**
     * @var integer
     * 
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $gitAlias;

    public function __construct()
    {
        parent::__construct();
        // your own logic
    }

    /**
     * @param string $gitAlias
     */
    public function setGitAlias($gitAlias)
    {
        $this->gitAlias = $gitAlias;
    }

    /**
     * @return string
     */
    public function getGitAlias()
    {
        return $this->gitAlias;
    }


}
