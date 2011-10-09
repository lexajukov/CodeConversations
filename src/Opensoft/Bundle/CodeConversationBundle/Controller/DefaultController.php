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
        return array('projects' => $this->getProjectManager()->findProjects());
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
