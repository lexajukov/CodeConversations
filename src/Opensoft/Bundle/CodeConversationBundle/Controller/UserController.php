<?php

namespace Opensoft\Bundle\CodeConversationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Opensoft\Bundle\CodeConversationBundle\Entity\User;

class UserController extends Controller
{
    /**
     * @Template()
     */
    public function showAction(User $user)
    {
        /** @var \Redpanda\Bundle\ActivityStreamBundle\Model\ActionManagerInterface $activityManager **/
        $activityManager = $this->container->get('activity_stream.action_manager');
        $data = $activityManager->findStreamByActor($user);
        
        return array('user' => $user, 'stream' => $data);
    }
}
