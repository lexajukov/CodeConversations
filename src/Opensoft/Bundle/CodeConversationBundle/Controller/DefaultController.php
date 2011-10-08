<?php

namespace Opensoft\Bundle\CodeConversationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Opensoft\Bundle\CodeConversationBundle\Form\Type\PullRequestFormType;
use Opensoft\Bundle\CodeConversationBundle\Form\Type\CommentFormType;
use Opensoft\Bundle\CodeConversationBundle\Entity\PullRequest;
use Opensoft\Bundle\CodeConversationBundle\Entity\Comment;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     * @Template()
     */
    public function homepageAction()
    {
        $projects = $this->get('doctrine')->getEntityManager()->getRepository('OpensoftCodeConversationBundle:Project')->findAll();

        return array('projects' => $projects);
    }

    /**
     * @Route("/about")
     * @Template()
     */
    public function aboutAction()
    {
        return array();
    }
}
