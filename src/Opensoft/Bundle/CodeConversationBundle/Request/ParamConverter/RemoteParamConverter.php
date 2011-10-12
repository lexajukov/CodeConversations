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
 * Requires projectName and remoteName to be set
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
        if (!$request->attributes->has('projectName') || !$request->attributes->has('remoteName')) {
            return;
        }

        $projectName = $request->attributes->get('projectName');
        $remoteName = $request->attributes->get('remoteName');
        $remote = $this->remoteManager->findRemoteByProjectNameAndRemoteName($projectName, $remoteName);

        if (null === $remote) {
            throw new NotFoundHttpException(sprintf('Remote with name "%s" was not found for project "%s"', $remoteName, $projectName));
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
