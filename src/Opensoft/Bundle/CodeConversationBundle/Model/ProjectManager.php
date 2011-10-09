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
abstract class ProjectManager implements ProjectManagerInterface
{
    protected $sourceCodeRepository;

    public function __construct(RepositoryInterface $sourceCodeRepo)
    {
        $this->sourceCodeRepository = $sourceCodeRepo;
    }
    
    public function createProject()
    {
        $class = $this->getClass();

        return new $class($this->sourceCodeRepository);
    }

}
