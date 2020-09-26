<?php
/**
 * @file
 * Contains the Page class.
 */

namespace Atlassian\Confluence;

use Atlassian\Configuration\Config;
use Atlassian\RestApiClient\ConfluenceClient;

class Page
{
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
     *
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
    public function __construct($space_key, $title)
    {
        self::$spaceKey = $space_key;
        self::$title = $title;
    }

    /**
     * Setter function of query property.
     *
     * @param array $query
     *
     * @return void
     */
    protected static function setQuery($query): void
    {
        self::$query = $query;
    }

    /**
     * Setter function of confluence property.
     *
     * @param \Atlassian\RestApiClient\ConfluenceClient $confluence
     *
     * @return void
     */
    protected static function setConfluence(ConfluenceClient $confluence): void
    {
        self::$confluence = $confluence;
    }

    /**
     * {@inheritdoc}
     *
     * @return ConfluenceClient
     */
    public static function getConfluence(): ConfluenceClient
    {
        return self::$confluence;
    }

    /**
     * Initiates query property with default values.
     *
     * @return void
     */
    private static function initQuery(): void
    {
        $query = array(
        'title' => self::$title,
        'spaceKey' => self::$spaceKey,
        'expand' => 'space,body.view,version,container',
        );
        self::setQuery($query);
    }

    /**
     * Builds the query property.
     *
     * @return void
     */
    protected static function buildQuery(): void
    {
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
    protected static function extract()
    {
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
    public static function get()
    {
        $page = self::extract();

        return $page;
    }

    /**
     * {@inheritdoc}
     */
    public static function getId()
    {
        $result = false;
        $page = self::get();
        if (isset($page['id'])) {
            $result = $page['id'];
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public static function getVersion()
    {
        $result = false;
        $page = self::get();
        if (isset($page['version']['number'])) {
            $result = $page['version']['number'];
        }
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
    private static function post(array $data, $url)
    {
        $json = json_encode($data);
        $username = Config::getUser();
        $password = Config::getPassword();

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_USERPWD, "$username:$password");
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($curl, CURLOPT_POST, true);
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
    private static function put($data, $url)
    {
        $json = json_encode($data);
        $username = Config::getUser();
        $password = Config::getPassword();

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_USERPWD, "$username:$password");
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: ' . strlen($json)));
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);

        $result = self::$confluence->getResult($response, $curl, $url);

        return $result;
    }

}
