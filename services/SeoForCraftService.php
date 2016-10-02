<?php
namespace Craft;

class SeoForCraftService extends BaseApplicationComponent
{
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