<?php
/*
 *
 */

namespace Opensoft\Bundle\CodeConversationBundle\Request\ParamConverter;

use Opensoft\Bundle\CodeConversationBundle\Model\PullRequestManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;

/**
 *
 *
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 */
class PullRequestParamConverter implements ParamConverterInterface
{
    /**
     * @var \Opensoft\Bundle\CodeConversationBundle\Model\PullRequestManagerInterface
     */
    protected $pullRequestManager;

    /**
     * @var string
     */
    protected $class;

    /**
     * @param \Opensoft\Bundle\CodeConversationBundle\Model\PullRequestManagerInterface $pullRequestManager
     * @param string $class
     */
    public function __construct(PullRequestManagerInterface $pullRequestManager, $class)
    {
        $this->pullRequestManager = $pullRequestManager;
        $this->class = $class;
    }

    /**
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationInterface $configuration
     */
    function apply(Request $request, ConfigurationInterface $configuration)
    {
        if (!$request->attributes->has('pullId')) {
            return;
        }

        $pullId = $request->attributes->get('pullId');
        $project = $this->pullRequestManager->findPullRequestById($pullId);

        if (null === $project) {
            throw new NotFoundHttpException(sprintf('Pull request with pullId "%s" was not found.', $pullId));
        }

        $request->attributes->set($configuration->getName(), $project);
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
