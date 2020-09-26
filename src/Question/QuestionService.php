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
    public string $url = '/questions/' . Constants::QUESTION_REST_API_VERSION . '/';

    /**
     * @var (int|string)[]
     *
     * @psalm-var array{limit: int, start: int, filter: string}
     */
    private array $defaultParam = [
                    'limit' => 10,
                    'start' => 0,
                    'filter' => 'recent',
                    ];

    private $accceptedAnswerId = null;

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
}