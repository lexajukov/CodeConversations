<?php
/*
 * This file is part of ProFIT
 *
 * Copyright (c) 2011 Farheap Solutions (http://www.farheap.com)
 *
 * The unauthorized use of this code outside the boundaries of
 * Farheap Solutions Inc. is prohibited.
 */

namespace Opensoft\Bundle\CodeConversationBundle\Form\Type;

use Opensoft\Bundle\CodeConversationBundle\Entity\Project;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Doctrine\ORM\EntityRepository;

/**
 *
 *
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 */ 
class PullRequestFormType extends AbstractType
{

    private $project;

    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    public function buildForm(FormBuilder $builder, array $options)
    {
        $project = $this->project;
        $builder
            ->add('title', 'text')
            ->add('description', 'textarea')
            ->add('sourceBranch', 'entity', array(
                'property' => 'name',
                'class' => 'OpensoftCodeConversationBundle:Branch',
                'query_builder' => function(EntityRepository $repo) use ($project) {
                    return $repo->createQueryBuilder('b')
                            ->join('b.remote', 'r')
                            ->where('r.project = :project')->setParameter('project', $project)
                            ->orderBy('r.name, b.name');
                }
            ))
            ->add('destinationBranch', 'entity', array(
                'property' => 'name',
                'class' => 'OpensoftCodeConversationBundle:Branch',
                'query_builder' => function(EntityRepository $repo) use ($project) {
                    return $repo->createQueryBuilder('b')
                            ->join('b.remote', 'r')
                            ->where('r.project = :project')->setParameter('project', $project)
                            ->orderBy('r.name, b.name');
                }
            ))
        ;
    }


    public function getDefaultOptions(array $options)
    {
        return array('data_class' => 'Opensoft\Bundle\CodeConversationBundle\Entity\PullRequest');
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
