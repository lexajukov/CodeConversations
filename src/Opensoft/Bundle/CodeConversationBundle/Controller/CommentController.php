<?php

namespace Opensoft\Bundle\CodeConversationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Opensoft\Bundle\CodeConversationBundle\Model\ProjectInterface;
use Opensoft\Bundle\CodeConversationBundle\Form\Type\PullRequestFormType;
use Opensoft\Bundle\CodeConversationBundle\Form\Type\CommitCommentFormType;
use Opensoft\Bundle\CodeConversationBundle\Form\Type\PullRequestCommentFormType;
use Opensoft\Bundle\CodeConversationBundle\Model\PullRequest;
use Opensoft\Bundle\CodeConversationBundle\Model\PullRequestInterface;
use Opensoft\Bundle\CodeConversationBundle\Entity\PullRequestComment;
use Opensoft\Bundle\CodeConversationBundle\Entity\CommitComment;

/**
 * @ParamConverter("project", class="Opensoft\Bundle\CodeConversationBundle\Model\ProjectInterface")
 */
class CommentController extends Controller
{
    /**
     * @Route("/{projectSlug}/pull/{pullId}/comment/new")
     * @Method("POST")
     * @ParamConverter("pullRequest", class="Opensoft\Bundle\CodeConversationBundle\Model\PullRequestInterface")
     * @Template("OpensoftCodeConversationsBundle:Default:viewPullRequest")
     */
    public function postPrCommentAction(ProjectInterface $project, PullRequestInterface $pullRequest)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $comment = new PullRequestComment();
        $comment->setPullRequest($pullRequest);
        $comment->setCreatedAt(new \DateTime());
        $comment->setAuthor($this->container->get('security.context')->getToken()->getUser());

        /** @var \Symfony\Component\Form\Form $form  */
        $form = $this->createForm(new PullRequestCommentFormType(), $comment);

        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);

            if ($form->isValid()) {

                /** @var \Redpanda\Bundle\ActivityStreamBundle\Entity\ActionManager $activityManager  */
                $activityManager = $this->container->get('activity_stream.action_manager');

                if ($request->get('close')) {
                    $pullRequest->setStatus(PullRequest::STATUS_CLOSED);
                    $em->persist($pullRequest);
                    $this->get('session')->setFlash('error', 'This pull request was closed.');
                    $activityManager->send("closed", $pullRequest);
                } elseif ($request->get('reopen')) {
                    $pullRequest->setStatus(PullRequest::STATUS_OPEN);
                    $em->persist($pullRequest);
                    $this->get('session')->setFlash('success', 'This pull request was reopened.');
                    $activityManager->send("reopened", $pullRequest);
                } else {
                    $this->get('session')->setFlash('success', 'Your comment was added to this pull request.');
                    $activityManager->send("commented on", $pullRequest);
                }

                $em->persist($comment);
                $em->flush();

                return $this->redirect($this->generateUrl('opensoft_codeconversation_pullrequest_view', array('pullId' => $pullRequest->getId(), 'projectSlug' => $project->getSlug())));
            }
        }

        return array('project' => $project, 'pullRequest' => $pullRequest, 'form' => $form->createView());
    }


    /**
     * @Route("/{projectSlug}/commit/{sha1}/comment/new")
     * @Method("POST")
     * @Template("OpensoftCodeConversationsBundle:Default:viewComment")
     */
    public function postCommitCommentAction(ProjectInterface $project, $sha1)
    {
        /** @var \Opensoft\Bundle\CodeConversationBundle\Git\RepositoryManager $repositoryManager  */
        $repositoryManager = $this->get('opensoft_codeconversation.repository_manager');
        $repository = $repositoryManager->getRepository($project);

        $commit = $repository->showCommit($sha1);

        if (!$commit) {
            throw $this->createNotFoundException("Commit $sha1 could not be found");
        }

        $comment = new CommitComment();
        $comment->setCommitSha1($sha1);
        $comment->setCreatedAt(new \DateTime());
        $comment->setProject($project);
        $comment->setAuthor($this->container->get('security.context')->getToken()->getUser());

        /** @var \Symfony\Component\Form\Form $form  */
        $form = $this->createForm(new CommitCommentFormType(), $comment);

        $request = $this->getRequest();
        $em = $this->getDoctrine()->getEntityManager();
        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);

            if ($form->isValid()) {
                $em->persist($comment);
                $em->flush();

                /** @var \Redpanda\Bundle\ActivityStreamBundle\Entity\ActionManager $activityManager  */
                $activityManager = $this->container->get('activity_stream.action_manager');
                $activityManager->send('commented on', $project, $comment);

                $this->get('session')->setFlash('success', 'Your comment was added to commit '.$sha1.'.');

                return $this->redirect($this->generateUrl('opensoft_codeconversation_project_viewcommit', array('sha1' => $sha1, 'projectSlug' => $project->getSlug())));
            }
        }
        $comments = $em->getRepository('OpensoftCodeConversationBundle:CommitComment')->findBy(array('commitSha1' => $sha1));

        return array('commit' => $commit, 'project' => $project, 'form' => $form->createView(), 'comments' => $comments);
    }

}
