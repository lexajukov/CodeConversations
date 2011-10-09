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
use Opensoft\Bundle\CodeConversationBundle\SourceCode\RepositoryInterface;
use Opensoft\Bundle\CodeConversationBundle\Exception\BuildException;

/**
 *
 *
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 */ 
class BranchPointValidator extends ConstraintValidator
{
    /**
     * @var \Opensoft\Bundle\CodeConversationBundle\SourceCode\RepositoryInterface
     */
    private $sourceCodeRepository;

    /**
     * @param \Opensoft\Bundle\CodeConversationBundle\SourceCode\RepositoryInterface $sourceCodeRepo
     */
    public function __construct(RepositoryInterface $sourceCodeRepo)
    {
        $this->sourceCodeRepository = $sourceCodeRepo;
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
            $this->sourceCodeRepository->init($object->getProject());
            $common = $this->sourceCodeRepository->mergeBase($source, $destination);
        } catch (\BuildException $e) {
            $this->setMessage($constraint->message);

            return false;
        }

        return true;
    }

}
