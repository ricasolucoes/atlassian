<?php namespace Atlassian\Answer;

class Answer
{
    /**
     * @var integer 
     */
    public $id;

    /**
     * @var integer 
     */
    public $questionId;

    /**
     * @var \Atlassian\Question\Author 
     */
    public $author;

    public $dateAnswered;

    public $friendlyDateAnswered;

    public $votes;

    public $comments;

    public $metadata;

    /**
     * @var boolean 
     */
    public $accepted;

    public $url;

    public $idAsString;

    /**
     * @var null|\Atlassian\Question\Question only accepted answer has question class 
     */
    public $question;
}