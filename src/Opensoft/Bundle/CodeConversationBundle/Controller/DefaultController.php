<?php

namespace Opensoft\Bundle\CodeConversationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Template()
     */
    public function homepageAction()
    {
        /** @var \Redpanda\Bundle\ActivityStreamBundle\Model\ActionManagerInterface $activityManager **/
        $activityManager = $this->container->get('activity_stream.action_manager');
        $stream = $activityManager->findStreamBy(array(), array('createdAt' => 'DESC'), 50);

        return array(
            'projects' => $this->getProjectManager()->findProjectBy(array(), array('name' => 'ASC')),
            'siteStream' => $stream
        );
    }

    /**
     * @Template()
     */
    public function aboutAction()
    {
        return array('about' => file_get_contents(__DIR__.'/../Resources/doc/about.md'));
    }

    /**
     * @return \Opensoft\Bundle\CodeConversationBundle\Model\ProjectManagerInterface
     */
    public function getProjectManager()
    {
        return $this->get('opensoft_codeconversation.manager.project');
    }
}
