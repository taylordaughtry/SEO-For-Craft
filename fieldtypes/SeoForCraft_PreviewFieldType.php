<?php
namespace Craft;

class SeoForCraft_PreviewFieldType extends BaseFieldType
{

    public function getName()
    {
        return Craft::t('Preview');
    }

    public function defineContentAttribute()
    {
        return false;
    }

    /**
     * This method generates the 'Snippet Preview' element on entry detail
     * pages. Note that this fieldtype does not store data, and simply servces
     * as an easy way to add HTML to the page for the user's visual reference.
     *
     * @public
     * @param string $name The field name
     * @param string $value The stored value
     * @return string The HTML required to display the element
     */
    public function getInputHtml($name, $value)
    {
        $oldPath = craft()->path->getTemplatesPath();
        $newPath = craft()->path->getPluginsPath().'seoforcraft/templates';
        craft()->path->setTemplatesPath($newPath);

        craft()->templates->includeCssResource('seoforcraft/css/field.css');
        craft()->templates->includeJsResource('seoforcraft/js/field.js');

        $vars = array(
            'context' => $this->element
        );

        $html = craft()->templates->render('preview', $vars);

        craft()->path->setTemplatesPath($oldPath);

        return $html;
    }
}