<?php
/**
 * Interface HTMLDOMElement.
 * 
 * @package hatemile\util\html
 * @author Carlson Santana Cruz
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @copyright (c) 2018, HaTeMiLe
 */

namespace hatemile\util\html;

require_once join(DIRECTORY_SEPARATOR, array(
    dirname(__FILE__),
    'HTMLDOMNode.php'
));

use \hatemile\util\html\HTMLDOMNode;

/**
 * The HTMLDOMElement interface contains the methods for access of the HTML
 * element.
 */
interface HTMLDOMElement extends HTMLDOMNode
{

    /**
     * Returns the tag name of element.
     * @return string The tag name of element in uppercase letters.
     */
    public function getTagName();

    /**
     * Returns the value of a attribute.
     * @param string $name The name of attribute.
     * @return string The value of the attribute or null if the element not
     * contains the attribute.
     */
    public function getAttribute($name);

    /**
     * Create or modify a attribute.
     * @param string $name The name of attribute.
     * @param string $value The value of attribute.
     */
    public function setAttribute($name, $value);

    /**
     * Remove a attribute of element.
     * @param string $name The name of attribute.
     */
    public function removeAttribute($name);

    /**
     * Check that the element has an attribute.
     * @param string $name The name of attribute.
     * @return bool True if the element has the attribute or false if the
     * element not has the attribute.
     */
    public function hasAttribute($name);

    /**
     * Check that the element has attributes.
     * @return bool True if the element has attributes or false if the element
     * not has attributes.
     */
    public function hasAttributes();

    /**
     * Append a element child.
     * @param \hatemile\util\html\HTMLDOMElement $element The element that be
     * inserted.
     * @return \hatemile\util\html\HTMLDOMElement This element.
     */
    public function appendElement(HTMLDOMElement $element);

    /**
     * Prepend a element child.
     * @param \hatemile\util\html\HTMLDOMElement $element The element that be
     * inserted.
     * @return \hatemile\util\html\HTMLDOMElement This element.
     */
    public function prependElement(HTMLDOMElement $element);

    /**
     * Returns the elements children of this element.
     * @return \hatemile\util\html\HTMLDOMElement[] The elements children of
     * this element.
     */
    public function getChildrenElements();

    /**
     * Returns the children of this element.
     * @return \hatemile\util\html\HTMLDOMNode[] The children of this element.
     */
    public function getChildren();

    /**
     * Joins adjacent Text nodes.
     * @return \hatemile\util\html\HTMLDOMElement This element.
     */
    public function normalize();

    /**
     * Check that the element has elements children.
     * @return bool True if the element has elements children or false if the
     * element not has elements children.
     */
    public function hasChildrenElements();

    /**
     * Check that the element has children.
     * @return bool True if the element has children or false if the element not
     * has children.
     */
    public function hasChildren();

    /**
     * Returns the inner HTML code of this element.
     * @return string The inner HTML code of this element.
     */
    public function getInnerHTML();

    /**
     * Returns the HTML code of this element.
     * @return string The HTML code of this element.
     */
    public function getOuterHTML();

    /**
     * Clone this element.
     * @return \hatemile\util\html\HTMLDOMElement The clone.
     */
    public function cloneElement();

    /**
     * Returns the first element child of this element.
     * @return \hatemile\util\html\HTMLDOMElement The first element child of
     * this element.
     */
    public function getFirstElementChild();

    /**
     * Returns the last element child of this element.
     * @return \hatemile\util\html\HTMLDOMElement The last element child of this
     * element.
     */
    public function getLastElementChild();

    /**
     * Returns the first node child of this element.
     * @return \hatemile\util\html\HTMLDOMNode The first node child of this
     * element.
     */
    public function getFirstNodeChild();

    /**
     * Returns the last node child of this element.
     * @return \hatemile\util\html\HTMLDOMNode The last node child of this
     * element.
     */
    public function getLastNodeChild();
}
