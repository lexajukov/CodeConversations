<?php

namespace Opensoft\Bundle\CodeConversationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     * @Template()
     */
    public function homepageAction()
    {
        /** @var \Redpanda\Bundle\ActivityStreamBundle\Model\ActionManagerInterface $activityManager **/
        $activityManager = $this->container->get('activity_stream.action_manager');
        $stream = $activityManager->findStreamBy(array(), array('createdAt' => 'DESC'));

        return array(
            'projects' => $this->getProjectManager()->findProjects(),
            'siteStream' => $stream
        );
    }

    /**
     * @Route("/about")
     * @Template()
     */
    public function aboutAction()
    {
        return array();
    }

    /**
     * @return \Opensoft\Bundle\CodeConversationBundle\Model\ProjectManagerInterface
     */
    public function getProjectManager()
    {
        return $this->get('opensoft_codeconversation.manager.project');
    }
}
