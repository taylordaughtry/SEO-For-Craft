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

    public function getInputHtml($name, $value)
    {
        $oldPath = craft()->path->getTemplatesPath();
        $newPath = craft()->path->getPluginsPath().'seoforcraft/templates';
        craft()->path->setTemplatesPath($newPath);

        craft()->templates->includeCssResource('seoforcraft/css/preview.css');
        craft()->templates->includeJsResource('seoforcraft/js/preview.js');

        $vars = array(
            'context' => $this->element
        );

        $html = craft()->templates->render('preview', $vars);

        craft()->path->setTemplatesPath($oldPath);

        return $html;
    }
}