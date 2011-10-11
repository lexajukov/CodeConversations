<?php
/*
 * This file is part of ProFIT
 *
 * Copyright (c) 2011 Farheap Solutions (http://www.farheap.com)
 *
 * The unauthorized use of this code outside the boundaries of
 * Farheap Solutions Inc. is prohibited.
 */

namespace Opensoft\Bundle\CodeConversationBundle\Git;

use Opensoft\Bundle\CodeConversationBundle\Model\ProjectInterface;
use Opensoft\Bundle\CodeConversationBundle\Model\RemoteInterface;
use Symfony\Component\HttpKernel\Util\Filesystem;
use PHPGit_Repository;

/**
 *
 *
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 */ 
class RepositoryManager
{
    /**
     * @var string
     */
    protected $dir;

    /**
     * @var \Symfony\Component\HttpKernel\Util\Filesystem
     */
    protected $filesystem;

    /**
     * @var string
     */
    protected $gitExecutable;

    /**
     * @param string $dir
     * @param string $gitExecutable
     */
    public function __construct($dir, $gitExecutable)
    {
        $this->dir = $dir;
        $this->gitExecutable = $gitExecutable;
        $this->filesystem = new Filesystem();

        $this->filesystem->mkdir($this->dir);
    }

    /**
     * @param \Opensoft\Bundle\CodeConversationBundle\Model\ProjectInterface $project
     * @return Repository
     */
    public function getRepository(ProjectInterface $project)
    {
        if ($this->hasRepository($project)) {
            $dir = $this->getRepoDir($project);
            $gitRepo = new PHPGit_Repository($dir, false, array('git_executable' => $this->gitExecutable));
        } else {
            $gitRepo = $this->createRepository($project);
        }

        return new Repository($project, $gitRepo);
    }

    /**
     * @param \Opensoft\Bundle\CodeConversationBundle\Model\ProjectInterface $project
     * @return bool
     */
    public function hasRepository(ProjectInterface $project)
    {
        $dir = $this->getRepoDir($project);

        return is_dir($dir.'/.git');
    }

    /**
     * @param \Opensoft\Bundle\CodeConversationBundle\Model\ProjectInterface $project
     * @return \PHPGit_Repository
     */
    public function createRepository(ProjectInterface $project)
    {
        $dir = $this->getRepoDir($project);
        $this->filesystem->mkdir($dir);
        $gitRepo = PHPGit_Repository::create($dir, false, array('git_executable' => $this->gitExecutable));
        $remote = $project->getDefaultRemote();
        $gitRepo->git(sprintf('remote add %s %s', $remote->getName(), $remote->getUrl()));
        $gitRepo->git(sprintf('fetch -p %s', $remote->getName()));

        return $gitRepo;
    }

    /**
     * @param \Opensoft\Bundle\CodeConversationBundle\Model\ProjectInterface $project
     * @return string
     */
    public function getRepoDir(ProjectInterface $project)
    {
        return $this->dir.'/'.$project->getName();
    }

    /**
     * @param string $dir
     */
    public function setDir($dir)
    {
        $this->dir = $dir;
    }

    /**
     * @return string
     */
    public function getDir()
    {
        return $this->dir;
    }


}
