<?php

namespace Atlassian\Page;


class Page
{
    /**
     * @var integer
     */
    public $id;

    /** @var string */
    public $type;

    /** @var string */
    public $status;

    /** @var string */
    public $title;

    /** @var \Atlassian\Space\Space */
    public $space;

    /** @var \Atlassian\Page\History */
    public $history;

    /** @var \Atlassian\Page\Attachment[] */
    public $attachments;

    /** @var array */
    public $children;
}