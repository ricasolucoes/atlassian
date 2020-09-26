<?php

namespace Atlassian\Page;


class Page
{
    /**
     * @var integer
     */
    public $id;

    /**
     * @var string 
     */
    public $type;

    /**
     * @var string 
     */
    public $title;

    /**
     * @var \Atlassian\Page\Attachment[] 
     */
    public $attachments;

    /**
     * @var array 
     */
    public $children;
}