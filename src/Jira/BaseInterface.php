<?php
/**
 * @file
 * Contains the Base interface.
 */

namespace Atlassian\Jira;

interface BaseInterface {

  /**
   * Base constructor.
   *
   * @param string $project_id
   * @param \DateTime $start_date
   * @param \DateTime $end_date
   */
  public function __construct($project_id, $start_date, $end_date = NULL);


  public function getProperties();

  public function getClassName();

  /**
   * Getter function of projectID property.
   *
   * @return string
   */
  public function getProjectID();
  /**
   * Getter function of projectID property.
   *
   * @param string $project_id
   */
  public function setProjectID($project_id);

  /**
   * Getter function of startDate property.
   *
   * @return DateTime
   */
  public function getStartDate();

  /**
   * Setter function of startDate property.
   *
   * @param DateTime $start_date
   * @return $this
   */
  public function setStartDate(\DateTime $start_date);

  /**
   * Getter function of endDate property.
   *
   * @return DateTime
   */
  public function getEndDate();

  /**
   * Setter function of endDate property.
   *
   * @param DateTime $end_date
   *
   * @return $this
   */
  public function setEndDate(\DateTime $end_date);

}
