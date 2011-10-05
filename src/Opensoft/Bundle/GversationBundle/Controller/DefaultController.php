<?php

namespace Opensoft\Bundle\GversationBundle\Controller;

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
        $projects = $this->get('doctrine')->getEntityManager()->getRepository('OpensoftGversationBundle:Project')->findAll();

        return array('projects' => $projects);
    }

    /**
     * @Route("/project/{id}/pulls/create")
     * @Template()
     */
    public function createPullRequestAction($id)
    {
        $project = $this->get('doctrine')->getEntityManager()->getRepository('OpensoftGversationBundle:Project')->find($id);

        if (!$project) {
            throw $this->createNotFoundException("Project '$id' does not exist");
        }

        return array('project' => $project);
    }

    /**
     * @Route("/hello/{name}")
     * @Template()
     */
    public function indexAction($name)
    {
        return array('name' => $name);
    }
}
