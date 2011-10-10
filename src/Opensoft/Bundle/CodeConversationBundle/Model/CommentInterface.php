<?php
/*
 *
 */

namespace Opensoft\Bundle\CodeConversationBundle\Model;

use Redpanda\Bundle\ActivityStreamBundle\Streamable\StreamableInterface;

/**
 *
 *
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 */
interface CommentInterface extends StreamableInterface
{
    /**
     * @param UserInterface $author
     */
    public function setAuthor(UserInterface $author);

    /**
     * @return UserInterface
     */
    public function getAuthor();

    /**
     * @param string $content
     */
    public function setContent($content);

    /**
     * @return string
     */
    public function getContent();

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt);

    /**
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * @param int $id
     */
    public function setId($id);
}
