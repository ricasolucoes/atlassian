<?php
namespace Atlassian\RestApiClient;

use Exception;
use Atlassian\Configuration\Config;
use Atlassian\RestApiClient\ClientInterface;

class Client implements ClientInterface {

  /**
   * The whole url includin host, api uri and jql query.
   * @var string
   */
  protected $url;

  /**
   * Decides whether you want to use Confluence or Jira client.
   *
   * Accepted values:
   * - 'jira'
   * - 'confluence'
   * @var string
   */
  protected $appID;

  /**
   * Client constructor.
   *
   * @param $app_id
   */
  public function __construct($app_id) {
    $this->setAppId($app_id);
  }

  /**
   * {@inheritdoc}
   */
  public function getUrl() {
    return $this->url;
  }

  /**
   * Setter of appId property.
   *
   * @param string $app_id
   *
   * @return $this
   */
  private function setAppId($app_id) {
    $this->appID = $app_id;

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function createUrl($path, array $query = array()) {
    Config::init($this->appID);
    $host = Config::getHost();
    $api_uri = Config::getApiUri();
    $result = $host . $api_uri . '/';
    if (!empty($path)) {
      $result .= $path;
    }
    if (!empty($query)) {
      $result .= '?' . http_build_query($query);
    }

    return $result;
  }

  /**
   * {@inheritdoc}
   */
  public function exec($url) {
    $this->url = urldecode($url);
    $username = Config::getUser();
    $password = Config::getPassword();

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($curl, CURLOPT_USERPWD, "$username:$password");
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);

    $response = curl_exec($curl);
    if (empty($response)) {
      $seconds = 5;
      $i = 1;
      $count = 5;
      while (empty($response) && $count >= 0) {
        sleep($i * $seconds);
        $response = curl_exec($curl);
        $i++;
        $count--;
      }
    }
    $result = $this->getResult($response, $curl, $url);

    return $result;
  }

  /**
   * {@inheritdoc}
   */
  public function getResult($response, $curl, $url) {
    // If request failed.
    if (!$response) {
      $http_response = curl_getinfo($curl, CURLINFO_HTTP_CODE);
      $body = curl_error($curl);
      curl_close($curl);

      //The server successfully processed the request, but is not returning any content.
      if ($http_response == 204) {
        return '';
      }
      $error = 'CURL Error (' . get_class($this) . ")\n
        url: $url\n
        body: $body";
      throw new Exception($error);
    }
    else {
      // If request was ok, parsing http response code.
      $http_response = curl_getinfo($curl, CURLINFO_HTTP_CODE);
      curl_close($curl);

      // don't check 301, 302 because setting CURLOPT_FOLLOWLOCATION
      if ($http_response != 200 && $http_response != 201) {
        $error = "CURL HTTP Request Failed: Status Code :
          $http_response, URL: $url
          \nError Message : $response";
        throw new Exception($error);
      }
    }
    $result = json_decode($response, TRUE);

    return $result;
  }

}
