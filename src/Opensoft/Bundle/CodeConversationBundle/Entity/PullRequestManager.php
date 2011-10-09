<?php
/*
 *
 */

namespace Opensoft\Bundle\CodeConversationBundle\Entity;

use Opensoft\Bundle\CodeConversationBundle\Model\PullRequestManager as BasePullRequestManager;
use Opensoft\Bundle\CodeConversationBundle\Model\PullRequestInterface;
use Opensoft\Bundle\CodeConversationBundle\Git\Builder;
use Symfony\Component\Validator\Constraint;
use Doctrine\ORM\EntityManager;

/**
 * Entity project manager
 *
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 */
class PullRequestManager extends BasePullRequestManager
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var string
     */
    protected $class;

    /**
     * @var \Doctrine\ORM\EntityRepository
     */
    protected $repository;

    /**
     * @param \Opensoft\Bundle\CodeConversationBundle\Git\Builder $builder
     * @param \Doctrine\ORM\EntityManager $em
     * @param string $class
     */
    public function __construct(Builder $builder, EntityManager $em, $class)
    {
        parent::__construct($builder);

        $this->em = $em;
        $this->repository = $em->getRepository($class);
        $metadata = $em->getClassMetadata($class);
        $this->class = $metadata->name;
    }

    /**
     * @param integer $id
     * @return \Opensoft\Bundle\CodeConversationBundle\Entity\PullRequest
     */
    public function findPullRequestById($id)
    {
        return $this->repository->findOneBy(array('id' => $id));
    }

    /**
     * @return \Opensoft\Bundle\CodeConversationBundle\Entity\PullRequest[]
     */
    public function findPullRequests()
    {
        return $this->repository->findAll();
    }

    /**
     * @param \Opensoft\Bundle\CodeConversationBundle\Model\PullRequestInterface $pullRequest
     */
    public function deletePullRequest(PullRequestInterface $pullRequest)
    {
        $this->em->remove($pullRequest);
        $this->em->flush();
    }

    /**
     * @param \Opensoft\Bundle\CodeConversationBundle\Model\PullRequestInterface $pullRequest
     * @param Boolean $andFlush
     */
    public function updatePullRequest(PullRequestInterface $pullRequest, $andFlush = true)
    {
        $this->em->persist($pullRequest);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param $value
     * @param \Symfony\Component\Validator\Constraint $constraint
     * @return boolean
     */
    public function validateUnique($value, Constraint $constraint)
    {
        // TODO: Implement validateUnique() method.
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

}
