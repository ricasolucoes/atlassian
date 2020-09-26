<?php namespace Atlassian\CQL;

use Atlassian\ConfluenceClient;

class CQLService extends ConfluenceClient
{
    public string $uri = '/api/content/search';

    public function search($paramArray = null)
    {
        $queryParam = '?' . 'cql=' . http_build_query($paramArray);

        $ret = $this->exec($this->uri . $queryParam, null);

        return $searchResults = $this->json_mapper->map(
            json_decode($ret), new CQLSearchResults()
        );
    }
}