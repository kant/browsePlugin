<?php

class BrowseArticle {
    private $title;
    private $imageUrl;
    private $articleUrl;
    private $abstract;
    private $articleSectionTitle;

    /**
     * BrowseArticle constructor.
     * @param $title string
     * @param $imageUrl string
     * @param $articleUrl string
     * @param $abstract string
     * @param $articleSectionTitle string
     */
    public function __construct($title, $imageUrl, $articleUrl, $abstract, $articleSectionTitle)
    {
        $this->title = $title;
        $this->imageUrl = $imageUrl;
        $this->articleUrl = $articleUrl;
        $this->abstract = $abstract;
        $this->articleSectionTitle = $articleSectionTitle;
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
     * @return string
     */
    public function getAbstract()
    {
        return $this->abstract;
    }

    /**
     * @param string $abstract
     */
    public function setAbstract($abstract)
    {
        $this->abstract = $abstract;
    }

    /**
     * @return string
     */
    public function getArticleSectionTitle()
    {
        return $this->articleSectionTitle;
    }

    /**
     * @param string $articleSectionTitle
     */
    public function setArticleSectionTitle($articleSectionTitle)
    {
        $this->articleSectionTitle = $articleSectionTitle;
    }

}