<?php

class BrowseArticle {
    private $title;
    private $imageUrl;
    private $articleUrl;
    private $articleId;

    /**
     * BrowseArticle constructor.
     * @param $title string
     * @param $imageUrl string
     * @param $articleUrl string
     */
    public function __construct($title, $imageUrl, $articleUrl)
    {
        $this->title = $title;
        $this->imageUrl = $imageUrl;
        $this->articleUrl = $articleUrl;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getImageUrl()
    {
        return $this->imageUrl;
    }

    /**
     * @param string $imageUrl
     */
    public function setImageUrl($imageUrl)
    {
        $this->imageUrl = $imageUrl;
    }

    /**
     * @return string
     */
    public function getArticleUrl()
    {
        return $this->articleUrl;
    }

    /**
     * @param string $articleUrl
     */
    public function setArticleUrl($articleUrl)
    {
        $this->articleUrl = $articleUrl;
    }

    /**
     * @return int
     */
    public function getArticleId()
    {
        return $this->articleId;
    }

    /**
     * @param int $articleId
     */
    public function setArticleId($articleId)
    {
        $this->articleId = $articleId;
    }

}