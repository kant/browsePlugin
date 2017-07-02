{**
 * plugins/generic/webFeed/templates/settingsForm.tpl
 *
 * Copyright (c) 2014-2017 Simon Fraser University
 * Copyright (c) 2003-2017 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Web feeds plugin settings
 *
 *}
<div id="browseSettings">
<div id="description">{translate key="plugins.generic.browse.description"}</div>

<h3>{translate key="plugins.generic.browse.settings"}</h3>

<script>
	$(function() {ldelim}
		// Attach the form handler.
		$('#browseSettingsForm').pkpHandler('$.pkp.controllers.form.AjaxFormHandler');
	{rdelim});
</script>

<form class="pkp_form" id="browseSettingsForm" method="post" action="{url router=$smarty.const.ROUTE_COMPONENT op="manage" category="generic" plugin=$pluginName verb="settings" save=true}">
	{csrf}
	{include file="controllers/notification/inPlaceNotification.tpl" notificationId="browseSettingsFormNotification"}

	{fbvFormArea id="browseSettingsFormArea"}
		{fbvFormSection list=true}
			{fbvElement type="text" id="sliderFirst" value=$sliderFirst label="plugins.generic.browse.settings.sliderFirst" size=$fbvStyles.size.SMALL}
            {fbvElement type="text" id="sliderSecond" value=$sliderSecond label="plugins.generic.browse.settings.sliderSecond" size=$fbvStyles.size.SMALL}
            {fbvElement type="text" id="sliderThird" value=$sliderThird label="plugins.generic.browse.settings.sliderThird" size=$fbvStyles.size.SMALL}
		{/fbvFormSection}
	{/fbvFormArea}

	{fbvFormButtons}
</form>

<p><span class="formRequired">{translate key="common.requiredField"}</span></p>
</div>
