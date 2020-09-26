<?php


namespace Atlassian\Entity;

class ConfluencePage
{
    /**
     * @var int $id
     */
    private $id;
    /**
     * @var string $title
     */
    private $title;
    /**
     * @var string $space
     */
    private $space;
    /**
     * @var array $ancestors
     */
    private $ancestors = array();
    /**
     * @var string $content
     */
    private $content;
    /**
     * @var int $version
     */
    private $version;
    /**
     * @var array $children
     */
    private $children = array();
    /**
     * @var string $url
     */
    private $url;
    /**
     * @var string
     */
    private $type;
    /**
     * @var
     */
    private $createdDate;

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return self
     */
    public function setType($type): self
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     *
     * @return self
     */
    public function setId($id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     *
     * @return self
     */
    public function setTitle($title): self
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSpace()
    {
        return $this->space;
    }

    /**
     * @param mixed $space
     *
     * @return self
     */
    public function setSpace($space): self
    {
        $this->space = $space;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAncestors()
    {
        return $this->ancestors;
    }

    /**
     * @param mixed $ancestors
     *
     * @return self
     */
    public function setAncestors($ancestors): self
    {
        $this->ancestors = $ancestors;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     *
     * @return self
     */
    public function setContent($content): self
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @return int
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param int $version
     *
     * @return self
     */
    public function setVersion($version): self
    {
        $this->version = $version;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param mixed $children
     *
     * @return self
     */
    public function setChildren($children): self
    {
        $this->children = $children;
        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     *
     * @return self
     */
    public function setUrl($url): self
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCreatedDate()
    {
        return $this->createdDate;
    }

    /**
     * @param mixed $createdDate
     *
     * @return self
     */
    public function setCreatedDate($createdDate): self
    {
        $this->createdDate = $createdDate;
        return $this;
    }
}