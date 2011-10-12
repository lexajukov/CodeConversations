<?php
/*
 *
 */

namespace Opensoft\Bundle\CodeConversationBundle\Model;

use Symfony\Component\Validator\Constraint;

/**
 * Remote Manager Interface
 *
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 */
interface RemoteManagerInterface
{
    /**
     * @return RemoteInterface
     */
    public function createRemote();

    /**
     * @param array $criteria
     * @return RemoteInterface[]
     */
    public function findRemotesBy(array $criteria);

    /**
     * @return RemoteInterface[]
     */
    public function findRemotes();

    /**
     * @param string $projectName
     * @param string $remoteName
     * @return RemoteInterface
     */
    public function findRemoteByProjectNameAndRemoteName($projectName, $remoteName);

    /**
     * @param RemoteInterface $remote
     */
    public function updateRemote(RemoteInterface $remote);

    /**
     * @param RemoteInterface $remote
     */
    public function deleteRemote(RemoteInterface $remote);

    /**
     * @return string
     */
    public function getClass();
}
