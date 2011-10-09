<?php
/*
 *
 */

namespace Opensoft\Bundle\CodeConversationBundle\Model;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 *
 *
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 */
class User extends BaseUser implements UserInterface
{

    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
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
