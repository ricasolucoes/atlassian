<?php namespace Atlassian\Space;

use Atlassian\ConfluenceClient;
use Atlassian\ConfluenceException;

use Atlassian\Constants;
use Symfony\Component\VarDumper\VarDumper;

/**
 * Confluence Space REST Service class
 *
 * @package Atlassian\Space
 */
class SpaceService extends ConfluenceClient
{
    // override parent uri
    public $url = '/' ;

    private $defaultParam = [
                    'limit' => 25,
                    'start' => 0,
                    'type' => 'global',
                    'status' => 'current',
                    ];

    /**
     * get question list
     *
     * @param  spaceKeyArray a list of space keys
     * @param  paramArray parameter array
     * @return mixed
     * @throws \Atlassian\ConfluenceException
     */
    public function getSpace($spaceKeysParam, $paramArray = null)
    {
        // set default param
        if (empty($paramArray)) {
            $paramArray = $this->defaultParam;
        }

        $queryParam = null;
        if (!empty($spaceKeysParam)) {
            $spaceParam = '&';
            foreach ($spaceKeysParam as $k) {
                $spaceParam = $spaceParam . 'spaceKey=' . $k . '&';
            }

            $queryParam = 'api/space?' . $spaceParam;
        } else {
            $queryParam = 'api/space?' . http_build_query($paramArray);
        }

        $ret = $this->exec($this->url . $queryParam, null);

        $ar = json_decode($ret);

        return $searchResults = $this->json_mapper->mapArray(
            $ar->results,  new \ArrayObject(), '\Atlassian\Space\Space'
        );
    }

}