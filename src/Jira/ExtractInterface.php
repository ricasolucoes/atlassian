<?php
/**
 * @file
 * Contains the Extract Interface.
 */

namespace Atlassian\Jira;

/**
 * A base class to extract data from Jira database.
 */
interface ExtractInterface extends BaseInterface
{

    public function getQuery();

    public function setCount($count);

}
