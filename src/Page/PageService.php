<?php namespace Atlassian\Page;

use Atlassian\ConfluenceClient;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;

class PageService extends ConfluenceClient
{
    public $uri = '/api/content';

    /**
     * @param  $pageOrAttachmentId
     * @return \Atlassian\Page\Page
     * @throws \JsonMapper_Exception
     * @throws \Atlassian\ConfluenceException
     */
    public function getPage($pageOrAttachmentId)
    {
        $url = sprintf('%s/%s', $this->uri, $pageOrAttachmentId);

        $ret = $this->exec($url);

        return $page = $this->json_mapper->map(
            json_decode($ret), new Page()
        );
    }

    public function updatePage($pageOrAttachmentId)
    {
        $url = sprintf('%s/%s', $this->uri, $pageOrAttachmentId);

        $ret = $this->exec($url);

        return $page = $this->json_mapper->map(
            json_decode($ret), new Page()
        );
    }

    public function deletePage($pageOrAttachmentId): string
    {
        $url = sprintf('%s/%s', $this->uri, $pageOrAttachmentId);

        $ret = $this->exec($url, null, 'DELETE');

        return $this->http_response;
    }

    public function getChildPage($pageId): Page
    {
        $p = new Page();

        // get attachements
        $url = sprintf('%s/%s/child/page', $this->uri, $pageId);

        $ret = $this->exec($url);

        $atts = json_decode($ret);

        $p->attachments = $this->json_mapper->mapArray(
            $atts->results,  new \ArrayObject(), '\Atlassian\Page\Attachment'
        );

        // child page
        $url = sprintf('%s/%s/child/page', $this->uri, $pageId);

        $ret = $this->exec($url);
        $children = json_decode($ret);

        $p->children = $children->results;

        return $p;
    }

    /**
     * get current page's attachements list
     *
     * @param  $pageId
     * @return Page
     * @throws \Atlassian\ConfluenceException
     */
    public function getAttachmentList($pageId)
    {
        $p = new Page();

        // get attachements
        $url = sprintf('%s/%s/child/attachment', $this->uri, $pageId);

        $ret = $this->exec($url);

        $atts = json_decode($ret);

        $p->attachments = $this->json_mapper->mapArray(
            $atts->results,  new \ArrayObject(), '\Atlassian\Page\Attachment'
        );

        // child page
        $url = sprintf('%s/%s/child/page', $this->uri, $pageId);

        $ret = $this->exec($url);
        $children = json_decode($ret);

        $p->children = $children->results;

        return $p;
    }

    /**
     * download all attachment in the current page
     *
     * @param $pageId
     * @param $destination output directory
     *
     * @return array
     *
     * @throws \Atlassian\ConfluenceException
     *
     * @psalm-return list<mixed>
     */
    public function downloadAttachments($pageId, $destination = '.' ): array
    {
        $page = $this->getPage($pageId);

        // get attachements
        $url = sprintf('%s/%s/child/attachment', $this->uri, $pageId);

        $ret = $this->exec($url);

        $atts = json_decode($ret);

        $attachments = $this->json_mapper->mapArray(
            $atts->results,  new \ArrayObject(), '\Atlassian\Page\Attachment'
        );

        $files = [];

        foreach($attachments as $a) {
            array_push($files, $a->title);

            $url =  $this->getConfiguration()->getHost() . $a->_links->download;
            $content = $this->exec($url, null, null, true);

            $adapter = new Local($destination);
            $filesystem = new Filesystem($adapter);

            $fileName = $a->title;
            if (defined('PHP_WINDOWS_VERSION_MAJOR') === true) {
                $fileName = iconv('UTF-8', 'CP949', $fileName);
            }

            $filesystem->put($page->title . DIRECTORY_SEPARATOR . $fileName, $content);

        }

        return $files;
    }
}