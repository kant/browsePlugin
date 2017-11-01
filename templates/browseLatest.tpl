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
                <div class="carousel-item {if $k==0}active{/if}">
                    <img class="slider d-block w-100" src="{$browseArticle->getImageUrl()}" alt="{$browseArticle->getTitle()}">
                    <div class="carousel-caption">
                        <a class="title-link" href="{url page="article" op="view" path=$browseArticle->getArticleUrl()}">
                            <h3>{$browseArticle->getTitle()|strip|escape:"html"}</h3>
                        </a>
                        {*
                        {if preg_match("/<p>(.*)<\/p>/U", $browseArticle->getAbstract()|strip_unsafe_html, $match)}
                            {foreach from=$match[1] item=summary}{strip}
                                <p class="slider-caption">
                                    {$summary|regex_replace:"/^\s*(<strong>.*<\/strong>)(?:[ ]*[:\.][ ]*)?/u":""}
                                </p>
                            {/strip}{/foreach}
                        {/if}
                        *}
                        <a class="read-full-carousel" href="{url page="article" op="view" path=$browseArticle->getArticleUrl()}">
                            {translate key="plugins.browse.read.article"}
                        </a>
                    </div>
                </div>
            {/foreach}
        </div>

        <!-- Controls -->
        <a class="carousel-control-prev" href="#carousel-article" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">{translate key="plugins.browse.previous"}</span>
        </a>
        <a class="carousel-control-next" href="#carousel-article" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">{translate key="plugins.browse.next"}</span>
        </a>
    </div>
</div>

{* browse latest *}
<div class="browse-latest-news">

    <div class="recent-articles">
        <h2 class="recent">{translate key="plugins.browse.recent.news"}</h2>
    </div>


    <div class="row">

        {foreach from=$publishedNews item=article key=k}
        <div class="news-block col-sm-6 col-md-6 col-lg-4">
            <div class="card">
                <a href="{url page="article" op="view" path=$article->getBestArticleId()}">
                 <img class="card-img-top" src="{$article->getLocalizedCoverImageUrl()|escape}" alt="Card image cap">
                </a>
                <div class="card-body">
                    <h3 class="news-cards card-title">
                        <a href="{url page="article" op="view" path=$article->getBestArticleId()}">
                            {$article->getLocalizedTitle()|strip|escape:"html"}
                        </a>
                    </h3>
                    <p class="card-text">
                    {if $article->getLocalizedAbstract()}
                        {if preg_match("/<p>(.*)<\/p>/U", $article->getLocalizedAbstract()|strip_unsafe_html, $match)}
                            {foreach from=$match[1] item=summary}{strip}
                                {$summary|regex_replace:"/^\s*(<strong>.*<\/strong>)(?:[ ]*[:\.][ ]*)?/u":""}
                            {/strip}{/foreach}
                        {/if}
                    {/if}
                    </p>
                </div>
                <div class="card-footer">
                    <small class="text-muted">{translate key="plugins.browse.published"}: {$article->getDatePublished()|date_format:"%Y-%m-%d"}</small>
                </div>
            </div>
        </div>
        {/foreach}
    </div>

</div>


<div class="latest-articles container">
    <div class="row">
    <div class="browse-latest-research col-lg-6">
        <div class="recent-articles">
            <h2 class="recent">{translate key="plugins.browse.recent.articles"}</h2>
        </div>
        <ol class="article-list">
            {foreach from=$publishedArticles item=article}
                <li class="article-item">
                    <div class="latest header">
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
                            <span class="sec-title badge badge-info">{$article->getSectionTitle()}</span>
                        </div>
                        <div class="additional-article-info">
                            <span class="published">{translate key="plugins.browse.published"}:</span><span class="date-published">{$article->getDatePublished()|date_format:"%Y-%m-%d"}</span><span class="article-id">{$article->getPages()|escape}</span>
                            {*<p class="views-articles"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>
                                {$article->getViews()}
                            </p>*}
                        </div>
                    </div>
                </li>
            {/foreach}{* articles *}
        </ol>
    </div>

    <div class="browse-latest-editorial col-lg-6">
        <div class="editorial">
            <h2 class="recent">{translate key="plugins.browse.recent.editorials"}</h2>
        </div>
        <ol class="article-list">
            {foreach from=$publishedEditorials item=article}
                <li class="article-item">
                    <div class="latest header">
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
                            <span class="sec-title badge badge-info">{$article->getSectionTitle()}</span>
                        </div>
                        <div class="additional-article-info">
                            <span class="published">{translate key="plugins.browse.published"}:</span><span class="date-published">{$article->getDatePublished()|date_format:"%Y-%m-%d"}</span><span class="article-id">{$article->getPages()|escape}</span>
                            {*<p class="views-articles"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>
                                {$article->getViews()}
                            </p>*}
                        </div>
                    </div>
                </li>
            {/foreach}{* articles *}
        </ol>
    </div>
    </div>
</div>
