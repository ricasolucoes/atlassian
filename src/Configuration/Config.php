<?php
/**
 * @file
 * Contains the Config class.
 */

namespace Atlassian\Configuration;

use Exception;

/**
 * Class Config to read configuration from settings.php
 *
 * @package Atlassian\Configuration
 */
class Config {

  static protected $settings = array();

  static protected $connection = 'default';

  /**
   * The name of client you want to use.
   *
   * It works with the following valies:
   * - 'jira'
   * - 'confluence'
   * @var string
   */
  static protected $app;

  static function init($app = '', $settings_file_name = 'settings.php', $connection = 'default') {
    self::setApp($app);
    self::setSettings($settings_file_name);
    if (!empty($app)) {
      self::setConnection($connection);
    }
  }

  public static function getApp() {
    return self::$app;
  }

  public static function setApp($value) {
    self::$app = $value;
  }

  public static function getConnection() {
    return self::$connection;
  }

  private static function setConnection($connection_name = 'default') {
    if (isset(self::$settings['connections']) &&
      isset(self::$settings['connections'][self::$app]) &&
      isset(self::$settings['connections'][self::$app][$connection_name])) {
      self::$connection = self::$settings['connections'][self::$app][$connection_name];
    }
    else {
      $error = "Connection ($connection_name) information is missing!";
      throw new Exception($error);
    }
  }

  public static function getSettings() {
    return self::$settings;
  }

  private static function setSettings($filename) {
    if (file_exists($filename)) {
      include $filename;
      if (isset($settings)) {
        self::$settings = $settings;
      }
    }
    else {
      $error = "Settings file ($filename) not found!";
      throw new Exception($error);
    }
  }

  public static function getProducts() {
    $settings = self::getSettings();
    $result = array();
    if (isset($settings['products'])) {
      $result = $settings['products'];
    }
    return $result;
  }

  public static function getConnections() {
    $settings = self::getSettings();
    $result = array();
    if (isset($settings['connections'])) {
      $result = $settings['connections'];
    }
    return $result;
  }
  public static function getUser() {
    $connection = self::getConnection();
    if ($connection && isset($connection['username'])) {
      return $connection['username'];
    }
    else {
      $error = "Username is missing.";
      throw new Exception($error);
    }
  }

  public static function getPassword() {
    $connection = self::getConnection();
    if ($connection && isset($connection['password'])) {
      return $connection['password'];
    }
    else {
      $error = "Password is missing.";
      throw new Exception($error);
    }
  }

  public static function getHost() {
    $connection = self::getConnection();
    if ($connection && isset($connection['host'])) {
      return $connection['host'];
    }
    else {
      $error = "Host is missing.";
      throw new Exception($error);
    }
  }

  public static function getApiUri() {
    $connection = self::getConnection();
    if ($connection &&  isset($connection['api_uri'])) {
      return $connection['api_uri'];
    }
    else {
      $error = "API URI is missing.";
      throw new Exception($error);
    }
  }

  public static function getSleep() {
    $settings = self::getSettings();
    $result = 10;
    if (isset($settings['sleep'])) {
      $result = $settings['sleep'];
    }
    return $result;
  }
}
