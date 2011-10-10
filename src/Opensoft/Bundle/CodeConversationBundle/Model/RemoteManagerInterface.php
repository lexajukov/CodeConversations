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
     * @param ProjectInterface $project
     * @return RemoteInterface
     */
    public function createRemote(ProjectInterface $project);

    /**
     * @param string $slug
     * @return RemoteInterface
     */
    public function findRemoteByName($name);

    /**
     * @param string $slug
     * @return RemoteInterface
     */
    public function findRemoteByProject(ProjectInterface $project);

    /**
     * @param array $criteria
     * @return RemoteInterface[]
     */
    public function findRemoteBy(array $criteria);

    /**
     * @return ProjectInterface[]
     */
    public function findRemotes();

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
