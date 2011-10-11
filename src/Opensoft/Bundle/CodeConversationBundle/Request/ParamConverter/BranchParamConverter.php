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
 * Requires projectSlug, remoteSlug and branchSlug to be set
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
        if (!$request->attributes->has('projectSlug') || !$request->attributes->has('remoteSlug') || !$request->attributes->has('branchSlug')) {
            return;
        }

        $projectSlug = $request->attributes->get('projectSlug');
        $remoteSlug = $request->attributes->get('remoteSlug');
        $branchSlug = $request->attributes->get('branchSlug');
        $branch = $this->branchManager->findRemoteByProjectSlugAndRemoteSlugAndBranchSlug($projectSlug, $remoteSlug, $branchSlug);

        if (null === $branch) {
            throw new NotFoundHttpException(sprintf('Branch with slug "%s" on remote "%s" was not found for project "%s%', $branchSlug, $remoteSlug, $projectSlug));
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
