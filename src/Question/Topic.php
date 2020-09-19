<?php namespace Atlassian\Question;


class Topic
{
    /**
     * @var integer 
     */
    public $id;

    /**
     * @var string 
     */
    public $idAsString;

    /**
     * @var string 
     */
    public $url;

    /**
     * @var boolean 
     */
    public $featured;

    /**
     * @var boolean 
     */
    public $isWatching;
}