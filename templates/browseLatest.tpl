{* carousel *}

<div class = "article-slider col-md-12 col-lg-12">
    <div id="carousel-article" class="carousel slide" data-ride="carousel">
        <!-- Indicators -->
        <ol class="carousel-indicators">
            <li data-target="#carousel-article" data-slide-to="0" class="active"></li>
            <li data-target="#carousel-article" data-slide-to="1"></li>
            <li data-target="#carousel-article" data-slide-to="2"></li>
        </ol>

        <!-- Wrapper for slides -->
        <div class="carousel-inner" role="listbox">
            {foreach from=$browseArticles key=k item=browseArticle}
                <div class="item {if $k==0}active{/if}">
                    <img src="{$browseArticle->getImageUrl()}" alt="{$browseArticle->getTitle()}">
                    <div class="carousel-caption">
                        <a class="title-link" href="{url page="article" op="view" path=$browseArticle->getArticleUrl()}">
                            <h3>{$browseArticle->getTitle()|strip|escape:"html"}</h3>
                        </a>
                        {if preg_match("/<p>(.*)<\/p>/U", $browseArticle->getAbstract()|strip_unsafe_html, $match)}
                            {foreach from=$match[1] item=summary}{strip}
                                <p class="slider-caption">
                                    {$summary|regex_replace:"/^\s*(<strong>.*<\/strong>)(?:[ ]*[:\.][ ]*)?/u":""}
                                </p>
                            {/strip}{/foreach}
                        {/if}
                        <a class="read-full-carousel" href="{url page="article" op="view" path=$browseArticle->getArticleUrl()}">
                            {translate key="plugins.browse.read.article"}
                        </a>
                    </div>
                </div>
            {/foreach}
        </div>

        <!-- Controls -->
        <a class="left carousel-control" href="#carousel-article" role="button" data-slide="prev">
            <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
            <span class="sr-only">{translate key="plugins.browse.previous"}</span>
        </a>
        <a class="right carousel-control" href="#carousel-article" role="button" data-slide="next">
            <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
            <span class="sr-only">{translate key="plugins.browse.next"}</span>
        </a>
    </div>
</div>

{* browse latest *}
<div class="browse-latest-research col-xs-12 col-md-7 col-lg-7">
    <div class="recent-articles">
        <h2 class="recent">{translate key="plugins.browse.recent.articles"}</h2>
    </div>
    <ol class="article-list">
        {foreach name=sections from=$publishedArticles item=section key=sectionId}
            {foreach from=$section.articles item=article}
                <li class="article-item">
                    <div class="header">
                        <h3 class="article-title">
                            <a href="{url page="article" op="view" path=$article->getBestArticleId()}">
                                {$article->getLocalizedTitle()|strip|escape:"html"}
                            </a>
                        </h3>
                    </div>
                    <div class="footer">
                        <div class="authors">
                            {foreach from=$article->getAuthors() key=k item=author}
                                <span>
                                    {$author->getLastName()|strip|escape:"html"}
                                    {if $k<($article->getAuthors()|@count - 1)}
                                        {$author->getFirstName()|regex_replace:"/(?<=\w)\w+/":".,"}
                                    {else}
                                        {$author->getFirstName()|regex_replace:"/(?<=\w)\w+/":"."}
                                    {/if}
                                </span>
                            {/foreach}{* authors *}
                        </div>
                        {if $article->getLocalizedAbstract()}
                            <div class="summary">
                                <div class="summary-wrapper">
                                    {if preg_match("/<p>(.*)<\/p>/U", $article->getLocalizedAbstract()|strip_unsafe_html, $match)}
                                        {foreach from=$match[1] item=summary}{strip}
                                            <a class="for-summary" href="{url page="article" op="view" path=$article->getBestArticleId()}">
                                                {$summary|regex_replace:"/^\s*(<strong>.*<\/strong>)(?:[ ]*[:\.][ ]*)?/u":""}
                                            </a>
                                        {/strip}{/foreach}
                                    {/if}
                                </div>
                            </div>
                        {/if}
                        <div class="section">
                            <span class="sec-title label label-info">{$article->getSectionTitle()}</span>
                        </div>
                        <div class="additional-article-info">
                            <span class="published">{translate key="plugins.browse.published"}:</span><span class="date-published">{$article->getDatePublished()|date_format:"%Y-%m-%d"}</span><span class="article-id">{$article->getPages()|escape}</span>
                        </div>
                    </div>
                </li>
            {/foreach}{* articles *}
        {/foreach}{* sections *}
    </ol>
</div>

<div class="browse-latest-news col-xs-12 col-md-5 col-lg-5">
    <div class="news">
        <h2 class="recent">{translate key="plugins.browse.editorials.news"}</h2>
    </div>
    <ol class="article-list">
        {foreach name=sections from=$publishedNews item=section key=sectionId}
            {foreach from=$section.articles item=article}
                <li class="article-item">
                    <div class="header">
                        <h3 class="article-title">
                            <a href="{url page="article" op="view" path=$article->getBestArticleId()}">
                                {$article->getLocalizedTitle()|strip|escape:"html"}
                            </a>
                        </h3>
                    </div>
                    <div class="footer">
                        <div class="authors">
                            {foreach from=$article->getAuthors() key=k item=author}
                                <span>
                                    {$author->getLastName()|strip|escape:"html"}
                                    {if $k<($article->getAuthors()|@count - 1)}
                                        {$author->getFirstName()|regex_replace:"/(?<=\w)\w+/":".,"}
                                    {else}
                                        {$author->getFirstName()|regex_replace:"/(?<=\w)\w+/":"."}
                                    {/if}
                                </span>
                            {/foreach}{* authors *}
                        </div>
                        {if $article->getLocalizedAbstract()}
                            <div class="summary">
                                <div class="summary-wrapper">
                                    {if preg_match("/<p>(.*)<\/p>/U", $article->getLocalizedAbstract()|strip_unsafe_html, $match)}
                                        {foreach from=$match[1] item=summary}{strip}
                                            <a class="for-summary" href="{url page="article" op="view" path=$article->getBestArticleId()}">
                                                {$summary|regex_replace:"/^\s*(<strong>.*<\/strong>)(?:[ ]*[:\.][ ]*)?/u":""}
                                            </a>
                                        {/strip}{/foreach}
                                    {/if}
                                </div>
                            </div>
                        {/if}
                        <div class="section">
                            <span class="sec-title label label-info">{$article->getSectionTitle()}</span>
                        </div>
                        <div class="additional-article-info">
                            <span class="published">{translate key="plugins.browse.published"}:</span><span class="date-published">{$article->getDatePublished()|date_format:"%Y-%m-%d"}</span><span class="article-id">{$article->getPages()|escape}</span>
                        </div>
                    </div>
                </li>
            {/foreach}{* articles *}
        {/foreach}{* sections *}
    </ol>
</div>