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
		$fields = array(
			array(
				'name' => 'Snippet Preview',
				'handle' => 'preview',
				'instructions' => 'This is what your page could look like in search results.',
				'type' => 'SeoForCraft_Preview',
				'settings' => false
			),
			array(
				'name' => 'Meta Title',
				'handle' => 'metaTitle',
				'instructions' => 'This will show in search results as the title of this page.',
				'type' => 'PlainText',
				'settings' => array(
					'maxLength' => '60'
				)
			),
			array(
				'name' => 'Meta Description',
				'handle' => 'metaDescription',
				'instructions' => 'This is a short snippet that teases the user about the content on this page. Note that google doesn\'t always use this.',
				'type' => 'PlainText',
				'settings' => array(
					'maxLength' => '155'
				)
			),
			array(
				'name' => 'No Index',
				'handle' => 'noIndex',
				'instructions' => 'Ask search engines not to index this page.',
				'type' => 'Lightswitch',
				'settings' => array(
					'on' => 'false'
				)
			),
			array(
				'name' => 'Open Graph Type',
				'handle' => 'ogType',
				'instructions' => 'What type should this entry be?',
				'type' => 'Dropdown',
				'settings' => array(
					'options' => array(
						array(
							'label' => 'Article',
							'value' => 'article'
						),
						array(
							'label' => 'Website',
							'value' => 'website'
						)
					)
				)
			),
			array(
				'name' => 'Open Graph Title',
				'handle' => 'ogTitle',
				'instructions' => 'If this is left blank, Craft will try the Meta Title, and finally default to the Entry Title.',
				'type' => 'PlainText',
				'settings' => array(
					'maxLength' => '90'
				)
			),
			array(
				'name' => 'Open Graph Description',
				'handle' => 'ogDescription',
				'instructions' => 'This is displayed on Facebook when you share this page.',
				'type' => 'PlainText',
				'settings' => array(
					'maxLength' => '200'
				)
			)
		);

		foreach ($fields as $field) {
			$model = new FieldModel();
			$model->groupId      = $groupId;
			$model->name         = $field['name'];
			$model->handle       = $field['handle'];
			$model->instructions = $field['instructions'];
			$model->translatable = true;
			$model->type         = $field['type'];
			$model->settings = $field['settings'];

			if (! craft()->fields->saveField($model))
			{
				Craft::log('Could not save the ' . $field['name'] . ' field.', LogLevel::Warning);

				return false;
			}
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