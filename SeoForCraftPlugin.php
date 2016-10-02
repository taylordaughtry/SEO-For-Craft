<?php
namespace Craft;

class SeoForCraftPlugin extends BasePlugin
{
	public $description = 'The definitive plugin for producing effective search rankings with Craft content.';
	public $developer = 'Taylor Daughtry';
	public $developerUrl = 'https://github.com/taylordaughtry';
	public $docsUrl = 'https://github.com/taylordaughtry/seoforcraft';
	public $feedUrl = 'https://raw.githubusercontent.com/taylordaughtry/seoforcraft/master/releases.json';
	public $pluginName = 'SEO for Craft';
	public $schema = '0.1.0';
	public $version = '0.1.0';

	public function getName()
	{
		return $this->pluginName;
	}

	public function getDescription()
	{
		return $this->description;
	}

	public function getVersion()
	{
		return $this->version;
	}

	public function getSchemaVersion()
	{
		return $this->schema;
	}

	public function getDeveloper()
	{
		return $this->developer;
	}

	public function getDeveloperUrl()
	{
		return $this->developerUrl;
	}

	public function getDocumentationUrl()
	{
		return $this->docsUrl;
	}

	public function getReleaseFeedUrl()
	{
		return $this->feedUrl;
	}

	public function getIconPath()
	{
		return craft()->path->getPluginsPath() . 'seoforcraft/resources/icon.svg';
	}

	public function onAfterInstall()
	{
		craft()->seoForCraft_install->installGroups();
	}

	public function onBeforeUninstall()
	{
		craft()->seoForCraft_install->unInstallGroups();
	}

	public function init()
	{
		parent::init();

		craft()->templates->hook('generateMeta', function(&$context) {
			$oldPath = craft()->path->getTemplatesPath();
			$newPath = craft()->path->getPluginsPath().'seoforcraft/templates';

			craft()->path->setTemplatesPath($newPath);

			$data = array(
				'settings' => craft()->plugins->getPlugin('SeoForCraft')->getSettings(),
				'entry' => $context['entry']
			);

			$output = craft()->templates->render('meta', $data);

			craft()->path->setTemplatesPath($oldPath);

			return $output;
		});
	}

	public function getSettingsHtml()
	{
		return craft()->templates->render('seoforcraft/settings', array(
			'settings' => $this->getSettings()
		));
	}

	protected function defineSettings()
	{
		return array(
			'metaGroupId' => array(AttributeType::String),
			'googleId' => array(AttributeType::String)
		);
	}
}