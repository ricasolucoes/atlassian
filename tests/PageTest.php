<?php

use \Atlassian\Page\PageService;
use Atlassian\Dumper;
use PHPUnit\Framework\TestCase;

class PageTest extends TestCase
{
    public function testGetPage()
    {
        global $argv, $argc;

        $pageId = '59452438';

        // override command line parameter

        if ($argc === 3) {
            Dumper::dump($argv);
            $pageId = $argv[2];
        }

        try {
            $ps = new PageService();

            $pages = $ps->getPage($pageId);

            //$this->assertClassNotHasAttribute('id', $p);
            foreach($pages as $page) {
                Dumper::dump($page);
                //exit(0);
            }

        } catch (\Atlassian\ConfluenceException $e) {
            $this->assertTrue(false, 'testGetSpace Failed : ' . $e->getMessage());
        }

        return $pageId;
    }

    /**
     * @depends testGetPage
     */
    public function testGetChildPage($pageId)
    {
        try {
            $ps = new PageService();

            $p = $ps->getChild($pageId);

            //print attachments
            $i = 0;
            //foreach($p->attachments as $a) {
            foreach($p->children as $a) {
                if ($i++ > 3) {
                    break;
                }

                Dumper::dd($a);
                $ret = $ps->deletePage($a->id);
                dump($ret);
                break;
            }

        } catch (\Atlassian\ConfluenceException $e) {
            $this->assertTrue(false, 'testGetSpace Failed : '.$e->getMessage());
        }

        return $pageId;
    }

}
