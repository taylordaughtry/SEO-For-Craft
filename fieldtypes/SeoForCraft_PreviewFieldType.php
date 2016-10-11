<?php
namespace Craft;

class SeoForCraft_PreviewFieldType extends BaseFieldType
{

    public function getName()
    {
        return Craft::t('SEO');
    }

    public function defineContentAttribute()
    {
        return false;
    }

    /**
     * This method generates the 'Snippet Preview' element on entry detail
     * pages. Note that this fieldtype does not store data, and simply serves
     * as an easy way to add HTML to the page for the user's visual reference.
     *
     * @public
     * @param string $name The field name
     * @param string $value The stored value
     * @return string The HTML required to display the element
     */
    public function getInputHtml($name, $value)
    {
        craft()->templates->includeCssResource('seoforcraft/css/field.css');
        craft()->templates->includeJsResource('seoforcraft/js/field.js');
        craft()->templates->includeJsResource('seoforcraft/js/textstatistics.js');

        $vars = array(
            'context' => $this->element
        );

        $html = craft()->templates->render('seoforcraft/field', $vars);

        return $html;
    }
}