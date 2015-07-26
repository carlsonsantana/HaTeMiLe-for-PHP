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

/**
 * The HTMLDOMElement interface contains the methods for access of the HTML
 * element.
 */
interface HTMLDOMElement {
	
	/**
	 * Returns the tag name of element.
	 * @return string The tag name of element in uppercase letters.
	 */
	public function getTagName();
	
	/**
	 * Returns the value of a attribute.
	 * @param string $name The name of attribute.
	 * @return string The value of the attribute, if the element not contains
	 * the attribute returns null.
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
	 * Returns if the element has an attribute.
	 * @param string $name The name of attribute.
	 * @return boolean True if the element has the attribute or false if the
	 * element not has the attribute.
	 */
	public function hasAttribute($name);
	
	/**
	 * Returns if the element has attributes.
	 * @return boolean True if the element has attributes or false if the
	 * element not has attributes.
	 */
	public function hasAttributes();
	
	/**
	 * Returns the text of element.
	 * @return string The text of element.
	 */
	public function getTextContent();
	
	/**
	 * Insert a element before this element.
	 * @param \hatemile\util\HTMLDOMElement $newElement The element that be
	 * inserted.
	 * @return \hatemile\util\HTMLDOMElement The element inserted.
	 */
	public function insertBefore(HTMLDOMElement $newElement);
	
	/**
	 * Insert a element after this element.
	 * @param \hatemile\util\HTMLDOMElement $newElement The element that be
	 * inserted.
	 * @return \hatemile\util\HTMLDOMElement The element inserted.
	 */
	public function insertAfter(HTMLDOMElement $newElement);
	
	/**
	 * Remove this element of the parser.
	 * @return \hatemile\util\HTMLDOMElement The removed element.
	 */
	public function removeElement();
	
	/**
	 * Replace this element for other element.
	 * @param \hatemile\util\HTMLDOMElement $newElement The element that replace
	 * this element.
	 * @return \hatemile\util\HTMLDOMElement The element replaced.
	 */
	public function replaceElement(HTMLDOMElement $newElement);
	
	/**
	 * Append a element child.
	 * @param \hatemile\util\HTMLDOMElement $element The element that be
	 * inserted.
	 * @return \hatemile\util\HTMLDOMElement The element inserted.
	 */
	public function appendElement(HTMLDOMElement $element);
	
	/**
	 * Returns the children of this element.
	 * @return \hatemile\util\HTMLDOMElement[] The children of this element.
	 */
	public function getChildren();
	
	/**
	 * Append a text child.
	 * @param string $text The text.
	 */
	public function appendText($text);
	
	/**
	 * Returns if the element has children.
	 * @return boolean True if the element has children or false if the element
	 * not has children.
	 */
	public function hasChildren();
	
	/**
	 * Returns the parent element of this element.
	 * @return \hatemile\util\HTMLDOMElement The parent element of this element.
	 */
	public function getParentElement();
	
	/**
	 * Returns the inner HTML code of this element.
	 * @return string The inner HTML code of this element.
	 */
	public function getInnerHTML();
	
	/**
	 * Modify the inner HTML code of this element.
	 * @param string $html The HTML code.
	 */
	public function setInnerHTML($html);
	
	/**
	 * Returns the HTML code of this element.
	 * @return string The HTML code of this element.
	 */
	public function getOuterHTML();
	
	/**
	 * Returns the native object of this element.
	 * @return object The native object of this element.
	 */
	public function getData();
	
	/**
	 * Modify the native object of this element.
	 * @param object $data The native object of this element.
	 */
	public function setData($data);
	
	/**
	 * Returns the first element child of this element.
	 * @return \hatemile\util\HTMLDOMElement The first element child of this
	 * element.
	 */
	public function getFirstElementChild();
	
	/**
	 * Returns the last element child of this element.
	 * @return \hatemile\util\HTMLDOMElement The last element child of this
	 * element.
	 */
	public function getLastElementChild();
	
	/**
	 * Clone this element.
	 * @return \hatemile\util\HTMLDOMElement The clone.
	 */
	public function cloneElement();
}