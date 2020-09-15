<?php namespace Atlassian\Answer;

use Atlassian\ConfluenceClient;
use Atlassian\ConfluenceException;
use Atlassian\Constants;
use Atlassian\Question\Question;
use Atlassian\Question\QuestionService;

/**
 * Confluence Questions REST Service class
 *
 * @package Atlassian\Answer
 * @see https://docs.atlassian.com/confluence-questions/rest/resource_AnswerResource.html
 */
class AnswerService extends ConfluenceClient
{
    // override parent uri
    public $url = '/questions/' . Constants::QUESTION_REST_API_VERSION . '/answer';

    private $defaultParam = [
                    'limit' => 10,
                    'start' => 0,
                    ];

    /**
     * get answer list
     *
     * @param string $username the user who made the answers
     * @param array|null $paramArray
     * @return mixed
     * @throws \Atlassian\ConfluenceException
     */
    public function getAnswers($username, $paramArray = null)
    {
        if (empty($username))
        {
            throw new ConfluenceException('username must be set.! ');
        }

        // set default param
        if (empty($paramArray))
        {
            $paramArray = $this->defaultParam;
        }
        $paramArray['username'] = $username;

        $queryParam = '?' . http_build_query($paramArray);

        $ret = $this->exec($this->url . $queryParam, null);

        return $searchResults = $this->json_mapper->mapArray(
            json_decode($ret),  new \ArrayObject(), '\Atlassian\Answer\Answer'
        );
    }

    /**
     * Get a answer detail by its ID
     *
     * @param $answerId answer id
     *
     * @return Answer|null
     */
    public function getAnswerDetail($answerId)
    {
        if (empty($answerId))
        {
            throw new ConfluenceException('Answer id must be not null.! ');
        }

        $ret = $this->exec($this->url . '/' . $answerId, null);

        return $answer = $this->json_mapper->map(
            json_decode($ret),  new Answer()
        );
    }

    /**
     * getting related answer
     *
     * @param $answerId
     * @return Question|null
     * @throws ConfluenceException
     * @throws \JsonMapper_Exception
     */
    public function getQuestion($answerId)
    {
        $answer = $this->getAnswerDetail($answerId);

        $qs = new QuestionService();

        return $qs->getQuestionDetail($answer->questionId);
    }
}