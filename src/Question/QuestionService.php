<?php namespace Atlassian\Question;

use Atlassian\Answer\Answer;
use Atlassian\Answer\AnswerService;
use Atlassian\ConfluenceClient;
use Atlassian\ConfluenceException;

use Atlassian\Constants;

/**
 * Confluence Questions REST Service class
 *
 * @package Atlassian\Question
 */
class QuestionService extends ConfluenceClient
{
    // override parent uri
    public $url = '/questions/' . Constants::QUESTION_REST_API_VERSION . '/';

    private $defaultParam = [
                    'limit' => 10,
                    'start' => 0,
                    'filter' => 'recent',
                    ];

    private $accceptedAnswerId = null;

    /**
     * get question list
     *
     * @param  null $paramArray
     * @return mixed
     * @throws \Atlassian\ConfluenceException
     */
    public function getQuestion($paramArray = null)
    {
        // set default param
        if (empty($paramArray)) {
            $paramArray = $this->defaultParam;
        }

        $queryParam = 'question?' . http_build_query($paramArray);

        $ret = $this->exec($this->url . $queryParam, null);

        return $searchResults = $this->json_mapper->mapArray(
            json_decode($ret),  new \ArrayObject(), '\Atlassian\Question\Question'
        );
    }

    /**
     * Get a question by its ID
     *
     * @param $questionId question id
     * @param int|null $questionId
     *
     * @return Question|null
     */
    public function getQuestionDetail(?int $questionId)
    {
        // clear old value
        $this->accceptedAnswerId = null;

        if (empty($questionId)) {
            throw new ConfluenceException('Question id must be not null.! ');
        }

        $ret = $this->exec($this->url . 'question/' . $questionId, null);

        $question = $this->json_mapper->map(
            json_decode($ret),  new Question()
        );

        $this->accceptedAnswerId = $question->accceptedAnswerId;

        return $question;
    }

    /**
     * Get a accepted answer
     *
     * @param  $questionId
     * @return Answer|null
     */
    public function getAcceptedAnswer($questionId)
    {
        $question = $this->getQuestionDetail($questionId);

        if (empty($question) || empty($question->acceptedAnswerId)) {
            return null;
        }

        $as = new AnswerService();

        return $as->getAnswerDetail($question->acceptedAnswerId);
    }

    /**
     * determine question has accepted answer
     *
     * @param  null $questionId
     * @return bool|null
     */
    public function hasAcceptedAnswer($questionId = null)
    {
        if ($questionId === null) {
            return !is_null($this->accceptedAnswerId) ? true : false;
        }

        $as = $this->getAcceptedAnswer($questionId);

        return !is_null($as) ? true : false;
    }
}