<?php
/*
 *
 */

namespace Opensoft\Bundle\CodeConversationBundle\Model;

use Opensoft\Bundle\CodeConversationBundle\SourceCode\RepositoryInterface;
use Redpanda\Bundle\ActivityStreamBundle\Streamable\StreamableInterface;

/**
 *
 *
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 */
interface ProjectInterface extends StreamableInterface
{
    /**
     * @param int $id
     */
    public function setId($id);

    /**
     * @param string $name
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getName();

    public function setPullRequests($pullRequests);

    public function addPullRequest(PullRequestInterface $pullRequest);

    /**
     * @return PullRequestInterface[]
     */
    public function getPullRequests();

    /**
     * @param string $slug
     */
    public function setSlug($slug);

    /**
     * @return string
     */
    public function getSlug();

    /**
     * @return RemoteInterface
     */
    public function getDefaultRemote();
    
    public function setDefaultRemote(RemoteInterface $remote);

    public function setRemotes($remotes);

    public function addRemote(RemoteInterface $remote);

    /**
     * @return RemoteInterface[]
     */
    public function getRemotes();
}
