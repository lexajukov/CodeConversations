<?php

namespace Opensoft\Bundle\CodeConversationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Opensoft\Bundle\CodeConversationBundle\Form\Type\PullRequestFormType;
use Opensoft\Bundle\CodeConversationBundle\Entity\PullRequest;

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
     * @Route("/project/{id}")
     * @Template()
     */
    public function showProjectAction($id)
    {
        $project = $this->get('doctrine')->getEntityManager()->getRepository('OpensoftCodeConversationBundle:Project')->find($id);

        if (!$project) {
            throw $this->createNotFoundException("Project '$id' does not exist");
        }

        /** @var \Opensoft\Bundle\CodeConversationBundle\Git\Builder $builder  */
        $builder = $this->get('opensoft_codeconversation.git.builder');
        $builder->init($project);

        $recentCommits = $builder->fetchRecentCommits();
//        print_r($recentCommits);

        return array('project' => $project, 'recentCommits' => $recentCommits);
    }

    /**
     * @Route("/project/{projectId}/commit/{commitHash}")
     * @Template()
     */
    public function showCommitAction($projectId, $commitHash)
    {
        $project = $this->get('doctrine')->getEntityManager()->getRepository('OpensoftCodeConversationBundle:Project')->find($projectId);

        if (!$project) {
            throw $this->createNotFoundException("Project '$projectId' does not exist");
        }

        /** @var \Opensoft\Bundle\CodeConversationBundle\Git\Builder $builder  */
        $builder = $this->get('opensoft_codeconversation.git.builder');
        $builder->init($project);

        $commit = $builder->fetchCommit($commitHash);

//        $diff = $builder->diff($commitHash);

        return array('commit' => $commit, 'project' => $project);
    }

    /**
     * @Route("/project/{id}/pulls_create")
     * @Template()
     */
    public function createPullRequestAction($id)
    {
        $em = $this->get('doctrine')->getEntityManager();
        $project = $em->getRepository('OpensoftCodeConversationBundle:Project')->find($id);

        if (!$project) {
            throw $this->createNotFoundException("Project '$id' does not exist");
        }

        $pullRequest = new PullRequest();
        $pullRequest->setProject($project);
        $pullRequest->setCreatedAt(new \DateTime());
        $pullRequest->setInitiatedBy($this->container->get('security.context')->getToken()->getUser());
        $pullRequest->setStatus(PullRequest::STATUS_OPEN);

        $form = $this->createForm(new PullRequestFormType(), $pullRequest);

        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);

            if ($form->isValid()) {

                $em->persist($pullRequest);
                $em->flush();

                return $this->redirect($this->generateUrl('opensoft_codeconversation_default_viewpullrequest', array('pullId' => $pullRequest->getId(), 'projectId' => $project->getId())));
            }
        }

        return array('project' => $project, 'form' => $form->createView());
    }

    /**
     * @Route("/project/{projectId}/pulls/{pullId}")
     * @Template()
     */
    public function viewPullRequestAction($projectId, $pullId)
    {
        $em = $this->get('doctrine')->getEntityManager();
        $project = $em->getRepository('OpensoftCodeConversationBundle:Project')->find($projectId);

        if (!$project) {
            throw $this->createNotFoundException("Project '$projectId' does not exist");
        }

        $pullRequest = $em->getRepository('OpensoftCodeConversationBundle:PullRequest')->find($pullId);

        if (!$pullRequest) {
            throw $this->createNotFoundException("Pull request '$pullRequest' does not exist");
        }

        return array('project' => $project, 'pullRequest' => $pullRequest);
    }

    /**
     * @Route("/hello/{name}")
     * @Template()
     */
    public function indexAction($name)
    {
        return array('name' => $name);
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
