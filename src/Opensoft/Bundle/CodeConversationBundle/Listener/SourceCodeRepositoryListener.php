<?php
/*
 * This file is part of ProFIT
 *
 * Copyright (c) 2011 Farheap Solutions (http://www.farheap.com)
 *
 * The unauthorized use of this code outside the boundaries of
 * Farheap Solutions Inc. is prohibited.
 */

namespace Opensoft\Bundle\CodeConversationBundle\Listener;

use Opensoft\Bundle\CodeConversationBundle\SourceCode\RepositoryFactory;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;

/**
 *
 *
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 */ 
class SourceCodeRepositoryListener 
{
    protected $repositoryFactory;

    public function __construct(RepositoryFactory $repositoryFactory)
    {
        $this->repositoryFactory = $repositoryFactory;
    }

    public function postLoad(LifeCycleEventArgs $eventArgs)
    {
        $project = $eventArgs->getEntity();
        $className = get_class($project);
        $em = $eventArgs->getEntityManager();
        $metadata = $em->getClassMetadata($className);

        if ($metadata->reflClass->implementsInterface('Opensoft\\Bundle\\CodeConversationBundle\\SourceCode\\SourceCodeInterface')) {
            $project->setSourceCodeRepository($this->repositoryFactory->initializeProject($project));
        }
    }
}
