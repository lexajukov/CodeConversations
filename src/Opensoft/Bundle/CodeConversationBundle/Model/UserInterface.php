<?php
/*
 *
 */

namespace Opensoft\Bundle\CodeConversationBundle\Model;

use FOS\UserBundle\Model\UserInterface as BaseUserInterface;

/**
 *
 *
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 */
interface UserInterface extends BaseUserInterface
{
    /**
     * @param string $gitAlias
     */
    public function setGitAlias($gitAlias);

    /**
     * @return string
     */
    public function getGitAlias();

    /**
     * @return string
     */
    public function setGravatar($gravatar);

    /**
     * @return string
     */
    public function getGravatar();
}
