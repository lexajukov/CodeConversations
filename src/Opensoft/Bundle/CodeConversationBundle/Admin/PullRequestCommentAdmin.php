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

/**
 *
 *
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 */
class PullRequestCommentAdmin extends Admin
{
    public function configureShowField(ShowMapper $showMapper)
    {
        $showMapper
            ->add('content')
            ->add('createdAt')
            ->add('author')
            ->add('pullRequest')
        ;
    }

    public function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('pullRequest')
            ->add('author')
            ->add('createdAt')
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
            ->add('pullRequest')
        ;
    }

    public function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('content')
            ->add('createdAt')
            ->add('author')
            ->add('pullRequest')
        ;
    }
}
