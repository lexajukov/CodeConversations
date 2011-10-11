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

/**
 *
 *
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 */ 
interface RemoteInterface
{
    public function setId($id);

    public function getId();

    public function setName($name);

    public function getName();

    public function setUrl($url);

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

    public function setHeadBranch(BranchInterface $branch);

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

    public function setProject(ProjectInterface $project);

    public function getProject();
}
