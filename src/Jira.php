<?php

namespace Atlassian;

/**
 * Atlassian Jira REST API wrapper for Atlassian
 */
class Jira
{
    /**
     * @var string
     */
    protected $uri;

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $password;
    protected string $encodedCredential;
    /**
     * authenticate - log in to the jira api
     *
     * @param string $uri      URI
     * @param string $username Username
     * @param string $password Password
     *
     * @return void
     */
    public function authenticate($uri, $username, $password): void
    {
        $this->uri = $uri;
        $this->username = $username;
        $this->password = $password;
        $this->encodedCredential = base64_encode($this->username . ':' . $this->password);
    }

    /**
     * isAuthenticated - private method used to determine if we're authenticated
     *
     * @return bool
     */
    private function isAuthenticated()
    {
        if (!$this->uri or !$this->username or !$this->password) {
            echo "Not authenticated\n";
            return false;
        }
        return true;
    }

    /**
     * prepCurl - prepare the curl object
     *
     * @param string $url URL
     *
     * @return mixed
     */
    private function prepCurl($url)
    {
        $headers = array(
            'Authorization: Basic ' . $this->encodedCredential
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_PORT, '443');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

        curl_setopt($ch, CURLINFO_HEADER_OUT, true);

        return $ch;
    }

    /**
     * getFilter - retrieve a filter
     *
     * @param string $key Filter key
     *
     * @return array
     */
    public function getFilter($key)
    {
        if ($this->isAuthenticated()) {
            $url = $this->uri . "/rest/api/2/filter/" . $key;
            $ch = $this->prepCurl($url);

            $responseString = curl_exec($ch);


            return json_decode($responseString, true);
        }
    }

    /**
     * getUser - retrieve a user
     *
     * @param string $accountId account id
     *
     * @return array
     */
    public function getUser($accountId)
    {
        if ($this->isAuthenticated()) {
            $url = $this->uri . "/rest/api/2/user/?accountId=" . $accountId;
            $ch = $this->prepCurl($url);

            $responseString = curl_exec($ch);


            return json_decode($responseString, true);
        }
    }
    /**
     * executeFilter - run a search based on a filter
     *
     * @param string $key Filter key
     *
     * @return array
     */
    public function executeFilter($key)
    {
        if ($this->isAuthenticated()) {
            $result = $this->getFilter($key);
            $searchUrl = $result['searchUrl'];

            $url = $searchUrl;
            $ch = $this->prepCurl($url);

            $responseString = curl_exec($ch);


            return json_decode($responseString, true);
        }
    }

    /**
     * getIssue - retrieve an issue
     *
     * @param string $key    Issue key
     * @param string $fields Fields
     *
     * @return array
     */
    public function getIssue($key, $fields = false)
    {
        if ($this->isAuthenticated()) {
            $url = $this->uri . "/rest/api/latest/issue/" . $key;
            if ($fields) {
                $url .= "?fields=" . $fields;
            }
            $ch = $this->prepCurl($url);

            $responseString = curl_exec($ch);


            return json_decode($responseString, true);
        }
    }

    /**
     * getChangelog - retrieve the changelog for the given issue key
     *
     * @param string $key Issue key
     *
     * @return array
     */
    public function getChangelog($key)
    {
        if ($this->isAuthenticated()) {
            $url = $this->uri . "/rest/api/2/issue/" . $key . "?expand=changelog";
            $ch = $this->prepCurl($url);

            $responseString = curl_exec($ch);


            return json_decode($responseString, true);
        }
    }

    /**
     * updateIssue - update an issue
     *
     * @param string $key  Issue key
     * @param array  $data Data for fields
     *
     * @return bool|string
     */
    public function updateIssue($key, $data)
    {
        if ($this->isAuthenticated()) {
            $url = $this->uri . "/rest/api/2/issue/" . $key;

            $headers = array(
                'Authorization: Basic ' . $this->encodedCredential,
                'Content-Type: application/json;charset=UTF-8'
            );

            $dataString = json_encode($data);

            $ch = $this->prepCurl($url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
            curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $response = curl_exec($ch);

            return $response;
        }
        return false;
    }

    /**
     * createIssue - create an issue
     *
     * @param array $data Data for fields
     *
     * @return bool|string
     */
    public function createIssue($data)
    {
        if ($this->isAuthenticated()) {
            $url = $this->uri . "/rest/api/2/issue/";

            $headers = array(
                'Authorization: Basic ' . $this->encodedCredential,
                'Content-Type: application/json;charset=UTF-8'
            );

            $dataString = json_encode($data);
            echo $dataString . "\n";
            $ch = $this->prepCurl($url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
            curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $response = curl_exec($ch);

            file_put_contents("/tmp/robot-curl-errors.txt", var_export(curl_error($ch), true) . "\n", FILE_APPEND);
            return $response;
        }
        return false;
    }

    /**
     * getTransitions - retrieve a list of available transitions
     *
     * @param string $key Issue key
     *
     * @return string[]
     *
     * @psalm-return array<array-key, string>
     */
    public function getTransitions($key): array
    {
        $url = $this->uri . "/rest/api/latest/issue/" . $key . '/transitions';
        $ch = $this->prepCurl($url);

        $transitions = json_decode(curl_exec($ch), true)['transitions'];

        $transitionIdToStatusName = [];

        foreach ($transitions as $transition) {
            $transitionIdToStatusName[$transition['id']] = strtolower($transition['to']['name']);
        }

        return $transitionIdToStatusName;
    }

    /**
     * transitionTo - transition an issue to a new status
     *
     * @param string $key    Issue key
     * @param string $status Transition to status
     *
     * @return bool
     */
    public function transitionTo($key, $status)
    {
        $url = $this->uri . "/rest/api/latest/issue/" . $key . "/transitions";
        $headers = array(
            'Authorization: Basic ' . $this->encodedCredential,
            'Content-Type: application/json;charset=UTF-8'
        );

        $transitions = $this->getTransitions($key);
        if ($id = array_search(strtolower($status), $transitions)) {
            $data = ['transition' => ['id' => $id]];
            $dataString = json_encode($data, JSON_PRETTY_PRINT);

            $ch = $this->prepCurl($url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
            curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            curl_exec($ch);
            return true;
        }
        return false;
    }

    /**
     * addComment - add a comment to an issue
     *
     * @param string $key     Issue key
     * @param string $comment Text of the comment
     *
     * @return bool|string
     */
    public function addComment($key, $comment)
    {
        $url = $this->uri . "/rest/api/2/issue/" . $key . "/comment";
        $headers = array(
            'Authorization: Basic ' . $this->encodedCredential,
            'Content-Type: application/json;charset=UTF-8'
        );

        $data = ['body' => $comment];
        $dataString = json_encode($data, JSON_PRETTY_PRINT);

        $ch = $this->prepCurl($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);

        return $response;
    }

    /**
     * runQuery - run an arbitrary jql query
     *
     * @param string $key Filter key
     *
     * @return array
     */
    public function runQuery($jql)
    {
        if ($this->isAuthenticated()) {
            $url = $this->uri . "/rest/api/2/search?jql=" . preg_replace("/ /", "+", $jql);
            $ch = $this->prepCurl($url);

            $responseString = curl_exec($ch);


            return json_decode($responseString, true);
        }
    }
}
