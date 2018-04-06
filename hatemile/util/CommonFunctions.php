<?php
/*
Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

    http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
 */

namespace hatemile\util;

require_once dirname(__FILE__) . '/html/HTMLDOMElement.php';

use \hatemile\util\html\HTMLDOMElement;

/**
 * The CommonFuncionts class contains the used methods by HaTeMiLe classes.
 */
class CommonFunctions
{

    /**
     * The name of attribute for not modify the elements.
     * @var string
     */
    const DATA_IGNORE = 'data-ignoreaccessibilityfix';

    /**
     * Count the number of ids created.
     * @var integer
     */
    protected static $count = 0;

    /**
     * The private constructor prevents that the class not can be initialized.
     */
    private function __construct()
    {
    }

    /**
     * Generate a id for a element.
     * @param \hatemile\util\html\HTMLDOMElement $element The element.
     * @param string $prefix The prefix of id.
     */
    public static function generateId(HTMLDOMElement $element, $prefix)
    {
        if (!$element->hasAttribute('id')) {
            $element->setAttribute('id', $prefix . CommonFunctions::$count);
            CommonFunctions::$count++;
        }
    }

    /**
     * Reset the count number of ids.
     */
    public static function resetCount()
    {
        CommonFunctions::$count = 0;
    }

    /**
     * Copy a list of attributes of a element for other element.
     * @param \hatemile\util\html\HTMLDOMElement $element1 The element that have
     * attributes copied.
     * @param \hatemile\util\html\HTMLDOMElement $element2 The element that copy
     * the attributes.
     * @param string[] $attributes The list of attributes that will be copied.
     */
    public static function setListAttributes(HTMLDOMElement $element1, HTMLDOMElement $element2
            , $attributes)
    {
        foreach ($attributes as $attribute) {
            if ($element1->hasAttribute($attribute)) {
                $element2->setAttribute($attribute, $element1->getAttribute($attribute));
            }
        }
    }

    /**
     * Increase a item in a list.
     * @param string $list The list.
     * @param string $stringToIncrease The value of item.
     * @return string True if the list contains the item or false is not contains.
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
     * @return boolean True if the list contains the item or false is not contains.
     */
    public static function inList($list, $stringToSearch)
    {
        if ((!empty($list)) && (!empty($stringToSearch))) {
            $elements = preg_split("/[ \n\t\r]+/", $list);
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
     * @return boolean True if element can be manipulated or false if element cannot be
     * manipulated.
     */
    public static function isValidElement(HTMLDOMElement $element)
    {
        if ($element->hasAttribute(CommonFunctions::DATA_IGNORE)) {
            return false;
        } else {
            $parentElement = $element->getParentElement();
            if ($parentElement != null) {
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
