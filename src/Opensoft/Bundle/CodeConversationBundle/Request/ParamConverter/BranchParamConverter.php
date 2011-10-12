<?php
/*
 *
 */

namespace Opensoft\Bundle\CodeConversationBundle\Request\ParamConverter;

use Opensoft\Bundle\CodeConversationBundle\Model\BranchManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;

/**
 * Requires projectName, remoteName and branchName to be set
 *
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 */
class BranchParamConverter implements ParamConverterInterface
{
    /**
     * @var \Opensoft\Bundle\CodeConversationBundle\Model\BranchManagerInterface
     */
    protected $branchManager;

    /**
     * @var string
     */
    protected $class;

    /**
     * @param \Opensoft\Bundle\CodeConversationBundle\Model\BranchManagerInterface $branchManager
     * @param string $class
     */
    public function __construct(BranchManagerInterface $branchManager, $class)
    {
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
        if (!$request->attributes->has('projectName') || !$request->attributes->has('remoteName') || !$request->attributes->has('branchName')) {
            return;
        }

        $projectName = $request->attributes->get('projectName');
        $remoteName = $request->attributes->get('remoteName');
        $branchName = $request->attributes->get('branchName');
        $branch = $this->branchManager->findBranchByProjectNameAndRemoteNameAndBranchName($projectName, $remoteName, $branchName);

        if (null === $branch) {
            throw new NotFoundHttpException(sprintf('Branch with name "%s" on remote "%s" was not found for project "%s%', $branchName, $remoteName, $projectName));
        }

        $request->attributes->set($configuration->getName(), $branch);
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
