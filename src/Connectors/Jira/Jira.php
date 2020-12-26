<?php
/**
 * https://github.com/lesstif/php-jira-rest-client
 */

namespace Atlassian\Connectors\Jira;

use Illuminate\Database\Eloquent\Model;
use Log;
use App\Models\User;
use Integrations\Connectors\Connector;
use JiraRestApi\Configuration\ArrayConfiguration;

class Jira extends Connector
{
    public static $ID = 9;

    public $registersForPage = 10;


    protected function getConnection($token = false)
    {
        return $this;
    }

    protected function getPaginate($page = 1)
    {
        return [
            'startAt' => (($this->registersForPage*$page)-$this->registersForPage),
            'maxResults' => $this->registersForPage,
            // 'orderBy' => 'name',
            //'expand' => null,
        ];
    }

    protected function getConfig($token)
    {
        return new ArrayConfiguration(
            array(
                 'jiraHost' => $token->account->customize_url,
                 // for basic authorization:
                 'jiraUser' => $token->account->email,
                 'jiraPassword' => $token->token,
                 // to enable session cookie authorization (with basic authorization only)
                //  'cookieAuthEnabled' => true,
                //  'cookieFile' => storage_path('jira-cookie.txt'),
                //  // if you are behind a proxy, add proxy settings
                //  "proxyServer" => 'your-proxy-server',
                //  "proxyPort" => 'proxy-port',
                //  "proxyUser" => 'proxy-username',
                //  "proxyPassword" => 'proxy-password',
            )
        );
    }
}
