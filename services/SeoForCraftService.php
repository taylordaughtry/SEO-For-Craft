<?php
namespace Craft;

class SeoForCraftService extends BaseApplicationComponent
{
	/**
	 * This method is an easy way to get a specific setting. It could be argued
	 * that storing the settings array in a property on the class would be more
	 * efficient, but I'm trading that microefficiency for code readability.
	 *
	 * @public
	 * @param string $key The key of the setting you'd like to retrieve
	 * @return mixed the value of the key passed
	 */
	public function getSetting($key)
	{
		$settings = craft()->plugins->getPlugin('SeoForCraft')->getSettings();

		$val = $settings[$key];

		return $val;
	}

	/**
	 * An easy way to save a setting. See the `getSetting` method for info.
	 *
	 * @public
	 * @param string $key The key of the setting you'd like to update
	 * @param mixed $value The value of the setting you'd like to update
	 * @return void
	 */
	public function saveSetting($key, $value)
	{
		$pluginInstance = craft()->plugins->getPlugin('SeoForCraft');

		craft()->plugins->savePluginSettings($pluginInstance, array(
			$key => $value
		));
	}
}