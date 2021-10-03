<?php
/**
 * @file
 * Contains the Extract class.
 */

namespace Atlassian\Jira;

use Atlassian\Filter\Filter;
use Atlassian\RestApiClient\JiraClient;

/**
 * A base class to extract data from Jira database.
 */
class Extract extends Base implements ExtractInterface
{
    /**
     * Count of query results.
     *
     * @var int
     */
    protected $count;
    /**
     * @var array
     */
    protected $query;
    /**
     * @var JiraClient
     */
    protected $jira;
    /**
     * @var array
     */
    protected $filters;

    /**
     * {@inheritdoc}
     */
    public function __construct($project_id, $start_date, $end_date = null)
    {
        parent::__construct($project_id, $start_date, $end_date);

        $filter = new Filter();
        $this->filters = $filter->getFilters();
        $this->jira = new JiraClient();
        $this->buildQuery();
    }

    /**
     * @param (int|string)[] $query
     *
     * @return void
     */
    protected function setQuery(array $query): void
    {
        $this->query = $query;
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function getQuery()
    {
        return $this->query;
    }


    protected function setJira(Jira $jira): void
    {
        $this->jira = $jira;
    }

    /**
     * {@inheritdoc}
     *
     * @return JiraClient
     */
    public function getJira()
    {
        return $this->jira;
    }

    /**
     * {@inheritdoc}
     *
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * {@inheritdoc}
     *
     * @return static
     */
    public function setCount($count)
    {
        $this->count = $count;

        return $this;
    }

    /**
     * Initialize $query property.
     *
     * @return void
     */
    private function initQuery(): void
    {
        $query = array(
        'jql' => '',
        'fields' => '*none',
        'startAt' => 0,
        'maxResults' => 0,
        );
        $this->setQuery($query);
    }

    protected function buildQuery(): void
    {
        $this->initQuery();
    }

    protected function extract(): void
    {
        $this->buildQuery();
        $url = $this->jira->createUrl('search', $this->query);
        $response = $this->jira->exec($url);
        $this->setCount($response['total']);
    }

    /**
     * Helper function to convert filter array into string;
     *
     * @param $params
     *   Params to be converted.
     *
     * @return string
     *   The paramas converted to string.
     */
    protected function getParamsStr($params)
    {
        // If the input is a string we create an array of it.
        if (!is_array($params)) {
            $params = explode(',', $params);
        }
        $result = '';
        foreach ($params as  $param) {
            if (empty($result)) {
                $result = sprintf('"%s"', trim($param));
            }
            else {
                $result .= sprintf(',"%s"', trim($param));
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    public function addFilter(array $filter)
    {
        $field = key($filter);
        $values = $filter[$field];
        $str = $this->getParamsStr($values);
        $query = $this->getQuery();
        if (!empty($query['jql'])) {
            $query['jql'] .= ' AND ';
        }
        $query['jql'] .= sprintf($field . ' IN (%s)', $str);
        $this->setQuery($query);
    }

    /**
     * {@inheritdoc}
     */
    public function getInterval()
    {
        $format = 'Y-m-d H:i';

        $startdate = date($format, $this->getStartDate());
        $enddate = date($format, $this->getEndDate());
        $result = array(
        'from' => $startdate,
        'to' => $enddate,
        );
        return  $result;
    }

}
