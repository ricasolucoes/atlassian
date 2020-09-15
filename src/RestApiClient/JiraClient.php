<?php

namespace Atlassian\RestApiClient;

class JiraClient extends Client {

  public function __construct() {
    parent::__construct('jira');
  }
}
