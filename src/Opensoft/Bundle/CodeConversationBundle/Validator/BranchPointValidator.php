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
use Opensoft\Bundle\CodeConversationBundle\Git\Repository;
use Opensoft\Bundle\CodeConversationBundle\Exception\BuildException;

/**
 *
 *
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 */ 
class BranchPointValidator extends ConstraintValidator
{
    /**
     * @var \Opensoft\Bundle\CodeConversationBundle\Git\Repository
     */
    private $repository;

    /**
     * @param \Opensoft\Bundle\CodeConversationBundle\Git\Repository $repository
     */
    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
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

        
        $source = $object->getSourceBranch()->getName();
        $destination = $object->getDestinationBranch()->getName();

        try {
            $this->repository->init($object->getProject());
            $common = $this->repository->mergeBase($source, $destination);
        } catch (\BuildException $e) {
            $this->setMessage($constraint->message);

            return false;
        }

        return true;
    }

}
