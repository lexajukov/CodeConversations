<?php
/*
 *
 */

namespace Opensoft\Bundle\CodeConversationBundle\Model;

use Redpanda\Bundle\ActivityStreamBundle\Streamable\StreamableInterface;
use FOS\UserBundle\Entity\User as BaseUser;

/**
 *
 *
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 */
class User extends BaseUser implements UserInterface, StreamableInterface
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

    /**
     * Return an array for the form
     *
     * array(
     *   'route' => $routeName,
     *   'parameters' => array(key => value, ...)
     * )
     *
     * @return array
     */
    public function getAbsolutePathParams()
    {
        return array(
            'route' => 'opensoft_codeconversation_user_show',
            'parameters' => array(
                'usernameCanonical' => $this->getUsernameCanonical()
            )
        );
    }


}
