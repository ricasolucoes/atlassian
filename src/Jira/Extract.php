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
class Extract extends Base implements ExtractInterface {
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
  public function __construct($project_id, $start_date, $end_date = NULL) {
    parent::__construct($project_id, $start_date, $end_date);

    $filter = new Filter();
    $this->filters = $filter->getFilters();
    $this->jira = new JiraClient();
    $this->buildQuery();
  }

  protected function setQuery($query) {
    $this->query = $query;
  }

  /**
   * {@inheritdoc}
   */
  public function getQuery() {
    return $this->query;
  }


  protected function setJira(Jira $jira) {
    $this->jira = $jira;
  }

  /**
   * {@inheritdoc}
   */
  public function getJira() {
    return $this->jira;
  }

  /**
   * {@inheritdoc}
   */
  public function getCount() {
    return $this->count;
  }

  /**
   * {@inheritdoc}
   */
  public function setCount($count) {
    $this->count = $count;

    return $this;
  }

  /**
   * Initialize $query property.
   */
  private function initQuery() {
    $query = array(
      'jql' => '',
      'fields' => '*none',
      'startAt' => 0,
      'maxResults' => 0,
    );
    $this->setQuery($query);
  }

  protected function buildQuery() {
    $this->initQuery();
  }

  protected function extract() {
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
  protected function getParamsStr($params) {
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
   */
  public function addFilter(array $filter) {
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
  public function getInterval() {
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
