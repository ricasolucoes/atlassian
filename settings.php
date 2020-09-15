<?php
/**
 * Here you can add connection.
 * The program will always use the default connection.
 */
$connections = array(
  'jira' => array(
    'default' => array(
      'username' => 'USER_NAME',
      'password' => 'PASSWORD',
      'host' => 'https://JIRA-HOST',
      'api_uri' => '/rest/api/2',
      'email' => 'EMAIL-ADDRESS',
    ),
  ),
  'confluence' => array(
    'default' => array(
      'username' => 'USER_NAME',
      'password' => 'PASSWORD',
      'host' => 'https://CONFLUENCE-HOST',
      'api_uri' => '/rest/api',
      'email' => 'EMAIL-ADDRESS',
      'space_key' => 'THE-SPACE-KEY',
      'root_page_title' => 'THE ROOT PAGE TITLE',
    ),
  ),
);
$settings['connections'] = $connections;

/**
 * Increasing this value will reduce the load on the server when extracting data.
 * The program will wait between two calculations as many seconds as set for this value.
 * If you want to decrease this value it's advised to discuss it with your system administrator.
 */

$settings['sleep'] = 10;
