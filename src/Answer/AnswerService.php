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
 * @see     https://docs.atlassian.com/confluence-questions/rest/resource_AnswerResource.html
 */
class AnswerService extends ConfluenceClient
{
    // override parent uri
    public string $url = '/questions/' . Constants::QUESTION_REST_API_VERSION . '/answer';

    /**
     * @var int[]
     *
     * @psalm-var array{limit: int, start: int}
     */
    private array $defaultParam = [
                    'limit' => 10,
                    'start' => 0,
                    ];

    /**
     * Get a answer detail by its ID
     *
     * @param $answerId answer id
     *
     * @return Answer|null
     */
    public function getAnswerDetail(int $answerId)
    {
        if (empty($answerId)) {
            throw new ConfluenceException('Answer id must be not null.! ');
        }

        $ret = $this->exec($this->url . '/' . $answerId, null);

        return $answer = $this->json_mapper->map(
            json_decode($ret),  new Answer()
        );
    }
}