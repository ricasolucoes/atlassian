<?php


namespace Atlassian\Tests\Entity;

use Atlassian\Tests\TestCase;
use Atlassian\Entity\ConfluencePage;

/**
 * Class ConfluencePageModelTest
 * @author  Yuxiao Tan <yuxiaota@gmail.com>
 */
class ConfluencePageTest extends TestCase
{
    /**
     * Test get space
     */
    public function testGetSpace(){
        $confluencePage = new ConfluencePage();
        $this->assertEquals($confluencePage->getSpace(), $confluencePage->getSpace());
    }

    /**
     * Test set space
     */
    public function testSetSpace(){
        $this->assertTrue(property_exists(ConfluencePage::class, 'space'));
        $confluencePage = new ConfluencePage();
        $confluencePage->setSpace('TEST');
        $this->assertEquals('TEST', $confluencePage->getSpace());
    }

    /**
     * Test set id
     */
    public function testSetId(){
        $this->assertTrue(property_exists(ConfluencePage::class, 'id'));
        $confluencePage = new ConfluencePage();
        $confluencePage->setId('123');
        $this->assertEquals('123', $confluencePage->getId());
    }

    /**
     * Test get id
     */
    public function testGetId(){
        $this->assertTrue(property_exists(ConfluencePage::class, 'title'));
        $confluencePage = new ConfluencePage();
        $this->assertEquals($confluencePage->getId(), $confluencePage->getId());
    }

}