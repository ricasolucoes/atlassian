<?php
/**
 * @file
 * Contains the Extract Interface.
 */

namespace Atlassian\Jira;

/**
 * A base class to extract data from Jira database.
 */
interface ExtractInterface extends BaseInterface {

  public function __construct($project_id, $start_date, $end_date = NULL);

  public function getQuery();

  public function getJira();

  public function getCount();

  public function setCount($count);

  /**
   * Adds a filter to query.
   *
   * @param array $filter
   *
   * @return mixed
   */
  public function addFilter(array $filter);

  /**
   * Returns a timeframe array based on startDate and endDate.
   *
   * @return array
   * Keys:
   * - 'from'
   * - 'to'
   */
  public function getInterval();

}
