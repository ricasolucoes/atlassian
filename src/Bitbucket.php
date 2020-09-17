<?php

namespace Atlassian;

/**
 * Atlassian Jira REST API wrapper for Atlassian
 */
class Bitbucket
{
    protected $uri;
    protected $username;
    protected $password;
    protected $encodedCredential;
    /**
     * authenticate - log in to the bitbucket api
     *
     * @param string $uri URI
     * @param string $username Username
     * @param string $password Password
     *
     * @return bool
     */
    public function authenticate($uri, $username, $password, $team, $repo)
    {
        $this->uri = $uri;
        $this->username = $username;
        $this->password = $password;
        $this->team = $team;
        $this->repo = $repo;
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
     * getPr - retrieve a pull request
     *
     * @param string $key PR id
     *
     * @return array
     */
    public function getPr($key)
    {
        if ($this->isAuthenticated()) {
            $url = $this->uri . "/2.0/repositories/" . $this->team . "/" . $this->repo . "/" . "pullrequests/" . $key;
            $ch = $this->prepCurl($url);

            $responseString = curl_exec($ch);

            $curlInfo = curl_getinfo($ch);

            return json_decode($responseString, true);
        }
    }

    /**
     * getCommit - retrieve details of a given commit id
     *
     * @param string $key commit id
     *
     * @return array
     */
    public function getCommit($key)
    {
        if ($this->isAuthenticated()) {
            $url = $this->uri . "/2.0/repositories/" . $this->team . "/" . $this->repo . "/" . "commit/" . $key;
            $ch = $this->prepCurl($url);

            $responseString = curl_exec($ch);

            $curlInfo = curl_getinfo($ch);

            return json_decode($responseString, true);
        }
    }

    /**
     * getPrsByKey - retrieve list of pull requests by jira key
     *
     * @param string $key jira key
     *
     * @return array (containing id's of pr's)
     */
    public function getPrsByKey($key)
    {
        if ($this->isAuthenticated()) {
            $url = $this->uri . "/2.0/repositories/" . $this->team . "/" . $this->repo . "/" . "pullrequests?state=OPEN&q=source.branch.name+%7E+%22" . $key . "%22+AND+state+%3D+%22OPEN%22";
            $ch = $this->prepCurl($url);

            $responseString = curl_exec($ch);
            $result = json_decode($responseString, true);

            $curlInfo = curl_getinfo($ch);

            $prIds = [];
            if (array_key_exists('values', $result)) {
                foreach ($result['values'] as $value) {
                    array_push($prIds, $value['id']);
                }
            }
            return $prIds;
        }
    }

    /**
     * getBranchesByKey - retrieve list of bitbucket branches by jira key
     *
     * @param string $key jira key
     *
     * @return array (containing branch names)
     */
    public function getBranchesByKey($key)
    {
        if ($this->isAuthenticated()) {
            $url = $this->uri . "/2.0/repositories/" . $this->team . "/" . $this->repo . "/" . "refs/branches?q=name+%7E+%22" . $key . "%22";
            $ch = $this->prepCurl($url);

            $responseString = curl_exec($ch);
            $result = json_decode($responseString, true);

            $curlInfo = curl_getinfo($ch);
            $result = json_decode($responseString, true);
            $branches = [];
            if (array_key_exists('values', $result)) {
                foreach ($result['values'] as $value) {
                    array_push($branches, $value['name']);
                }
            }
            return $branches;
        }
    }

    /**
     * addPrComment - add a comment to a pull request
     *
     * @param string $key PR id
     * @param string $comment Text of the comment
     *
     * @return array
     */
    public function addComment($key, $comment)
    {
        $url = $this->uri . "/1.0/repositories/" . $this->team . "/" . $this->repo . "/" . "pullrequests/" . $key . "/comments";
        $headers = array(
            'Authorization: Basic ' . $this->encodedCredential,
            'Content-Type: application/json;charset=UTF-8'
        );

        $data = ['content' => $comment];
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
     * getOpenPrIdsForBranch - retrieve ids of all open prs for this branch
     *
     * @param string $branch branch name
     *
     * @return array
     */
    public function getOpenPrIdsForBranch($branch)
    {
        if ($this->isAuthenticated()) {
            $url = $this->uri . "/2.0/repositories/" . $this->team . "/" . $this->repo . "/" . "pullrequests?state=OPEN&q=source.branch.name+%3D+%22" . $branch . "%22+AND+state+%3D+%22OPEN%22";
            $ch = $this->prepCurl($url);

            $responseString = curl_exec($ch);

            $curlInfo = curl_getinfo($ch);

            $result = json_decode($responseString, true);

            $prIds = [];
            if (array_key_exists('values', $result)) {
                foreach ($result['values'] as $value) {
                    array_push($prIds, $value['id']);
                }
            }

            return $prIds;
        }
    }
    /**
     * mergePr - merge a pull request - to be used for automated launches
     *
     * @param string $key PR id
     *
     * @return array
     */
    public function mergePr($key)
    {
        $url = $this->uri . "/2.0/repositories/" . $this->team . "/" . $this->repo . "/" . "pullrequests/" . $key . "/merge";
        $headers = array(
            'Authorization: Basic ' . $this->encodedCredential,
            'Content-Type: application/json;charset=UTF-8'
        );

        $data = [];
        $dataString = json_encode($data, JSON_PRETTY_PRINT);

        $ch = $this->prepCurl($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
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
     * declinePr - decline a pull request
     *
     * @param string $key PR id
     *
     * @return array
     */
    public function declinePr($key)
    {
        $url = $this->uri . "/2.0/repositories/" . $this->team . "/" . $this->repo . "/" . "pullrequests/" . $key . "/decline";
        $headers = array(
            'Authorization: Basic ' . $this->encodedCredential,
            'Content-Type: application/json;charset=UTF-8'
        );

        $data = ['reason' => 'failed automated build'];
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
     * runPipeline - execute a given pipeline
     *
     * @param string $branch branchname
     * @param string $commit commit hash
     * @param string $pipeline name of pipeline
     *
     * @return array
     */

    /*
    $ curl -X POST -is -u username:password \
      -H 'Content-Type: application/json' \
     https://api.bitbucket.org/2.0/repositories/jeroendr/meat-demo2/pipelines/ \
     -d '
      {
         "target": {
          "commit": {
             "hash":"a3c4e02c9a3755eccdc3764e6ea13facdf30f923",
             "type":"commit"
           },
           "selector": {
              "type": "custom",
              "pattern": "Deploy to production"
           },
           "type": "pipeline_ref_target",
           "ref_name": "master",
           "ref_type": "branch"
         }
      }'
    */

    public function runPipeline($branch, $pipeline)
    {
        $url = $this->uri . "/2.0/repositories/" . $this->team . "/" . $this->repo . "/" . "pipelines/";
        $headers = array(
            'Authorization: Basic ' . $this->encodedCredential,
            'Content-Type: application/json;charset=UTF-8'
        );

        $data = [
        'target' => [
#			'commit' => [
#				'hash' => $commit,
#				'type' => 'commit'
#			],
            'selector' => [
                'type' => 'custom',
                'pattern' => $pipeline
            ],
            'type' => 'pipeline_ref_target',
            'ref_type' => 'branch',
            'ref_name' => $branch
        ]
    ];
        $dataString = json_encode($data, JSON_PRETTY_PRINT);
        echo var_export($dataString, true) . "\n";
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
}
