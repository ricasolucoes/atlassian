<?php
/**
 * @file
 * Contains the Page class.
 */

namespace Atlassian\Confluence;

use Atlassian\Configuration\Config;
use Atlassian\RestApiClient\ConfluenceClient;

class Page {
  /**
   * @see http://www.zestudio.net/actualites/confluence-send-api-rest-getpostput-requests-in-json-format-php/
   * @see http://stackoverflow.com/questions/30907337/using-confluence-rest-api-to-post-generated-html-table-php-without-getting-htt
   * @see http://stackoverflow.com/questions/31878032/using-php-to-create-confluence-wiki-pages
   * @see https://developer.atlassian.com/confdev/confluence-rest-api/confluence-rest-api-examples
   * @see https://confluence.atlassian.com/confkb/how-to-use-php-inside-confluence-pages-317197666.html
   */

  /**
   * @var array
   * - 'title'
   * - 'spaceKey'
   * - 'expand'
   */
  static protected $query;
  /**
   * A REST API client.
   *
   * @var ConfluenceClient
   */
  static protected $confluence;
  /**
   * The name of the space.
   * @var string
   */
  static protected $spaceKey;
  /**
   * The title of the page.
   *
   * @var string
   */
  static protected $title;

  /**
   * Page constructor.
   *
   * @param string $space_key
   *   The space key.
   * @param string $title
   *   The page title.
   */
  public function __construct($space_key, $title) {
    self::$spaceKey = $space_key;
    self::$title = $title;
  }

  /**
   * {@inheritdoc}
   */
  public static function init($space_key, $title) {
    return new Page($space_key, $title);
  }

  /**
   * Setter function of query property.
   *
   * @param array $query
   */
  protected static function setQuery($query) {
    self::$query = $query;
  }

  /**
   * {@inheritdoc}
   */
  public static function getQuery() {
    return self::$query;
  }

  /**
   * Setter function of confluence property.
   *
   * @param \Atlassian\RestApiClient\ConfluenceClient $confluence
   */
  protected static function setConfluence(ConfluenceClient $confluence) {
    self::$confluence = $confluence;
  }

  /**
   * {@inheritdoc}
   */
  public static function getConfluence() {
    return self::$confluence;
  }

  /**
   * Initiates query property with default values.
   */
  private static function initQuery() {
    $query = array(
      'title' => self::$title,
      'spaceKey' => self::$spaceKey,
      'expand' => 'space,body.view,version,container',
    );
    self::setQuery($query);
  }

  /**
   * Builds the query property.
   */
  protected static function buildQuery() {
    self::initQuery();
    if (empty(self::getConfluence())) {
      $confluence = new ConfluenceClient();
      self::setConfluence($confluence);
    }
  }

  /**
   * Runs the query set in query property against Confluence and returns the response.
   *
   * @return array|mixed
   */
  protected static function extract() {
    self::buildQuery();
    $url = self::$confluence->createUrl('content', self::$query);
    $response = self::$confluence->exec($url);
    $result = array();
    if (isset($response['results']) && is_array($response['results'])) {
      $result = reset($response['results']);
    }

    return $result;
  }

  /**
   * {@inheritdoc}
   */
  public static function get() {
    $page = self::extract();

    return $page;
  }

  /**
   * {@inheritdoc}
   */
  public static function getId() {
    $result = FALSE;
    $page = self::get();
    if (isset($page['id'])) {
      $result = $page['id'];
    }

    return $result;
  }

  /**
   * {@inheritdoc}
   */
  public static function getVersion() {
    $result = FALSE;
    $page = self::get();
    if (isset($page['version']['number'])) {
      $result = $page['version']['number'];
    }
    return $result;
  }

  /**
   * {@inheritdoc}
   */
  public static function exists() {
    $result = self::getId() !== FALSE;
    return $result;
  }

  /**
   * {@inheritdoc}
   */
  public static function update($ancestor_id, $body) {
    $version = self::getVersion();
    $version++;
    $data = array(
      'id' => self::getId(),
      'type' => 'page',
      'title' => self::$title,
      'space' => array('key' => self::$spaceKey,),
      'ancestors' => array(
        array(
          'type' => 'page',
          'id' => $ancestor_id,
        ),
      ),
      'body' => array(
        'storage' => array(
          'value' => $body,
          'representation' => 'storage',
        )
      ),
      'version' => array(
        'number' => $version,
      ),
    );
    $url = self::$confluence->createUrl('content/' . $page_id);

    $result = self::put($data, $url);

    return $result;
  }

  /**
   * {@inheritdoc}
   */
  public static function add($ancestor_id, $body = '') {
    $data = array(
      'type' => 'page',
      'title' => self::$title,
      'space' => array('key' => self::$spaceKey,),
      'ancestors' => array(
        array(
          'type' => 'page',
          'id' => $ancestor_id,
        ),
      ),
      'body' => array(
        'storage' => array(
          'value' => $body,
          'representation' => 'storage',
        )
      ),
    );
    $url = self::$confluence->createUrl('content');
    $result = self::post($data, $url);

    return $result;
  }

  /**
   * {@inheritdoc}
   */
  public static function addImage($filename, $comment = '') {
    $content_id = self::getId();
    $data = array(
      'file' => '@' . $filename,
    );
    $json = json_encode($data);

    self::buildQuery();
    $url = self::$confluence->createUrl('content/' . $content_id . '/child/attachment');
    $username = Config::getUser();
    $password = Config::getPassword();

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($curl, CURLOPT_USERPWD, "$username:$password");
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($curl, CURLOPT_SAFE_UPLOAD, FALSE);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: image/png'));
    curl_setopt($curl, CURLOPT_POST, TRUE);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $json);

    $response = curl_exec($curl);

    $result = self::$confluence->getResult($response, $curl, $url);

    return $result;
  }

  /**
   * {@inheritdoc}
   */
  public static function delete() {
    $id = self::getId();
    $url = self::$confluence->createUrl("content/$id");

    $username = Config::getUser();
    $password = Config::getPassword();

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($curl, CURLOPT_USERPWD, "$username:$password");
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');

    $response = curl_exec($curl);

    $result = self::$confluence->getResult($response, $curl, $url);
    return $result;

  }

  /**
   * Helper function to post data.
   *
   * @param array $data
   * @param $url
   *
   * @return mixed|string
   */
  private static function post(array $data, $url) {
    $json = json_encode($data);
    $username = Config::getUser();
    $password = Config::getPassword();

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($curl, CURLOPT_USERPWD, "$username:$password");
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($curl, CURLOPT_POST, TRUE);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $json);

    $response = curl_exec($curl);

    $result = self::$confluence->getResult($response, $curl, $url);

    return $result;
  }

  /**
   * Helper function to put data.
   *
   * @param array $data
   * @param $url
   *
   * @return mixed|string
   */
  private static function put($data, $url) {
    $json = json_encode($data);
    $username = Config::getUser();
    $password = Config::getPassword();

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_USERPWD, "$username:$password");
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: ' . strlen($json)));
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
    curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    $response = curl_exec($curl);

    $result = self::$confluence->getResult($response, $curl, $url);

    return $result;
  }

}
