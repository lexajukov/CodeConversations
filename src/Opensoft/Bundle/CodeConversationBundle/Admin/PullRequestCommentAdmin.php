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
            ->add('id', null, array('identifier' => true))
            ->add('content')
            ->add('createdAt')
            ->add('author')
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
