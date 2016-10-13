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
        return AttributeType::Mixed;
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
        $id = craft()->templates->formatInputId($name);
        $namespacedId = craft()->templates->namespaceInputId($id);

        craft()->templates->includeCssResource('seoforcraft/css/field.css');
        craft()->templates->includeJsResource('seoforcraft/js/field.js');
        craft()->templates->includeJsResource('seoforcraft/js/textstatistics.js');

        $value['ogImage'] = isset($value['ogImage']) ? array(craft()->assets->getFileById($value['ogImage'])) : '';
        $value['twitterImage'] = isset($value['twitterImage']) ? array(craft()->assets->getFileById($value['twitterImage'])) : '';
        $value['twitterLargeImage'] = isset($value['twitterLargeImage']) ? array(craft()->assets->getFileById($value['twitterLargeImage'])) : '';

        $vars = array(
            'context' => $this->element,
            'id' => $namespacedId,
            'name' => $name,
            'values' => $value,
            'elementType' => craft()->elements->getElementType(ElementType::Asset)
        );

        return craft()->templates->render('seoforcraft/field', $vars);
    }
}