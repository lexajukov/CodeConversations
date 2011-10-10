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

    /**
     * @Route("/user/{usernameCanonical}/stream")
     * @ParamConverter("user", class="OpensoftCodeConversationBundle:User")
     * @Template()
     */
    public function streamAction(User $user)
    {
        /** @var \Redpanda\Bundle\ActivityStreamBundle\Model\ActionManagerInterface $activityManager **/
        $activityManager = $this->container->get('activity_stream.action_manager');

        $data = $activityManager->findStreamByActor($user);

//        foreach ($data as $item) {
//            print_r(get_class($item));
//        }

        return array('user' => $user, 'stream' => $data);
    }
}
