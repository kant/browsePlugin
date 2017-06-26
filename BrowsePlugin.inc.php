<?php

import('lib.pkp.classes.plugins.GenericPlugin');
import("plugins.generic.browse.classes.BrowseArticle");

class BrowsePlugin extends GenericPlugin {
    /**
     * Get the display name of this plugin
     * @return string
     */
    function getDisplayName() {
        return __('plugins.generic.browse.displayName');
    }

    /**
     * Get the description of this plugin
     * @return string
     */
    function getDescription() {
        return __('plugins.generic.browse.description');
    }

    /**
     * @copydoc PKPPlugin::getTemplatePath
     */
    function getTemplatePath($inCore = false) {
        return parent::getTemplatePath($inCore) . 'templates/';
    }

    /**
     * Called as a plugin is registered to the registry
     * @param $category String Name of category plugin was registered to
     * @return boolean True iff plugin initialized successfully; if false,
     * 	the plugin will not be registered.
     */
    function register($category, $path) {
        $success = parent::register($category, $path);
        if (!Config::getVar('general', 'installed') || defined('RUNNING_UPGRADE')) return true;
        if ($success && $this->getEnabled()) {

            HookRegistry::register('Templates::Index::journal', array($this, 'browseLatest'), HOOK_SEQUENCE_NORMAL);

            // Add stylesheet and javascript
            HookRegistry::register('TemplateManager::display',array($this, 'displayCallback'));

        }
        return $success;
    }

    function displayCallback($hookName, $params) {
        $template = $params[1];
        $output =& $params[2];

        if ($template != 'frontend/pages/indexJournal.tpl') return false;

        $templateMgr = $params[0];
        $templateMgr->addStylesheet('browse1', Request::getBaseUrl() . DIRECTORY_SEPARATOR . $this->getPluginPath() . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'browse.css');
        $templateMgr->addJavaScript('jatsParser', Request::getBaseUrl() . DIRECTORY_SEPARATOR . $this->getPluginPath() . DIRECTORY_SEPARATOR .  'js' . DIRECTORY_SEPARATOR .'browse.js');

        return false;
    }

    function browseLatest($hookName, $params) {
        $smarty =& $params[1];
        $output =& $params[2];

        // Get articles except Editorial and News
        $publishedArticleDao = DAORegistry::getDAO('PublishedArticleDAO');
        $publishedArticleObjects = $publishedArticleDao->getPublishedArticlesByJournalId($journalId = null, $rangeInfo = null, $reverse = true);
        $publishedArticles = array();
        $publishedNews = array();

        $showArticlesCount = 0;
        $showNewsCount = 0;
        $xmlGalley = null;
        $browseArticles = array();
        while ((($showArticlesCount + $showNewsCount) < 19) && $publishedArticle = $publishedArticleObjects->next()) {
            if ($showArticlesCount < 10 && ($publishedArticle->getSectionId() != 5)) {
                $publishedArticles[]['articles'][] = $publishedArticle;
                $showArticlesCount = $showArticlesCount + 1;
            } elseif ($showNewsCount < 10 && ($publishedArticle->getSectionId() == 5)) {
                $publishedNews[]['articles'][] = $publishedArticle;
                $showNewsCount = $showNewsCount + 1;
            }
            if ($publishedArticle->getId() == 19) {
                $browseArticles = $this->slidersSearch($params, $publishedArticle, $browseArticles);
            } elseif ($publishedArticle->getId() == 26) {
                $browseArticles = $this->slidersSearch($params, $publishedArticle, $browseArticles);
            }
        }
        print_r($browseArticles);

        $smarty->assign('browseArticles', $browseArticles);
        $smarty->assign('publishedArticles', $publishedArticles);
        $smarty->assign('publishedNews', $publishedNews);

        $output .= $smarty->fetch($this->getTemplatePath() . 'browseLatest.tpl');
        return false;
    }

    /**
     * @see Plugin::getActions()
     */
    function getActions($request, $verb) {
        $router = $request->getRouter();
        import('lib.pkp.classes.linkAction.request.AjaxModal');
        return array_merge(
            $this->getEnabled()?array(
                new LinkAction(
                    'settings',
                    new AjaxModal(
                        $router->url($request, null, null, 'manage', null, array('verb' => 'settings', 'plugin' => $this->getName(), 'category' => 'generic')),
                        $this->getDisplayName()
                    ),
                    __('manager.plugins.settings'),
                    null
                ),
            ):array(),
            parent::getActions($request, $verb)
        );
    }

    /**
     * @see Plugin::manage()
     */
    function manage($args, $request) {
        switch ($request->getUserVar('verb')) {
            case 'settings':
                $context = $request->getContext();

                AppLocale::requireComponents(LOCALE_COMPONENT_APP_COMMON,  LOCALE_COMPONENT_PKP_MANAGER);
                $templateMgr = TemplateManager::getManager($request);
                $templateMgr->register_function('plugin_url', array($this, 'smartyPluginUrl'));

                $this->import('SettingsForm');
                $form = new SettingsForm($this, $context->getId());

                if ($request->getUserVar('save')) {
                    $form->readInputData();
                    if ($form->validate()) {
                        $form->execute();
                        return new JSONMessage(true);
                    }
                } else {
                    $form->initData();
                }
                return new JSONMessage(true, $form->fetch($request));
        }
        return parent::manage($args, $request);
    }

    /**
     * @param $params
     * @param $publishedArticle
     * @param $browseArticles BrowseArticle
     * @return array
     */
    public function slidersSearch($params, $publishedArticle, $browseArticles)
    {
        foreach ($publishedArticle->getGalleys() as $galley) {
            if ($galley && in_array($galley->getFileType(), array('application/xml', 'text/xml'))) {
                $xmlGalley = $galley;
                $submissionFile = $xmlGalley->getFile();

                $submissionFileDao = DAORegistry::getDAO('SubmissionFileDAO');
                import('lib.pkp.classes.submission.SubmissionFile'); // Constants
                $embeddableFiles = array_merge(
                    $submissionFileDao->getLatestRevisions($submissionFile->getSubmissionId(), SUBMISSION_FILE_PROOF),
                    $submissionFileDao->getLatestRevisionsByAssocId(ASSOC_TYPE_SUBMISSION_FILE, $submissionFile->getFileId(), $submissionFile->getSubmissionId(), SUBMISSION_FILE_DEPENDENT)
                );
                $referredArticle = null;
                $articleDao = DAORegistry::getDAO('ArticleDAO');
                foreach ($embeddableFiles as $embeddableFile) {
                    $params = array();

                    if ($embeddableFile->getFileType() == 'image/png' || $embeddableFile->getFileType() == 'image/jpeg') {

                        // Ensure that the $referredArticle object refers to the article we want
                        if (!$referredArticle || $referredArticle->getId() != $galley->getSubmissionId()) {
                            $referredArticle = $articleDao->getById($galley->getSubmissionId());
                        }
                        $fileUrl = Application::getRequest()->url(null, 'article', 'download', array($referredArticle->getBestArticleId(), $galley->getBestGalleyId(), $embeddableFile->getFileId()), $params);

                        //$imageUrlArray[$embeddableFile->getOriginalFileName()] = $fileUrl;
                        if ($embeddableFile->getOriginalFileName() == "carousel.png") {
                            $browseArticle = new BrowseArticle($publishedArticle->getLocalizedTitle(), $fileUrl, $publishedArticle->getBestArticleId());
                            array_push($browseArticles, $browseArticle);
                        }
                    }
                }
            }
        }
        return $browseArticles;
    }
}