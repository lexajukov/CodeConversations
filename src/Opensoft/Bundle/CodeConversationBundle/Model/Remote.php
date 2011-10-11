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
class Remote implements RemoteInterface
{
    protected $id;

    protected $name;

    protected $url;

    protected $username;

    protected $password;

    protected $project;

    protected $branches;

    protected $headBranch;

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param BranchInterface $headBranch
     */
    public function setHeadBranch(BranchInterface $headBranch)
    {
        $this->headBranch = $headBranch;
    }

    /**
     * @return BranchInterface
     */
    public function getHeadBranch()
    {
        return $this->headBranch;
    }

    /**
     * @param BranchInterface[] $branches
     */
    public function setBranches(array $branches)
    {
        $this->branches = array();
        foreach ($branches as $branch) {
            $this->addBranch($branch);
        }
    }

    /**
     * @param BranchInterface $branch
     */
    public function addBranch(BranchInterface $branch)
    {
        $this->branches[] = $branch;
    }

    /**
     * @return BranchInterface[]
     */
    public function getBranches()
    {
        return $this->branches;
    }

    public function setProject(ProjectInterface $project)
    {
        $this->project = $project;
    }

    public function getProject()
    {
        return $this->project;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function getUsername()
    {
        return $this->username;
    }

}
