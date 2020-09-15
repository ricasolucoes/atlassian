<?php
/**
 * @file
 * Contains the Filter class.
 */

namespace Atlassian\Filter;

class Filter {
  private $bugWontFix;
  private $cannotReproduce;
  private $closed;
  private $critical;
  private $defects;
  private $duplicate;
  private $high;
  private $moreInfoNeeded;
  private $notBugWontFix;
  private $open;
  private $priority;
  private $toBeDetermined;
  private $urgent;
  protected $filters;

  public function __construct() {
    // JQL Filters.
    $this->defects = array(
      'issuetype' => array(
        'Bug',
        'Risk',
        'Security mitigation',
      ),
    );

    // Missing: 'Needs Justification'.
    $this->open = array(
      'status' => array(
        'Open',
        'Testing',
        'Kanban To Do',
        'Good Idea',
        'In Progress',
        'Reopened',
        'In Code Review',
        'In Design',
        'In Roadmap',
        'Ready For Dev',
        'Design Review',
        'Ready To Release',
      ),
    );

    $this->closed = array(
      'status' => array(
        'Closed',
        'Rejected',
        'Done',
      ),
    );

    $this->cannotReproduce = array(
      'resolution' => array('Cannot Reproduce'),
    );

    $this->duplicate = array(
      'resolution' => array('Duplicate'),
    );

    $this->bugWontFix = array(
      'resolution' => array("Bug, but won't fix"),
    );

    $this->notBugWontFix = array(
      'resolution' => array("Not a bug, won't fix"),
    );

    $this->moreInfoNeeded = array(
      'resolution' => array('More Info Needed'),
    );

    $this->critical = array(
      'priority' => array('Critical'),
    );

    $this->high = array(
      'priority' => array('High'),
    );

    $this->urgent = array(
      'priority' => array('Urgent'),
    );

    $this->priority = array(
      'priority' => array('Critical', 'High', 'Urgent'),
    );

    $this->toBeDetermined = array(
      'priority' => array('TBD'),
    );

    // JQL filters.
    $this->filters = array(
      'bwf' => $this->bugWontFix,
      'cannot_reproduce' => $this->cannotReproduce,
      'closed' => $this->closed,
      'critical' => $this->critical,
      'defects' => $this->defects,
      'duplicate' => $this->duplicate,
      'high' => $this->high,
      'min' => $this->moreInfoNeeded,
      'nbwf' => $this->notBugWontFix,
      'open' => $this->open,
      'priority' => $this->priority,
      'urgent' => $this->urgent,
      'tbd' => $this->toBeDetermined,
    );
  }

  /**
   *
   * @return Filter
   */
  public function getFilters() {
    return $this->filters;
  }

}
