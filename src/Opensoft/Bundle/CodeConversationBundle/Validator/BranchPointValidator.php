<?php
/*
 * This file is part of ProFIT
 *
 * Copyright (c) 2011 Farheap Solutions (http://www.farheap.com)
 *
 * The unauthorized use of this code outside the boundaries of
 * Farheap Solutions Inc. is prohibited.
 */

namespace Opensoft\Bundle\CodeConversationBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Opensoft\Bundle\CodeConversationBundle\Git\RepositoryManager;

/**
 *
 *
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 */ 
class BranchPointValidator extends ConstraintValidator
{
    /**
     * @var \Opensoft\Bundle\CodeConversationBundle\Git\RepositoryManager
     */
    private $repositoryManager;

    /**
     * @param \Opensoft\Bundle\CodeConversationBundle\Git\RepositoryManager $repositoryManager
     */
    public function __construct(RepositoryManager $repositoryManager)
    {
        $this->repositoryManager = $repositoryManager;
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param mixed      $object      The value that should be validated
     * @param Constraint $constraint The constrait for the validation
     *
     * @return Boolean Whether or not the value is valid
     *
     * @api
     */
    public function isValid($object, Constraint $constraint)
    {
        if (!is_object($object)) {
            throw new \RuntimeException('This is a class constraint.');
        }

        try {
            $repository = $this->repositoryManager->getRepository($object->getProject());


            $source = $object->getSourceBranch()->getRemote()->getName().'/'.$object->getSourceBranch()->getName();
            $destination = $object->getDestinationBranch()->getRemote()->getName().'/'.$object->getDestinationBranch()->getName();

            $common = $repository->getMergeBase($source, $destination);
        } catch (\GitRuntimeException $e) {
            $this->setMessage($constraint->message);

            return false;
        }

        return true;
    }

}
