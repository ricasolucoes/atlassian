<?php
require 'vendor/autoload.php';

use Atlassian\ConfluenceClient;
use Atlassian\Dumper;
use Atlassian\Page\Page;
use Atlassian\Page\PageService;

function save($dir, $fileName, $content)
{
    @mkdir($dir, 0755, true);
    $file = new SplFileObject($dir . '/' . $fileName, "w");
    $written = $file->fwrite($content);

    $file->fflush();

    echo " $fileName\n";
}

function download($url, $dir, $fileName)
{
    print("download " . $url);

    $c = new ConfluenceClient();

    $data = $c->exec($url, null, null, true);

    /*

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_USERPWD, "ricasolucoes:dbsdn07dbwn09");
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $data = curl_exec ($ch);

    Dumper::dd($data);

    curl_close ($ch);
    */

    save($dir, $fileName, $data);
}

function downLoadAttachment($pageId)
{
    $ps = new PageService();

    $page = $ps->getChild($pageId);

    foreach ($page->children as $a) {
        print($a->title . "\n");

        $tp = new PageService();

        $cps = $tp->getChild($a->id);

        foreach ($cps->children as $c) {
            $parent = str_replace("/", ",", $a->title);
            $child = str_replace("/", ",", $c->title);

            $path = $parent . '/' . $child;

            if (strpos($a->title, '관리') !== false) { // Management output
                $path = 'Outputs' . '/' . $path;

                // Get another
                $cp = new PageService();

                $tmp = $cp->getChild($c->id);

                foreach ($tmp as $att) {
                    $att = $tp->getPage($a->id);

                    download($att->download, $path, $a->title);

                    //save($path, $cps, 'bbb');
                }

                print("\t" . $c->title . "\n");
            } elseif (strpos($a->title, 'Development') !== false) { // Development output
                $dir = 'Outputs' . '/' . $dir;

                save($dir, $file, 'aaa');

                print("\t" . $c->title . "\n");
            } else {
                die("do not know Outputs " . $a->title);
            }
        }
    }
} // download


//$pageId = '59444134';

$ids = ['59446136', '59446138'];

foreach ($ids as $id) {
    $me = (new PageService())->getPage($id);

    $page = (new PageService())->getChild($id);

    $isDev = 0;

    //Dumper::dd($page);

    foreach ($page->children as $firstChild) {
        $path = 'Outputs/' . $me->title . '/' . $firstChild->title;



        if (strpos($me->title, 'MG1') !== false) { // Management output
            Dumper::dump($me->title . '/' . $firstChild->title);

            $prjCode = preg_replace("/[^A-Za-z0-9\-]/", "", $firstChild->title);

            $secondChild = (new PageService())->getChild($firstChild->id);
            //Dumper::dump(['id' => $firstChild->id,'count ' => count($secondChild)]);

            // Attachments from here
            foreach ($secondChild->attachments as $att) {
                //Dumper::dump($att);
                //$attaches = (new PageService())->getChild($c->id);

                //foreach ($attaches as $att) {
                //Dumper::dd($cc);

                //$attachs = (new PageService())->getChild($cc->id);

                //foreach($attachs->attachments as $att) {
                $url = 'https://wiki.ktnet.com/' . $att->_links->download;

                download($url, $path, 'SF-2017-DV-' . $prjCode . ' ' . $att->title);
                //Dumper::dd($att);
                    //}
                //}
            }


            //downLoadAttachment($a->id);

            //save($path, 'qwe', 'accd');

            //Dumper::dd('qwe');
        } elseif (strpos($me->title, 'DV') !== false) {  // Development output
            Dumper::dump($me->title . '/' . $firstChild->title);

            $secondChild = (new PageService())->getChild($firstChild->id);

            // One more step down
            foreach ($secondChild->children as $sc) {
                $prjCode = preg_replace("/[^A-Za-z0-9\-]/", "", $secondChild->title);

                Dumper::dump($sc);
                $attaches = (new PageService())->getChild($sc->id);

                (new PageService())->downloadAttachments($sc->id, "abc");

                foreach ($attaches as $att) {
                    //Dumper::dd($att);


                    //$attachs = (new PageService())->getChild($cc->id);

                    //foreach($attachs->attachments as $att) {
                    //$url = 'https://wiki.ktnet.com/' . $att->_links->download;

                    //download($url, $path, 'SF-2017-DV-' . $prjCode . ' ' . $att->title);
                    //Dumper::dd($att);
                    //}
                    //}
                }
            }
        } else { //I don't know
            //var_dump("Unknown title :" . $me->title);
        }
    }
}
