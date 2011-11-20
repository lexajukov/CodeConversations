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
class RemoteAdmin extends Admin
{
    public function configureShowField(ShowMapper $showMapper)
    {
        $showMapper
            ->add('name')
            ->add('url')
            ->add('username')
            ->add('password')
            ->add('branches')
            ->add('project')
            ->add('headBranch')
        ;
    }

    public function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id', null, array('identifier' => true))
            ->add('name')
            ->add('url')
            ->add('username')
            ->add('password')
            ->add('branches')
            ->add('project')
            ->add('headBranch')
        ;
    }

    public function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name')
            ->add('url')
            ->add('username')
            ->add('password')
            ->add('branches')
            ->add('project')
            ->add('headBranch')
        ;
    }
}
