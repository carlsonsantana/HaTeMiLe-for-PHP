<?php
/**
 * Class CommonFunctions.
 * 
 * @package hatemile\util
 * @author Carlson Santana Cruz
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @copyright (c) 2018, HaTeMiLe
 */

namespace hatemile\util;

require_once join(DIRECTORY_SEPARATOR, array(
    dirname(__FILE__),
    'html',
    'HTMLDOMElement.php'
));

use \hatemile\util\html\HTMLDOMElement;

/**
 * The CommonFunctions class contains the used methods by HaTeMiLe classes.
 */
class CommonFunctions
{

    /**
     * The name of attribute for not modify the elements.
     * @var string
     */
    const DATA_IGNORE = 'data-ignoreaccessibilityfix';

    /**
     * The private constructor prevents that the class not can be initialized.
     */
    private function __construct()
    {
    }

    /**
     * Copy a list of attributes of a element for other element.
     * @param \hatemile\util\html\HTMLDOMElement $element1 The element that have
     * attributes copied.
     * @param \hatemile\util\html\HTMLDOMElement $element2 The element that copy
     * the attributes.
     * @param string[] $attributes The list of attributes that will be copied.
     */
    public static function setListAttributes(
        HTMLDOMElement $element1,
        HTMLDOMElement $element2,
        $attributes
    ) {
        foreach ($attributes as $attribute) {
            if ($element1->hasAttribute($attribute)) {
                $element2->setAttribute(
                    $attribute,
                    $element1->getAttribute($attribute)
                );
            }
        }
    }

    /**
     * Increase a item in a list.
     * @param string $list The list.
     * @param string $stringToIncrease The value of item.
     * @return string The HTML list with the item added, if the item not was
     * contained in list.
     */
    public static function increaseInList($list, $stringToIncrease)
    {
        if ((!empty($list)) && (!empty($stringToIncrease))) {
            if (CommonFunctions::inList($list, $stringToIncrease)) {
                return $list;
            } else {
                return $list . ' ' . $stringToIncrease;
            }
        } elseif (empty($list)) {
            return $stringToIncrease;
        } else {
            return $list;
        }
    }

    /**
     * Verify if the list contains the item.
     * @param string $list The list.
     * @param string $stringToSearch The value of item.
     * @return bool True if the list contains the item or false is not contains.
     */
    public static function inList($list, $stringToSearch)
    {
        if ((!empty($list)) && (!empty($stringToSearch))) {
            $elements = preg_split('/[ \n\t\r]+/', $list);
            foreach ($elements as $element) {
                if ($element === $stringToSearch) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Check that the element can be manipulated by HaTeMiLe.
     * @param \hatemile\util\html\HTMLDOMElement $element The element
     * @return bool True if element can be manipulated or false if element
     * cannot be manipulated.
     */
    public static function isValidElement(HTMLDOMElement $element)
    {
        if ($element->hasAttribute(CommonFunctions::DATA_IGNORE)) {
            return false;
        } else {
            $parentElement = $element->getParentElement();
            if ($parentElement !== null) {
                $tagName = $parentElement->getTagName();
                if (($tagName !== 'BODY') && ($tagName !== 'HTML')) {
                    return CommonFunctions::isValidElement($parentElement);
                } else {
                    return true;
                }
            } else {
                return true;
            }
        }
    }
}
