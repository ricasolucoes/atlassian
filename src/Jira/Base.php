<?php
/**
 * @file
 * Contains the Base class.
 */

namespace Atlassian\Jira;

class Base implements BaseInterface {

  /**
   * The JIRA project ID.
   *
   * @var string
   */
  protected $projectID;

  /**
   * The start date of query interval.
   *
   * @var DateTime
   */
  protected $startDate;

  /**
   * The end date of query interval.
   *
   * @var DateTime
   */
  protected $endDate;

  /**
   * {@inheritdoc}
   */
  public function __construct($project_id, $start_date, $end_date = NULL) {
    $this->setProjectID($project_id);
    $this->setStartDate($start_date);
    if (!isset($end_date)) {
      $end_date = time();
    }
    $this->setEndDate($end_date);
  }


  public function getProperties() {
    $result = get_object_vars($this);

    return $result;
  }

  public function getClassName() {
    return get_class($this);
  }

  /**
   * {@inheritdoc}
   */
  public function getProjectID() {
    return $this->projectID;
  }

  /**
   * {@inheritdoc}
   */
  public function setProjectID($project_id) {
    $this->projectID = $project_id;
  }

  /**
   * {@inheritdoc}
   */
  public function getStartDate() {
    return $this->startDate;
  }

  /**
   * {@inheritdoc}
   */
  public function setStartDate(\DateTime $start_date) {
    $this->startDate = $start_date;

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getEndDate() {
    return $this->endDate;
  }

  /**
   * {@inheritdoc}
   */
  public function setEndDate(\DateTime $end_date) {
    $this->endDate = $end_date;

    return $this;
  }

}
