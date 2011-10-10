<?php

namespace Opensoft\Bundle\CodeConversationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Opensoft\Bundle\CodeConversationBundle\Model\PullRequestManagerInterface;
use Opensoft\Bundle\CodeConversationBundle\Model\ProjectInterface;
use Opensoft\Bundle\CodeConversationBundle\Form\Type\PullRequestFormType;
use Opensoft\Bundle\CodeConversationBundle\Form\Type\PullRequestCommentFormType;
use Opensoft\Bundle\CodeConversationBundle\Model\PullRequestInterface;
use Opensoft\Bundle\CodeConversationBundle\Model\PullRequest;
use Opensoft\Bundle\CodeConversationBundle\Model\PullRequestTimeline;
use Opensoft\Bundle\CodeConversationBundle\Entity\PullRequestComment;

/**
 * @ParamConverter("project", class="Opensoft\Bundle\CodeConversationBundle\Model\ProjectInterface")
 * @ParamConverter("pullRequest", class="Opensoft\Bundle\CodeConversationBundle\Model\PullRequestInterface")
 */
class PullRequestController extends Controller
{
    /**
     * @return \Opensoft\Bundle\CodeConversationBundle\Model\PullRequestManagerInterface
     */
    public function getPullRequestManager()
    {
        return $this->container->get('opensoft_codeconversation.manager.pull_request');
    }

    /**
     * @Route("/project/{slug}/pulls/new")
     * @Template()
     */
    public function createAction(ProjectInterface $project)
    {
        $pullRequestManager = $this->getPullRequestManager();

        $pullRequest = $pullRequestManager->createPullRequest();
        $pullRequest->setProject($project);
        $pullRequest->setCreatedAt(new \DateTime());
        $pullRequest->setInitiatedBy($this->container->get('security.context')->getToken()->getUser());
        $pullRequest->setStatus(PullRequest::STATUS_OPEN);

        $form = $this->createForm(new PullRequestFormType($project), $pullRequest);

        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);

            if ($form->isValid()) {

                $pullRequestManager->updatePullRequest($pullRequest);

                $this->get('session')->setFlash('success', 'Pull request engaged.');

                return $this->redirect($this->generateUrl('opensoft_codeconversation_pullrequest_view', array('pullId' => $pullRequest->getId(), 'slug' => $project->getSlug())));
            }
        }

        return array('project' => $project, 'form' => $form->createView());
    }

    /**
     * @Route("/project/{slug}/pulls")
     * @Template()
     */
    public function listAction(ProjectInterface $project)
    {
        return array('project' => $project, 'pullRequests' => $this->getPullRequestManager()->findPullRequestBy(array('project' => $project->getId()), array('createdAt' => 'DESC')));
    }

    /**
     * @Route("/project/{slug}/pull/{pullId}")
     * @Template()
     */
    public function viewAction(ProjectInterface $project, PullRequestInterface $pullRequest)
    {
        $em = $this->getDoctrine()->getEntityManager();

        /** @var \Opensoft\Bundle\CodeConversationBundle\SourceCode\RepositoryInterface $sourceCodeRepo  */
        $sourceCodeRepo = $this->get('opensoft_codeconversation.source_code.repository');
        $sourceCodeRepo->init($project);
//
//        print_r($project->getName());
//        die();

        $mergeBase = $sourceCodeRepo->mergeBase($pullRequest->getSourceBranch()->getName(), $pullRequest->getDestinationBranch()->getName());
//        print_r($mergeBase);
//        die();
        $fullDiff = $sourceCodeRepo->unifiedDiff($mergeBase, $pullRequest->getSourceBranch()->getName());
//        $commits = $sourceCodeRepo->fetchCommits($mergeBase, $pullRequest->getSourceBranch()->getName());

//        $timeline = $pullRequest->getEventTimeline();

//        $timeline = new PullRequestTimeline();
//        foreach ($commits as $commit) {
//            $timeline->add($commit->getTimestamp(), $commit);
//        }
//        foreach ($pullRequest->getComments() as $comment)
//        {
//            $timeline->add($comment->getCreatedAt(), $comment);
//        }


        /** @var \Redpanda\Bundle\ActivityStreamBundle\Entity\ActionManager $activityManager  */
        $activityManager = $this->container->get('activity_stream.action_manager');
        $stream = $activityManager->findStreamByTarget($pullRequest);
        $activityManager->


        /** @var \Gedmo\Loggable\Entity\LogEntry[] $logs  */
//        $logs = $em->getRepository('StofDoctrineExtensionsBundle:LogEntry')->findBy(array('objectClass' => get_class($pullRequest), 'objectId' => $pullRequest->getId()));
//        foreach ($logs as $logEntry) {
//            $timeline->insert($logEntry);
//        }

        $form = $this->createForm(new PullRequestCommentFormType(), new PullRequestComment());

        return array(
            'project' => $project,
            'pullRequest' => $pullRequest,
            'form' => $form->createView(),
            'fullDiff' => $fullDiff,
            'stream' => $stream,
//            'commits' => $commits,
//            'timeline' => $timeline
        );
    }
}
