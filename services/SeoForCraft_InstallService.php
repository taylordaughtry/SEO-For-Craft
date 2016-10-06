<?php
namespace Craft;

class SeoForCraft_InstallService extends BaseApplicationComponent
{
	/**
	 * This installs all required elements for the plugin. That includes
	 * transforms, fields, and field groups.
	 *
	 * @public
	 * @return void
	 */
	public function install()
	{
		$this->installGroups();
		$this->installFields();
		$this->installTransforms();
		$this->installSources();
	}

	/**
	 * Cleanly removes all elements this plugin has added. If you're wondering
	 * where the 'uninstallFields' method is, Craft automatically removes
	 * fields whose parent field group has been removed.
	 *
	 * @public
	 * @return void
	 */
	public function uninstall()
	{
		$this->unInstallGroups();
		$this->unInstallTransforms();
		$this->unInstallSources();
	}

	/**
	 * Creates a 'Metadata' field group so all the plugin's fields can be
	 * easily referenced.
	 *
	 * @public
	 * @return void
	 */
	public function installGroups()
	{
		$group = new FieldGroupModel();
		$group->name = 'Metadata';

		if (craft()->fields->saveGroup($group)) {
			SeoForCraftPlugin::log('\'Metadata\' Field Group created.', LogLevel::Info, true);

			craft()->seoForCraft->saveSetting('metaGroupId', $group->id);
		} else {
			SeoForCraftPlugin::log('A \'Metadata\' Field Group is already present.', LogLevel::Warning, true);
		}
	}

	/**
	 * Removes the 'Metadata' field group added by the plugin, along with all
	 * its fields.
	 *
	 * @public
	 * @return void
	 */
	public function unInstallGroups()
	{
		craft()->fields->deleteGroupById(craft()->seoForCraft->getSetting('metaGroupId'));
	}

	/**
	 * Adds fields required by the plugin. These can be easily added to any
	 * Section by dragging the 'Metadata' field group into any section layout.
	 *
	 * This method takes a DRY approach to field creation; the $field array
	 * stores any dynamic values for each field. Then this array is used at the
	 * bottom to create each field using those dynamic values.
	 *
	 * @public
	 * @return void
	 */
	public function installFields()
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
			),
			array(
				'name' => 'Open Graph Image',
				'handle' => 'ogImage',
				'instructions' => 'Upload an image that\'s at least 1200 x 630, in any valid image format.',
				'type' => 'Assets',
				'settings' => array(
					'useSingleFolder' => 1,
					'singleUploadLocationSource' => craft()->seoForCraft->getSetting('sourceId'),
					'restrictFiles' => 1,
					'allowedKinds' => array(
						'image'
					),
					'limit' => 1,
					'viewMode' => 'large',
					'selectionLabel' => 'Add an Image'
				)
			),
			array(
				'name' => 'Twitter Title',
				'handle' => 'twitterTitle',
				'instructions' => 'Specify an optional title to be displayed on the Twitter Card.',
				'type' => 'PlainText',
				'settings' => array(
					'maxLength' => '140'
				)
			),
			array(
				'name' => 'Twitter Description',
				'handle' => 'twitterDescription',
				'instructions' => 'Specify an optional description to be displayed on the Twitter Card.',
				'type' => 'PlainText',
				'settings' => array(
					'maxLength' => '200'
				)
			),
			array(
				'name' => 'Twitter Image',
				'handle' => 'twitterImage',
				'instructions' => 'Upload an image that\'s at least 120 x 120, in any valid image format.',
				'type' => 'Assets',
				'settings' => array(
					'useSingleFolder' => 1,
					'singleUploadLocationSource' => craft()->seoForCraft->getSetting('sourceId'),
					'restrictFiles' => 1,
					'allowedKinds' => array(
						'image'
					),
					'limit' => 1,
					'viewMode' => 'large',
					'selectionLabel' => 'Add an Image'
				)
			),
			array(
				'name' => 'Twitter Large Image',
				'handle' => 'twitterLargeImage',
				'instructions' => 'Upload an image that\'s at least 280 x 150, in any valid image format.',
				'type' => 'Assets',
				'settings' => array(
					'useSingleFolder' => 1,
					'singleUploadLocationSource' => craft()->seoForCraft->getSetting('sourceId'),
					'restrictFiles' => 1,
					'allowedKinds' => array(
						'image'
					),
					'limit' => 1,
					'viewMode' => 'large',
					'selectionLabel' => 'Add an Image'
				)
			),
			array(
				'name' => 'Twitter Type',
				'handle' => 'twitterType',
				'instructions' => 'What type should this entry be?',
				'type' => 'Dropdown',
				'settings' => array(
					'options' => array(
						array(
							'label' => 'Summary',
							'value' => 'summary'
						),
						array(
							'label' => 'Summary Large Image',
							'value' => 'summaryLargeImage'
						)
					)
				)
			)
		);

		$groupId = craft()->seoForCraft->getSetting('metaGroupId');

		foreach ($fields as $field) {
			$model = new FieldModel();
			$model->groupId      = $groupId;
			$model->name         = $field['name'];
			$model->handle       = $field['handle'];
			$model->instructions = $field['instructions'];
			$model->translatable = true;
			$model->type         = $field['type'];
			$model->settings = $field['settings'];

			if (! craft()->fields->saveField($model)) {
				SeoForCraftPlugin::log('Could not save the ' . $field['name'] . ' field.', LogLevel::Warning, true);
			}
		}
	}

	/**
	 * Adds the required transforms. These dimensions are pulled directly from
	 * their respective sites' optimal dimension suggestions.
	 *
	 * @public
	 * @return void
	 */
	public function installTransforms()
	{
		$transforms = array(
			array(
				'name' => 'Open Graph Image Transform',
				'handle' => 'ogImageTransform',
				'width' => 1200,
				'height' => 630,
				'quality' => 82
			),
			array(
				'name' => 'Twitter Image Transform',
				'handle' => 'twitterImageTransform',
				'width' => 120,
				'height' => 120,
				'quality' => 82
			),
			array(
				'name' => 'Twitter Large Image Transform',
				'handle' => 'twitterLargeImageTransform',
				'width' => 280,
				'height' => 150,
				'quality' => 82
			),
		);

		$ids = [];

		foreach ($transforms as $transform) {
			$model = new AssetTransformModel();

			$model->name = $transform['name'];
			$model->handle = $transform['handle'];
			$model->width = $transform['width'];
			$model->height = $transform['height'];
			$model->quality = $transform['quality'];

			if (! craft()->assetTransforms->saveTransform($model)) {
				SeoForCraftPlugin::log('Could not create \'' . $model['name'] . '\' Transform.', LogLevel::Info, true);
			} else {
				$ids[] = $model->id;
			}
		}

		craft()->seoForCraft->saveSetting('transformIds', base64_encode(serialize($ids)));
	}

	/**
	 * Uninstalls all transforms added by the plugin.
	 *
	 * @public
	 * @return void
	 */
	public function unInstallTransforms()
	{
		$ids = craft()->seoForCraft->getSetting('transformIds');

		if ($ids) {
			$ids = unserialize(base64_decode($ids));

			foreach ($ids as $id) {
				craft()->assetTransforms->deleteTransform($id);
			}
		}
	}

	/**
	 * Adds a source for the root directory of the website. The root directory
	 * is where many popular social services look for social images. However,
	 * this location can be modified to fit your project if needed; just update
	 * the source after installation.
	 *
	 * @public
	 * @return void
	 */
	public function installSources()
	{
		$source = new AssetSourceModel();
		$source->name = 'Root';
		$source->handle = 'root';
		$source->settings = array(
			'path' => $_SERVER['DOCUMENT_ROOT'],
			'url' => '/',
			'publicURLs' => 1
		);

		craft()->assetSources->saveSource($source);

		SeoForCraftPlugin::log('\'Root\' Asset Source created.', LogLevel::Info, true);

		craft()->seoForCraft->saveSetting('sourceId', $source->id);
	}

	/**
	 * Removes the sources installed by this plugin.
	 *
	 * @public
	 * @return void
	 */
	public function unInstallSources()
	{
		$id = craft()->seoForCraft->getSetting('sourceId');

		craft()->assetSources->deleteSourceById($id);
	}
}