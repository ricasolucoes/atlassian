<?php
/**
 * @file
 * Contains the FilteredExtract Interface.
 */

namespace Atlassian\Jira;

/**
 * A base class to extract data from Jira database.
 */
interface FilteredExtractInterface extends Extractnterface {

  public function addFilterProjects($projects);

  public function addFilterBugWontFix();

  public function addFilterDefects();

  public function addFilterIgnoreDuplicates();

  public function addFilterOpen();

  public function addFilterPriority();

  public function addFilterCritical();

  public function addFilterUrgent();

  public function addFilterHigh();

  public function addFilterTBD();

  public function addFilterCannotReproduce();

  public function addFilterDuplicate();

  public function addFilterMoreInfoNeeded();

  public function addFilterNBWF();

  public function addFilterClosed();

  public function addTimeFrame($field);

  public function addFilterEscaped();

  public function addFilterRegression();

}
