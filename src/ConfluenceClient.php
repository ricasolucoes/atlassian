<?php

namespace Atlassian;

use Atlassian\Configuration\ConfigurationInterface;
use Atlassian\Configuration\DotEnvConfiguration;
use Monolog\Logger as Logger;
use Monolog\Handler\StreamHandler;

/**
 * Interact confluence server with REST API.
 */
class ConfluenceClient
{
    /**
     * Json Mapper.
     *
     * @var \JsonMapper
     */
    protected $json_mapper;

    /**
     * HTTP response code.
     *
     * @var string
     */
    protected $http_response;

    /**
     * Confluence REST API URI.
     *
     * @var string
     */
    protected $api_uri = '/rest';

    /**
     * Monolog instance.
     *
     * @var \Monolog\Logger
     */
    protected $log;

    /**
     * Confluence Rest API Configuration.
     *
     * @var ConfigurationInterface
     */
    protected $configuration;

    /**
     * Constructor.
     *
     * @param ConfigurationInterface $configuration
     * @param Logger                 $logger
     */
    public function __construct(ConfigurationInterface $configuration = null, Logger $logger = null)
    {
        if ($configuration === null) {
            $path = './';
            if (!file_exists('.env')) {
                // If calling the getcwd() on laravel it will returning the 'public' directory.
                $path = '../';
            }
            $configuration = new DotEnvConfiguration($path);
        }

        $this->configuration = $configuration;
        $this->json_mapper = new \JsonMapper();

        $this->json_mapper->undefinedPropertyHandler = [
            \Atlassian\JsonMapperHelper::class,
            'setUndefinedProperty',
        ];

        // create logger
        if ($logger) {
            $this->log = $logger;
        } else {
            $this->log = new Logger('Confluence');
            $this->log->pushHandler(
                new StreamHandler(
                    $configuration->getLogFile(),
                    $this->convertLogLevel($configuration->getLogLevel())
                )
            );
        }

        $this->http_response = 200;
    }

    /**
     * Convert log level.
     *
     * @param $log_level
     *
     * @return int
     */
    private function convertLogLevel(string $log_level): int
    {
        switch ($log_level) {
        case 'DEBUG':
            return Logger::DEBUG;
        case 'INFO':
            return Logger::INFO;
        case 'ERROR':
            return Logger::ERROR;
        default:
            return Logger::WARNING;
        }
    }

    /**
     * Execute REST request.
     *
     * @param string $context        Rest API context (ex.:issue, search, etc..)
     * @param string $post_data
     * @param string $custom_request [PUT|DELETE]
     *
     * @return string|true
     *
     * @throws ConfluenceException
     */
    public function exec($context, $post_data = null, $custom_request = null, bool $isFqdn = false)
    {
        $url = $this->createUrlByContext($context, $isFqdn);

        $this->log->addDebug("Curl $url JsonData=".$post_data);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);

        // post_data
        if (!is_null($post_data)) {
            // PUT REQUEST
            if (!is_null($custom_request) && $custom_request == 'PUT') {
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
            }
            if (!is_null($custom_request) && $custom_request == 'DELETE') {
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
            } else {
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
            }
        }

        $this->authorization($ch);

        if (!$this->getConfiguration()->isSslVerify()) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        }

        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt(
            $ch, CURLOPT_HTTPHEADER,
            array('Accept: */*', 'Content-Type: application/json')
        );

        curl_setopt($ch, CURLOPT_VERBOSE, $this->getConfiguration()->isVerbose());

        $this->log->addDebug('Curl exec='. $url . ',customreq=' . $custom_request);
        $response = curl_exec($ch);

        // if request failed.
        if (!$response) {
            $this->http_response = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $body = curl_error($ch);
            curl_close($ch);

            //The server successfully processed the request, but is not returning any content.
            if ($this->http_response == 204) {
                return '';
            }

            // HostNotFound, No route to Host, etc Network error
            $this->log->addError('CURL Error: = '.$body);
            throw new ConfluenceException('CURL Error: = '.$body);
        } else {
            // if request was ok, parsing http response code.
            $this->http_response = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            curl_close($ch);

            // don't check 301, 302 because setting CURLOPT_FOLLOWLOCATION
            if ($this->http_response != 200 && $this->http_response != 201) {
                throw new ConfluenceException(
                    'CURL HTTP Request Failed: Status Code : '
                    .$this->http_response.', URL:'.$url
                    ."\nError Message : ".$response, $this->http_response
                );
            }
        }

        return $response;
    }

    /**
     * Create upload handle.
     *
     * @param string $url         Request URL
     * @param string $upload_file Filename
     *
     * @return resource
     */
    private function createUploadHandle($url, $upload_file)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);

        // send file
        curl_setopt($ch, CURLOPT_POST, true);

        if (PHP_MAJOR_VERSION == 5 && PHP_MINOR_VERSION  < 5) {
            $attachments = realpath($upload_file);
            $filename = basename($upload_file);

            curl_setopt(
                $ch, CURLOPT_POSTFIELDS,
                array('file' => '@'.$attachments.';filename='.$filename)
            );

            $this->log->addDebug('using legacy file upload');
        } else {
            // CURLFile require PHP > 5.5
            $attachments = new \CURLFile(realpath($upload_file));
            $attachments->setPostFilename(basename($upload_file));

            curl_setopt(
                $ch, CURLOPT_POSTFIELDS,
                array('file' => $attachments)
            );

            $this->log->addDebug('using CURLFile='.var_export($attachments, true));
        }

        $this->authorization($ch);

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $this->getConfiguration()->isCurlOptSslVerifyHost());
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->getConfiguration()->isCurlOptSslVerifyPeer());

        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt(
            $ch, CURLOPT_HTTPHEADER,
            array(
                'Accept: */*',
                'Content-Type: multipart/form-data',
                'X-Atlassian-Token: nocheck',
            )
        );

        curl_setopt($ch, CURLOPT_VERBOSE, $this->getConfiguration()->isCurlOptVerbose());

        $this->log->addDebug('Curl exec='.$url);

        return $ch;
    }

    /**
     * Get URL by context.
     *
     * @param string $context
     *
     * @return string
     */
    protected function createUrlByContext($context, bool $isFqdn = false)
    {
        if ($isFqdn == true) {
            return $context;
        }

        $host = $this->getConfiguration()->getHost();

        return $host.$this->api_uri.'/'.preg_replace('/\//', '', $context, 1);
    }

    /**
     * Add authorize to curl request.
     *
     * @TODO session/oauth methods
     *
     * @param resource $ch
     *
     * @return void
     */
    protected function authorization($ch): void
    {
        $username = $this->getConfiguration()->getUser();
        $password = $this->getConfiguration()->getPassword();
        if (!empty($username) && !empty($password)) {
            curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
        }
    }

    /**
     * Confluence Rest API Configuration.
     *
     * @return ConfigurationInterface
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }
}
