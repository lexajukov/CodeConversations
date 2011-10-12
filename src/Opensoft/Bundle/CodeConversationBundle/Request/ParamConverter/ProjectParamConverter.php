<?php
/*
 *
 */

namespace Opensoft\Bundle\CodeConversationBundle\Request\ParamConverter;

use Opensoft\Bundle\CodeConversationBundle\Model\ProjectManagerInterface;
use Opensoft\Bundle\CodeConversationBundle\Model\RemoteManagerInterface;
use Opensoft\Bundle\CodeConversationBundle\Model\BranchManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;

/**
 *
 *
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 */
class ProjectParamConverter implements ParamConverterInterface
{
    /**
     * @var \Opensoft\Bundle\CodeConversationBundle\Model\ProjectManagerInterface
     */
    protected $projectManager;

    /**
     * @var \Opensoft\Bundle\CodeConversationBundle\Model\RemoteManagerInterface
     */
    protected $remoteManager;

    /**
     * @var \Opensoft\Bundle\CodeConversationBundle\Model\BranchManagerInterface
     */
    protected $branchManager;

    /**
     * @var string
     */
    protected $class;

    /**
     * @param \Opensoft\Bundle\CodeConversationBundle\Model\ProjectManagerInterface $projectManager
     * @param \Opensoft\Bundle\CodeConversationBundle\Model\RemoteManagerInterface $remoteManager
     * @param \Opensoft\Bundle\CodeConversationBundle\Model\BranchManagerInterface $branchManager
     * @param string $class
     */
    public function __construct(ProjectManagerInterface $projectManager, RemoteManagerInterface $remoteManager, BranchManagerInterface $branchManager, $class)
    {
        $this->projectManager = $projectManager;
        $this->remoteManager = $remoteManager;
        $this->branchManager = $branchManager;
        $this->class = $class;
    }

    /**
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationInterface $configuration
     */
    function apply(Request $request, ConfigurationInterface $configuration)
    {
        if (!$request->attributes->has('projectSlug')) {
            return;
        }

        $slug = $request->attributes->get('projectSlug');
        $project = $this->projectManager->findProjectBySlug($slug);

        if (null === $project) {
            throw new NotFoundHttpException(sprintf('Project with slug "%s" was not found.', $slug));
        }

        $request->attributes->set($configuration->getName(), $project);


        if ($request->attributes->has('remoteSlug')) {
            $slug = $request->attributes->get('remoteSlug');
            $remote = $this->remoteManager->findRemotesBy(array('project' => $project->getId(), 'slug' => $slug));
            $remote = $remote[0];

            if (null === $remote) {
                throw new NotFoundHttpException(sprintf('Remote with slug "%s" was not found.', $slug));
            }

            $request->attributes->set('remote', $remote);
        } else {
            $remote = $project->getDefaultRemote();
            $request->attributes->set('remote', $remote);
        }


        if ($request->attributes->has('branchSlug')) {
            $slug = $request->attributes->get('branchSlug');
            $branch = $this->branchManager->findBranchesBy(array('remote' => $remote->getId(), 'slug' => $slug));
            $branch = $branch[0];

            if (null === $remote) {
                throw new NotFoundHttpException(sprintf('Branch with slug "%s" was not found.', $slug));
            }

            $request->attributes->set('branch', $branch);
        } else {
            $branch = $remote->getHeadBranch();
            $request->attributes->set('branch', $branch);
        }


    }

    /**
     * @param \Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationInterface $configuration
     * @return bool
     */
    function supports(ConfigurationInterface $configuration)
    {
        return $configuration->getClass() === $this->class;
    }

}
