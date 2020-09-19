<?php namespace Atlassian\Question;

class Question
{
    public $id;

    public $title;

    public $url;

    /**
     * @var \Atlassian\Question\Author 
     */
    public $author;

    public $friendlyDateAsked;

    public $dateAsked;

    public $answersCount;

    /**
     * @var \Atlassian\Question\Topic[] 
     */
    public $topics;

    /**
     * @var integer 
     */
    public $acceptedAnswerId;
}
