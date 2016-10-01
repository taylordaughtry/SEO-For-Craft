<?php
namespace Craft;

class SeoForCraftService extends BaseApplicationComponent
{
	public function installGroups()
	{
		$group = new FieldGroupModel();
		$group->name = 'Metadata';

		if (craft()->fields->saveGroup($group)) {
			$this->installFields($group->id);

			$this->saveSetting('metaGroupId', $group->id);

			return;
		}

		// TODO: Handle situation where a 'Metadata' field group already exists.
	}

	public function unInstallGroups()
	{
		craft()->fields->deleteGroupById($this->getSetting('metaGroupId'));
	}

	public function installFields($groupId)
	{
		$preview = new FieldModel();
		$preview->groupId      = $groupId;
		$preview->name         = 'Snippet Preview';
		$preview->handle       = 'preview';
		$preview->instructions = 'This is what your page could look like in search results.';
		$preview->translatable = true;
		$preview->type         = 'SeoForCraft_Preview';

		if (! craft()->fields->saveField($preview))
		{
			Craft::log('Could not save the Preview field.', LogLevel::Warning);

			return false;
		}

		$metaTitle = new FieldModel();
		$metaTitle->groupId      = $groupId;
		$metaTitle->name         = 'Meta Title';
		$metaTitle->handle       = 'metaTitle';
		$metaTitle->instructions = 'This will show in search results as the title of this page.';
		$metaTitle->translatable = true;
		$metaTitle->type         = 'PlainText';
		$metaTitle->settings = array(
			'maxLength' => '60'
		);

		if (! craft()->fields->saveField($metaTitle))
		{
			Craft::log('Could not save the Meta Title field.', LogLevel::Warning);

			return false;
		}

		$metaDescription = new FieldModel();
		$metaDescription->groupId      = $groupId;
		$metaDescription->name         = 'Meta Description';
		$metaDescription->handle       = 'metaDescription';
		$metaDescription->instructions = 'This is a short snippet that teases the user about the content on this page. Note that google doesn\'t always use this.';
		$metaDescription->translatable = true;
		$metaDescription->type         = 'PlainText';
		$metaDescription->settings = array(
			'maxLength' => '155'
		);

		if (! craft()->fields->saveField($metaDescription))
		{
			Craft::log('Could not save the Meta Description field.', LogLevel::Warning);

			return false;
		}

		$noIndex = new FieldModel();
		$noIndex->groupId      = $groupId;
		$noIndex->name         = 'No Index';
		$noIndex->handle       = 'noIndex';
		$noIndex->instructions = 'Ask search engines not to index this page.';
		$noIndex->translatable = true;
		$noIndex->type         = 'Lightswitch';
		$noIndex->settings = array(
			'on' => 'false'
		);

		if (! craft()->fields->saveField($noIndex))
		{
			Craft::log('Could not save the No Index field.', LogLevel::Warning);

			return false;
		}
	}

	public function getSetting($key)
	{
		$settings = craft()->plugins->getPlugin('SeoForCraft')->getSettings();

		return $settings[$key];
	}

	public function saveSetting($key, $value)
	{
		$pluginInstance = craft()->plugins->getPlugin('SeoForCraft');

		craft()->plugins->savePluginSettings($pluginInstance, array(
			$key => $value
		));
	}
}