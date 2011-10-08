<?php
/*
 *
 */

namespace Opensoft\Bundle\CodeConversationBundle\Twig;

use Symfony\Bundle\DoctrineBundle\Registry;

/**
 *
 *
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 */
class GitAuthorAliasExtension extends \Twig_Extension
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    private static $cachedUserLookup = array();

    public function __construct(Registry $doctrineRegistry)
    {
        $this->em = $doctrineRegistry->getEntityManager();
    }

    public function getFunctions()
    {
        return array(
            'git_author' => new \Twig_Function_Method($this, 'getGitAuthor'),
            'git_author_exists' => new \Twig_Function_Method($this, 'exists'),
        );
    }

    public function exists($authorName)
    {
        if (!array_key_exists($authorName, self::$cachedUserLookup)) {
            self::$cachedUserLookup[$authorName] = $this->em->getRepository('OpensoftCodeConversationBundle:User')->findOneBy(array('gitAlias' => $authorName));
        }

        return null !== self::$cachedUserLookup[$authorName];
    }

    public function getGitAuthor($authorName)
    {
        if (!isset(self::$cachedUserLookup[$authorName])) {
            self::$cachedUserLookup[$authorName] = $this->em->getRepository('OpensoftCodeConversationBundle:User')->findOneBy(array('gitAlias' => $authorName));
        } 
        
        return self::$cachedUserLookup[$authorName];
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    function getName()
    {
        return 'git_author_alias';
    }

}
