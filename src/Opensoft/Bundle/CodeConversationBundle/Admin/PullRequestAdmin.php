<?php
/*
 *
 */

namespace Opensoft\Bundle\CodeConversationBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Opensoft\Bundle\CodeConversationBundle\Model\PullRequest;

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
            ->add('getStatusCode', null, array('label' => 'Status'))
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
            ->addIdentifier('id')
            ->add('project')
            ->add('status_code', 'text', array('label' => 'Status', 'sortable' => 'status'))
            ->add('title')
            ->add('baseBranch')
            ->add('headBranch')
            ->add('mergeBase')
            ->add('createdAt')
            ->add('comments')
            ->add('author')
            ->add('_action', 'actions', array(
                'actions' => array(
                    'view' => array(),
                    'edit' => array(),
                    'delete' => array(),
                )
            ))
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagrid)
    {
        $datagrid
            ->add('project')
            ->add('status', 'doctrine_orm_choice', array(), 'choice', array('choices' => PullRequest::getStatusList()))
            ->add('baseBranch')
            ->add('headBranch')
            ->add('author')
        ;
    }

    public function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('status', 'choice', array('choices' => PullRequest::getStatusList()))
            ->add('title')
            ->add('mergeBase')
            ->add('description')
            ->add('createdAt')
            ->add('project')
            ->add('baseBranch')
            ->add('headBranch')
            ->add('author')
        ;
    }
}
