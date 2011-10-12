<?php
/*
 *
 */

namespace Opensoft\Bundle\CodeConversationBundle\Model;

/**
 *
 *
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 */
interface BranchInterface
{
    /**
     * @param int $id
     */
    public function setId($id);

    /**
     * @return int
     */
    public function getId();

    /**
     * @param string $name
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getFullName();

    /**
     * @param RemoteInterface $remote
     */
    public function setRemote(RemoteInterface $remote);

    /**
     * @return RemoteInterface
     */
    public function getRemote();

    /**
     * @param \Boolean $enabled
     */
    public function setEnabled($enabled);

    /**
     * @return \Boolean
     */
    public function isEnabled();

    public function getTip();

    public function setTip($tip);
}
