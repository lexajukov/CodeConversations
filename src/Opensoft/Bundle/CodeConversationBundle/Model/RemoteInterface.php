<?php
/*
 * This file is part of ProFIT
 *
 * Copyright (c) 2011 Farheap Solutions (http://www.farheap.com)
 *
 * The unauthorized use of this code outside the boundaries of
 * Farheap Solutions Inc. is prohibited.
 */

namespace Opensoft\Bundle\CodeConversationBundle\Model;

use Opensoft\Bundle\CodeConversationBundle\SourceCode\RepositoryInterface;
use Redpanda\Bundle\ActivityStreamBundle\Streamable\StreamableInterface;

/**
 *
 *
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 */ 
interface RemoteInterface
{
    /**
     * @param integer $id
     */
    public function setId($id);

    /**
     * @return integer
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
     * @param string $slug
     */
    public function setSlug($slug);

    /**
     * @return string
     */
    public function getSlug();

    /**
     * @param string $url
     */
    public function setUrl($url);

    /**
     * @return string
     */
    public function getUrl();

    /**
     * @param string $password
     */
    public function setPassword($password);

    /**
     * @return string
     */
    public function getPassword();

    /**
     * @param string $username
     */
    public function setUsername($username);

    /**
     * @return string
     */
    public function getUsername();

    /**
     * @param BranchInterface $branch
     */
    public function setHeadBranch(BranchInterface $branch);

    /**
     * @return BranchInterface
     */
    public function getHeadBranch();

    /**
     * @param BranchInterface[] $branches
     */
    public function setBranches(array $branches);

    /**
     * @param BranchInterface $branch
     */
    public function addBranch(BranchInterface $branch);

    /**
     * @return BranchInterface[]
     */
    public function getBranches();

    /**
     * @param ProjectInterface $project
     */
    public function setProject(ProjectInterface $project);

    /**
     * @return ProjectInterface
     */
    public function getProject();
}
