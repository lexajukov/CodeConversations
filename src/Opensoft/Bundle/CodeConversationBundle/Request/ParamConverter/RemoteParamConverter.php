<?php
/*
 *
 */

namespace Opensoft\Bundle\CodeConversationBundle\Request\ParamConverter;

use Opensoft\Bundle\CodeConversationBundle\Model\RemoteManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;

/**
 * Requires projectSlug and remoteSlug to be set
 *
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 */
class RemoteParamConverter implements ParamConverterInterface
{
    /**
     * @var \Opensoft\Bundle\CodeConversationBundle\Model\RemoteManagerInterface
     */
    protected $remoteManager;

    /**
     * @var string
     */
    protected $class;

    /**
     * @param \Opensoft\Bundle\CodeConversationBundle\Model\RemoteManagerInterface $remoteManager
     * @param string $class
     */
    public function __construct(RemoteManagerInterface $remoteManager, $class)
    {
        $this->remoteManager = $remoteManager;
        $this->class = $class;
    }

    /**
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationInterface $configuration
     */
    function apply(Request $request, ConfigurationInterface $configuration)
    {
        if (!$request->attributes->has('projectSlug') || !$request->attributes->has('remoteSlug')) {
            return;
        }

        $projectSlug = $request->attributes->get('projectSlug');
        $remoteSlug = $request->attributes->get('remoteSlug');
        $remote = $this->remoteManager->findRemoteByProjectSlugAndRemoteSlug($projectSlug, $remoteSlug);

        if (null === $remote) {
            throw new NotFoundHttpException(sprintf('Remote with slug "%s" was not found for project "%s"', $remoteSlug, $projectSlug));
        }

        $request->attributes->set($configuration->getName(), $remote);
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
