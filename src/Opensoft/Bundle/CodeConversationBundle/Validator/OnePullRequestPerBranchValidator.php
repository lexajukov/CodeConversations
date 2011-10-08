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
use Symfony\Bundle\DoctrineBundle\Registry;

/**
 *
 *
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 */ 
class OnePullRequestPerBranchValidator extends ConstraintValidator
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @param \Symfony\Bundle\DoctrineBundle\Registry $doctrineRegistry
     */
    public function __construct(Registry $doctrineRegistry)
    {
        $this->em = $doctrineRegistry->getEntityManager();
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

        $exists = $this->em->getRepository('OpensoftCodeConversationBundle:PullRequest')->findBy(array(
            'project' => $object->getProject()->getId(),
            'sourceBranch' => $object->getSourceBranch()->getId(),
            'destinationBranch' => $object->getDestinationBranch()->getId()
        ));
//        print_r($exists);
        if (!empty($exists)) {
            $this->setMessage($constraint->message);
            return false;
        }

        return true;
    }

}
