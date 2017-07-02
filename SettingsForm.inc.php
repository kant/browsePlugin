<?php

/**
 * @file plugins/generic/browse/SettingsForm.inc.php
 *
 * Copyright (c) 2014-2017 Simon Fraser University
 * Copyright (c) 2003-2017 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class SettingsForm
 * @ingroup plugins_generic_webFeed
 *
 * @brief Form for managers to modify web feeds plugin settings
 */

import('lib.pkp.classes.form.Form');

class SettingsForm extends Form {

	/** @var int Associated context ID */
	private $_contextId;

	/** @var WebFeedPlugin Web feed plugin */
	private $_plugin;

	/**
	 * Constructor
	 * @param $plugin BrowsePlugin plugin
	 * @param $contextId int Context ID
	 */
	function __construct($plugin, $contextId) {
		$this->_contextId = $contextId;
		$this->_plugin = $plugin;

		parent::__construct($plugin->getTemplatePath() . 'settingsForm.tpl');
		$this->addCheck(new FormValidatorPost($this));
		$this->addCheck(new FormValidatorCSRF($this));
	}

	/**
	 * Initialize form data.
	 */
	function initData() {
		$contextId = $this->_contextId;
		$plugin = $this->_plugin;
		$this->setData('sliderFirst', $plugin->getSetting($contextId, 'sliderFirst'));
        $this->setData('sliderSecond', $plugin->getSetting($contextId, 'sliderSecond'));
        $this->setData('sliderThird', $plugin->getSetting($contextId, 'sliderThird'));
	}

	/**
	 * Assign form data to user-submitted data.
	 */
	function readInputData() {
		$this->readUserVars(array('sliderFirst', 'sliderSecond', 'sliderThird'));

		// check that recent items value is a positive integer
		if ((int) $this->getData('sliderFirst') <= 0) $this->setData('sliderFirst', '');
        if ((int) $this->getData('sliderSecond') <= 0) $this->setData('sliderSecond', '');
        if ((int) $this->getData('sliderThird') <= 0) $this->setData('sliderThird', '');
	}

	/**
	 * Fetch the form.
	 * @copydoc Form::fetch()
	 */
	function fetch($request) {
		$templateMgr = TemplateManager::getManager($request);
		$templateMgr->assign('pluginName', $this->_plugin->getName());
		return parent::fetch($request);
	}

	/**
	 * Save settings. 
	 */
	function execute() {
		$plugin = $this->_plugin;
		$contextId = $this->_contextId;

		$plugin->updateSetting($contextId, 'sliderFirst', $this->getData('sliderFirst'));
        $plugin->updateSetting($contextId, 'sliderSecond', $this->getData('sliderSecond'));
        $plugin->updateSetting($contextId, 'sliderThird', $this->getData('sliderThird'));
	}
}

?>
