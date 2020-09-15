<?php
/**
 * @file
 * Contains the FilterdExtract class.
 */

namespace Atlassian\Jira;

use Atlassian\Filter\Filter;
use Atlassian\RestApiClient\JiraClient;

/**
 * A base class to extract data from Jira database.
 */
class FilterdExtract extends Extract implements FilteredExtractInterface {
  /**
   * FilteredExtract constructor.
   *
   * @param string $project_id
   * @param \DateTime $start_date
   * @param null $end_date
   */
  public function __construct($project_id, $start_date, $end_date = NULL) {
    parent::__construct($project_id, $start_date, $end_date);
  }

  public function addFilterProjects($projects) {
    $filter = array(
      'project' => $projects,
    );
    $this->addFilter($filter);
  }

  public function addFilterBugWontFix() {
    $filter = $this->filters['bwf'];
    $this->addFilter($filter);
  }

  public function addFilterDefects() {
    $filter = $this->filters['defects'];
    $this->addFilter($filter);
  }

  public function addFilterIgnoreDuplicates() {
    $query = $this->getQuery();
    if (!empty($query['jql'])) {
      $query['jql'] .= ' AND ';
    }
    $query['jql'] .= '(resolution not in (Duplicate) OR resolution is EMPTY)';
    $this->setQuery($query);
  }

  public function addFilterOpen() {
    $interval = $this->getInterval();
    $query = $this->getQuery();
    $filter = $this->filters['open'];
    $field = key($filter);
    $values = $filter[$field];
    // Missing statuses: 'In Testing', 'Needs Justification',

    $str = $this->getParamsStr($values);
    if (!empty($query['jql'])) {
      $query['jql'] .= ' AND ';
    }
    // Additionally, evaluating Open should consider the timeframe that it is being
    // reported against.  A defect closed in Q1 was open in Q2, so if the
    // Resolved Date is later than the range reporting against, that defect can be
    // considered open for that timeframe.
    $query['jql'] .= sprintf('(%s IN (%s) OR resolved > "%s")', $field, $str, $interval['to']);
    $this->setQuery($query);
  }

  public function addFilterPriority() {
    $filter = $this->filters['priority'];
    $this->addFilter($filter);
  }

  public function addFilterCritical() {
    $filter = $this->filters['critical'];
    $this->addFilter($filter);
  }

  public function addFilterUrgent() {
    $filter = $this->filters['urgent'];
    $this->addFilter($filter);
  }

  public function addFilterHigh() {
    $filter = $this->filters['high'];
    $this->addFilter($filter);
  }

  public function addFilterTBD() {
    $filter = $this->filters['tbd'];
    $this->addFilter($filter);
  }

  public function addFilterCannotReproduce() {
    $filter = $this->filters['cannot_reproduce'];
    $this->addFilter($filter);
  }

  public function addFilterDuplicate() {
    $filter = $this->filters['duplicate'];
    $this->addFilter($filter);
  }

  public function addFilterMoreInfoNeeded() {
    $filter = $this->filters['min'];
    $this->addFilter($filter);
  }

  public function addFilterNBWF() {
    $filter = $this->filters['nbwf'];
    $this->addFilter($filter);
  }

  public function addFilterClosed() {
    $filter = $this->filters['closed'];
    $this->addFilter($filter);
  }

  public function addTimeFrame($field) {
    $interval = $this->getInterval();
    $query = $this->getQuery();
    if (!empty($query['jql'])) {
      $query['jql'] .= ' AND ';
    }
    $query['jql'] .= sprintf('%s>="%s" AND %s<="%s"', $field, $interval['from'], $field, $interval['to']);
    $this->setQuery($query);
  }

  public function addFilterEscaped() {
    $query = $this->getQuery();
    if (!empty($query['jql'])) {
      $query['jql'] .= ' AND ';
    }
    $query['jql'] .= '("Customer Reported" = YES';
    $query['jql'] .= ' OR "Support Ticket ID" is not NULL';
    $query['jql'] .= ' OR (issueFunction in linkedIssuesOf("project = OP")))';
    $this->setQuery($query);
  }

  public function addFilterRegression() {
    $query = $this->getQuery();
    if (!empty($query['jql'])) {
      $query['jql'] .= ' AND ';
    }
    $query['jql'] .= 'Regression = Yes';
    $this->setQuery($query);
  }

}
