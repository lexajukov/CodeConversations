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


    /**
     * @param PullRequestInterface[] $pullRequests
     */
    public function setPullRequests(array $pullRequests);

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

    public function setSourceCodeRepository(RepositoryInterface $repo);

    public function getSourceCodeRepository();

    /**
     * @param \Closure $callback
     */
    public function initSourceCodeRepo($callback = null);


    public function synchronizeBranches();

    /**
     * @param string $sha1
     * @return Commit
     */
    public function getCommit($sha1);

    public function getRecentCommits($object = null, $limit = null);

    public function getFileAtCommit($sha1, $filepath);


    public function setRemotes($remotes);

    public function addRemote(RemoteInterface $remote);

    /**
     * @return RemoteInterface[]
     */
    public function getRemotes();
}
