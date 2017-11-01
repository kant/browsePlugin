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

        //articles id for displaying from database
        $params = $this->getRequest();
        $journal = $params->getJournal();
        $sliderFirst = $this->getSetting($journal->getId(), 'sliderFirst');
        $sliderSecond = $this->getSetting($journal->getId(), 'sliderSecond');
        $sliderThird = $this->getSetting($journal->getId(), 'sliderThird');

        // Limit artcles number to retrieve
        import('lib.pkp.classes.db.DBResultRange');
        $rangeInfoNews = new DBResultRange(9, 1);
        $rangeInfoArticles = new DBResultRange(12, 1);
        $rangeInfoEditorials = new DBResultRange(12, 1);

        // Get articles except Editorial and News
        $publishedArticleDao = DAORegistry::getDAO('PublishedArticleDAO');
        $publishedArticleObjects = $publishedArticleDao->getPublishedArticlesBySection($journalId = null, $rangeInfoArticles, $reverse = true, "3,4,9");
        $publishedEditorialObjects = $publishedArticleDao->getPublishedArticlesBySection($journalId = null, $rangeInfoEditorials, $reverse = true, "5,10");
        $publishedNewsObjects = $publishedArticleDao->getPublishedArticlesBySection($journalId = null, $rangeInfoNews, $reverse = true, "11");

        $publishedArticles = array();  //array for researches, reviews, clinical cases

        $publishedNews = array();  //array for news

        $publishedEditorials = array(); //array for editorials, commentaries and education

        $xmlGalley = null;
        $browseArticles = array();
        while ($publishedArticle = $publishedArticleObjects->next()) {
            $publishedArticles[] = $publishedArticle;
            if ($publishedArticle->getId() == $sliderFirst) {
                $browseArticles = $this->slidersSearch($params, $publishedArticle, $browseArticles);
            } elseif ($publishedArticle->getId() == $sliderSecond) {
                $browseArticles = $this->slidersSearch($params, $publishedArticle, $browseArticles);
            } elseif ($publishedArticle->getId() == $sliderThird) {
                $browseArticles = $this->slidersSearch($params, $publishedArticle, $browseArticles);
            }
        }

        while ($publishedArticle = $publishedNewsObjects->next()) {
            $publishedNews[] = $publishedArticle;
        }

        while ($publishedArticle = $publishedEditorialObjects->next()) {
            $publishedEditorials[] = $publishedArticle;
            if ($publishedArticle->getId() == $sliderFirst) {
                $browseArticles = $this->slidersSearch($params, $publishedArticle, $browseArticles);
            } elseif ($publishedArticle->getId() == $sliderSecond) {
                $browseArticles = $this->slidersSearch($params, $publishedArticle, $browseArticles);
            } elseif ($publishedArticle->getId() == $sliderThird) {
                $browseArticles = $this->slidersSearch($params, $publishedArticle, $browseArticles);
            }
        }

        $smarty->assign('browseArticles', $browseArticles);
        $smarty->assign('publishedArticles', $publishedArticles);
        $smarty->assign('publishedNews', $publishedNews);
        $smarty->assign('publishedEditorials', $publishedEditorials);

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
                        if ($embeddableFile->getOriginalFileName() == "slider.png") {
                            $browseArticle = new BrowseArticle($publishedArticle->getLocalizedTitle(), $fileUrl, $publishedArticle->getBestArticleId(), $publishedArticle->getLocalizedAbstract(), $publishedArticle->getSectionTitle());
                            array_push($browseArticles, $browseArticle);
                        }
                    }
                }
            }
        }
        return $browseArticles;
    }
}