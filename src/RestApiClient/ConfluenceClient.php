<?php
namespace Atlassian\RestApiClient;

use Atlassian\RestApiClient\Client;

class ConfluenceClient extends Client {

  public function __construct() {
    parent::__construct('confluence');
  }
}
