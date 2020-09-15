<?php
namespace Atlassian\RestApiClient;

interface ClientInterface {

  function createUrl($path, array $query = array());

  function exec($url);

  function getResult($response, $curl, $url);
}
