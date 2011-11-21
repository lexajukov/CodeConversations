<?php

namespace Opensoft\Bundle\CodeConversationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
     * @ParamConverter("pullRequest", class="Opensoft\Bundle\CodeConversationBundle\Model\PullRequestInterface")
     * @Template("OpensoftCodeConversationBundle:Default:viewPullRequest")
     */
    public function postPrCommentAction(ProjectInterface $project, PullRequestInterface $pullRequest)
    {
        /** @var \Doctrine\ORM\EntityManager $em  */
        $em = $this->getDoctrine()->getEntityManager();

        $comment = new PullRequestComment();
        $comment->setPullRequest($pullRequest);
        $comment->setCreatedAt(new \DateTime());
        $commentAuthor = $this->container->get('security.context')->getToken()->getUser();
        $comment->setAuthor($commentAuthor);

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

                    $verb = 'closed';
                    $activityManager->send($verb, $pullRequest, $pullRequest->getProject());
                } elseif ($request->get('reopen')) {
                    $pullRequest->setStatus(PullRequest::STATUS_OPEN);
                    $em->persist($pullRequest);

                    $this->get('session')->setFlash('success', 'This pull request was reopened.');

                    $verb = 'reopened';
                    $activityManager->send($verb, $pullRequest, $pullRequest->getProject());
                } else {
                    $this->get('session')->setFlash('success', 'Your comment was added to this pull request.');
                    
                    $verb = 'commented on';
                    $activityManager->send($verb, $pullRequest, $pullRequest->getProject());
                }

                $em->persist($comment);
                $em->flush();

                // find users to notify about comments to this pull request
                $participants[$pullRequest->getAuthor()->getId()] = $pullRequest->getAuthor();

                $commenters = $em->getRepository('OpensoftCodeConversationBundle:PullRequestComment')->findBy(array('pullRequest' => $pullRequest->getId()));
                foreach ($commenters as $commenter) {
                    $author = $commenter->getAuthor();
                    if (!isset($participants[$author->getId()])) {
                        $participants[$author->getId()] = $author;
                    }
                }
                // exclude pull request comment author (logged in user.. they already know they're making this comment)
                if (isset($participants[$commentAuthor->getId()])) {
                    unset($participants[$commentAuthor->getId()]);
                }

                // only send if we actually have people to notify
                if (!empty($participants)) {
                    /** @var \Opensoft\Bundle\CodeConversationBundle\Notification\Notifier $notifier  */
                    $notifier = $this->container->get('opensoft_codeconversation.notifier');
                    $notifier->notify($participants,
                        sprintf("%s %s %s pull request %d", $commentAuthor->getUsername(), $verb, $project->getName(), $pullRequest->getId()),
                        'OpensoftCodeConversationBundle:Email:comment.html.twig',
                        array(
                            'author' => $commentAuthor,
                            'object' => 'pull request '.$pullRequest->getId(),
                            'comment' => $comment->getContent(),
                            'url' => $this->generateUrl('opensoft_codeconversation_pullrequest_view', array('pullId' => $pullRequest->getId(), 'projectName' => $project->getName()), true)
                        )
                    );
                }

                return $this->redirect($this->generateUrl('opensoft_codeconversation_pullrequest_view', array('pullId' => $pullRequest->getId(), 'projectName' => $project->getName())));
            }
        }

        return array('project' => $project, 'pullRequest' => $pullRequest, 'form' => $form->createView());
    }


    /**
     * @Template("OpensoftCodeConversationBundle:Default:viewComment")
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
        $commentAuthor = $this->container->get('security.context')->getToken()->getUser();
        $comment->setAuthor($commentAuthor);

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
                $activityManager->send('commented on', $comment, $project);

                $this->get('session')->setFlash('success', 'Your comment was added to commit '.$sha1.'.');



                // find users to notify about comments to this commit
                $commitAuthor = $em->getRepository('OpensoftCodeConversationBundle:User')->findOneByGitAlias($commit->getAuthorName());
                $commitCommitter = $em->getRepository('OpensoftCodeConversationBundle:User')->findOneByGitAlias($commit->getCommitterName());

                $participants[$commitAuthor->getId()] = $commitAuthor;
                $participants[$commitCommitter->getId()] = $commitCommitter;

                $commenters = $em->getRepository('OpensoftCodeConversationBundle:CommitComment')->findBy(array('commitSha1' => $sha1));
                foreach ($commenters as $commenter) {
                    $author = $commenter->getAuthor();
                    if (!isset($participants[$author->getId()])) {
                        $participants[$author->getId()] = $author;
                    }
                }
                // exclude commit comment author (logged in user.. they already know they're making this comment)
                if (isset($participants[$commentAuthor->getId()])) {
                    unset($participants[$commentAuthor->getId()]);
                }

                // only send if we actually have people to notify
                if (!empty($participants)) {
                    /** @var \Opensoft\Bundle\CodeConversationBundle\Notification\Notifier $notifier  */
                    $notifier = $this->container->get('opensoft_codeconversation.notifier');
                    $notifier->notify($participants,
                        sprintf("%s commented on %s commit %s", $commentAuthor->getUsername(), $project->getName(), $sha1),
                        'OpensoftCodeConversationBundle:Email:comment.html.twig',
                        array(
                            'author' => $commentAuthor,
                            'object' => sprintf("commit %s", $sha1),
                            'comment' => $comment->getContent(),
                            'url' => $this->generateUrl('opensoft_codeconversation_project_viewcommit', array('sha1' => $sha1, 'projectName' => $project->getName()), true)
                        )
                    );
                }


                return $this->redirect($this->generateUrl('opensoft_codeconversation_project_viewcommit', array('sha1' => $sha1, 'projectName' => $project->getName())));
            }
        }
        $comments = $em->getRepository('OpensoftCodeConversationBundle:CommitComment')->findBy(array('commitSha1' => $sha1));

        return array('commit' => $commit, 'project' => $project, 'form' => $form->createView(), 'comments' => $comments);
    }

}
