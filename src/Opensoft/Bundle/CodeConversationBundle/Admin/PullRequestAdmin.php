<?php
/*
 *
 */

namespace Opensoft\Bundle\CodeConversationBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

/**
 *
 *
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 */
class PullRequestAdmin extends Admin
{
    public function configureShowField(ShowMapper $showMapper)
    {
        $showMapper
            ->add('status')
            ->add('title')
            ->add('mergeBase')
            ->add('description')
            ->add('createdAt')
            ->add('comments')
            ->add('project')
            ->add('baseBranch')
            ->add('headBranch')
            ->add('author')
        ;
    }

    public function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id', null, array('identifier' => true))
            ->add('status')
            ->add('title')
            ->add('mergeBase')
            ->add('description')
            ->add('createdAt')
            ->add('comments')
            ->add('project')
            ->add('baseBranch')
            ->add('headBranch')
            ->add('author')
        ;
    }

    public function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('status')
            ->add('title')
            ->add('mergeBase')
            ->add('description')
            ->add('createdAt')
            ->add('comments')
            ->add('project')
            ->add('baseBranch')
            ->add('headBranch')
            ->add('author')
        ;
    }
}
