<?php
/*
 *
 */

namespace Opensoft\Bundle\CodeConversationBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use FOS\UserBundle\Model\UserManagerInterface;

/**
 *
 *
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 */
class UserAdmin extends Admin
{
    /**
     * @var \FOS\UserBundle\Model\UserManagerInterface
     */
    protected $userManager;

    public function configureShowField(ShowMapper $showMapper)
    {
        $showMapper
            ->add('username')
            ->add('email')
            ->add('gitAlias')
            ->add('gravatar')
        ;
    }

    public function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('username')
            ->add('email')
            ->add('gitAlias')
            ->add('gravatar')
            ->add('roles', 'string', array('template' => 'SonataAdminBundle:CRUD:list_array.html.twig'))
//            ->add('roles', null)
            ->add('locked', 'boolean')
            ->add('expired', 'boolean')
            ->add('enabled', 'boolean')
            ->add('credentialsExpired', 'boolean')
            ->add('_action', 'actions', array(
                'actions' => array(
                    'view' => array(),
                    'edit' => array(),
                    'delete' => array(),
                )
            ))
        ;
    }

    public function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('General')
                ->add('username')
                ->add('email')
                ->add('gitAlias')
                ->add('gravatar')
                ->add('plainPassword', 'text')
            ->end()
//            ->with('Groups')
//                ->add('groups', 'sonata_type_model', array('required' => false))
//            ->end()
            ->with('Management')
//                ->add('roles', 'sonata_security_roles', array( 'multiple' => true))
                ->add('locked', null, array('required' => false))
                ->add('expired', null, array('required' => false))
                ->add('enabled', null, array('required' => false))
                ->add('credentialsExpired', null, array('required' => false))
            ->end()

            ->setHelps(array(
                'locked' => $this->trans('Lock the user from temporarily logging in'),
                'gitAlias' => $this->trans('The git author name to map to'),
                'gravatar' => $this->trans('If you have an avatar at gravatar.com, specify you gravatar email address')
            ))
        ;
    }

    /**
     * @param $user
     */
    public function preUpdate($user)
    {
        $this->getUserManager()->updateCanonicalFields($user);
        $this->getUserManager()->updatePassword($user);
    }

    /**
     * @param \FOS\UserBundle\Model\UserManagerInterface $userManager
     */
    public function setUserManager(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * @return \FOS\UserBundle\Model\UserManagerInterface
     */
    public function getUserManager()
    {
        return $this->userManager;
    }
}
