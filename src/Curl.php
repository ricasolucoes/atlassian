<?php


namespace Atlassian;

/**
 * Class Curl
 *
 * @package Rainflute\ConfluenceClient
 */
class Curl
{
    /**
     * @var null|resource
     */
    private $curl = null;
    /**
     * @var string
     */
    private $hostUrl;

    /**
     * Get host url
     *
     * @return string
     */
    public function getHost()
    {
        return $this->hostUrl;
    }

    /**
     * Set option to web client
     *
     * @param  $name
     * @param  $value
     * @return $this
     */
    public function setOption(int $name, array $value)
    {
        curl_setopt($this->curl, $name, $value);
        return $this;
    }

    /**
     * Set multiple options
     *
     * @param  array $options
     * @return $this
     */
    public function setOptions($options)
    {
        curl_setopt_array($this->curl, $options);
        return $this;
    }

    /**
     * Execute the quest and return response from server
     *
     * @return mixed
     */
    public function execute()
    {
        return curl_exec($this->curl);
    }

    /**
     * Set headers from an array to web client
     *
     * @param  $headers
     * @return $this
     */
    public function setHeaders(array $headers)
    {
        $httpHeaders = [];
        foreach ($headers as $key => $value) {
            $httpHeaders[] = $key.':'.$value;
        }
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, $httpHeaders);
        return $this;
    }

    /**
     * Get errors from the request
     *
     * @return string
     */
    public function getError()
    {
        return curl_error($this->curl);
    }

    /**
     * Get error number
     *
     * @return int
     */
    public function getErrorNumber()
    {
        return curl_errno($this->curl);
    }

    /**
     * Close connection
     *
     * @return $this
     */
    public function close()
    {
        curl_close($this->curl);
        return $this;
    }
}
