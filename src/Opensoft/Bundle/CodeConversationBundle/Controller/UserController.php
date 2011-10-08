<?php

namespace Opensoft\Bundle\CodeConversationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Opensoft\Bundle\CodeConversationBundle\Entity\User;

class UserController extends Controller
{
    /**
     * @Route("/user/{usernameCanonical}")
     * @ParamConverter("user", class="OpensoftCodeConversationBundle:User")
     * @Template()
     */
    public function showAction(User $user)
    {
        return array('user' => $user);
    }
}
