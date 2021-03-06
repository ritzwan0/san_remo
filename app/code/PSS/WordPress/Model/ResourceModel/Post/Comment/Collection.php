<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 *
 * This package designed for Magento COMMUNITY edition
 * PSS Digital does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * PSS Digital does not provide extension support in case of * incorrect edition usage.
 *
 * @author PSS Digital Team
 * @category PSS
 * @package PSS_WordPress
 * @copyright Copyright (c) 2018 PSS (https://www.pss-ti.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace PSS\WordPress\Model\ResourceModel\Post\Comment;

use PSS\WordPress\Model\ResourceModel\Meta\Collection\AbstractCollection;
use PSS\WordPress\Model\Post;

class Collection extends AbstractCollection
{
    /**
     * Name prefix of events that are dispatched by model
     *
     * @var string
     */
    protected $_eventPrefix = 'wordpress_post_comment_collection';

    /**
     * Name of event parameter
     *
     * @var string
     */
    protected $_eventObject = 'post_comments';

    /**
     * @var Post
     */
    protected $post;

    /**
     * Set the resource
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('PSS\WordPress\Model\Post\Comment', 'PSS\WordPress\Model\ResourceModel\Post\Comment');

        return parent::_construct();
    }

    /**
     * Order the comments by date
     *
     * @param string $dir = null
     * @return $this
     */
    public function addOrderByDate($dir = null)
    {
        if (is_null($dir)) {
            $dir = $this->optionManager->getOption('comment_order');
            $dir = in_array($dir, array('asc', 'desc')) ? $dir : 'asc';
        }

        $this->getSelect()->order('main_table.comment_date ' . $dir);

        return $this;
    }

    /**
     * Add parent comment filter
     *
     * @param int $parentId = 0
     * @return $this
     */
    public function addParentCommentFilter($parentId = 0)
    {
        return $this->addFieldToFilter('comment_parent', $parentId);
    }

    /**
     *
     */
    public function setPost(Post $post)
    {
        $this->post = $post;
        $this->addPostIdFilter($this->post->getId());

        return $this;
    }

    /**
     * Filters the collection of comments
     * so only comments for a certain post are returned
     * @param $postId
     * @return Collection
     */
    public function addPostIdFilter($postId)
    {
        return $this->addFieldToFilter('comment_post_ID', $postId);
    }

    /**
     * Filter the collection by a user's ID
     *
     * @param int $userId
     * @return $this
     */
    public function addUserIdFilter($userId)
    {
        return $this->addFieldToFilter('user_id', $userId);
    }

    /**
      * Filter the collection by the comment_author_email column
      *
      * @param string $email
      * @return $this
     */
    public function addCommentAuthorEmailFilter($email)
    {
        return $this->addFieldToFilter('comment_author_email', $email);
    }

    /*
      * Filters the collection so only approved comments are returned
      *
      * @return $this
     */
    public function addCommentApprovedFilter($status = 1)
    {
        return $this->addFieldToFilter('comment_approved', $status);
    }

    /*
     *
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        if ($this->post) {
            foreach ($this->getItems() as $comment) {
                $comment->setPost($this->post);
            }
        }

        return parent::_afterLoad();
    }
}
