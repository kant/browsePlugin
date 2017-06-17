<?php

import('lib.pkp.classes.plugins.GenericPlugin');

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

        return false;
    }

    function browseLatest($hookName, $params) {
        $smarty =& $params[1];
        $output =& $params[2];

        // Get articles
        $publishedArticleDao = DAORegistry::getDAO('PublishedArticleDAO');
        $publishedArticleObjects = $publishedArticleDao->getPublishedArticlesByJournalId();
        $publishedArticles = array();
        while ($publishedArticle = $publishedArticleObjects->next()) {
            $publishedArticles[]['articles'][] = $publishedArticle;
        }

        $smarty->assign('publishedArticles', $publishedArticles);

        $output .= $smarty->fetch($this->getTemplatePath() . 'browseLatest.tpl');
        return false;
    }
}