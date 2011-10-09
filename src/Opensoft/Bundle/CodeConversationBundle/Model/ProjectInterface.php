<?php
/*
 *
 */

namespace Opensoft\Bundle\CodeConversationBundle\Model;

use Opensoft\Bundle\CodeConversationBundle\SourceCode\RepositoryInterface;

/**
 *
 *
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 */
interface ProjectInterface 
{
    public function getBranches();

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
     * @param string $password
     */
    public function setPassword($password);

    /**
     * @return string
     */
    public function getPassword();

    /**
     * @param PullRequestInterface[] $pullRequests
     */
    public function setPullRequests(array $pullRequests);

    /**
     * @return PullRequestInterface[]
     */
    public function getPullRequests();

    /**
     * @param string $url
     */
    public function setUrl($url);

    /**
     * @return string
     */
    public function getUrl();

    /**
     * @param string $username
     */
    public function setUsername($username);

    /**
     * @return string
     */
    public function getUsername();

    /**
     * @param \Opensoft\Bundle\CodeConversationBundle\Entity\Branch $headBranch
     */
    public function setHeadBranch(BranchInterface $headBranch);

    /**
     * @return \Opensoft\Bundle\CodeConversationBundle\Entity\Branch
     */
    public function getHeadBranch();
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
}
