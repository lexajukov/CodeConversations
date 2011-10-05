<?php
/*
 * This file is part of ProFIT
 *
 * Copyright (c) 2011 Farheap Solutions (http://www.farheap.com)
 *
 * The unauthorized use of this code outside the boundaries of
 * Farheap Solutions Inc. is prohibited.
 */

namespace Opensoft\Bundle\GversationBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

/**
 *
 *
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 */ 
class PullRequestFormType extends AbstractType
{

    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('description')
            ->add('sourceBranch', null, array('property' => 'name'))
            ->add('destinationBranch', null, array('property' => 'name'))
        ;
    }


    public function getDefaultOptions(array $options)
    {
        return array('data_class' => 'Opensoft\Bundle\GversationBundle\Entity\PullRequest');
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    function getName()
    {
        return 'pull_request';
    }

}
