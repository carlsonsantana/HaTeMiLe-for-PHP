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

namespace hatemile\util\html;

/**
 * The HTMLDOMParser interface contains the methods for access a native parser.
 */
interface HTMLDOMParser {
	
	/**
	 * Find all elements in the parser by selector.
	 * @param string|\hatemile\util\html\HTMLDOMElement $selector The selector.
	 * @return \hatemile\util\html\HTMLDOMParser The parser with the elements found.
	 */
	public function find($selector);
	
	/**
	 * Find all elements in the parser by selector, children of found elements.
	 * @param string|\hatemile\util\html\HTMLDOMElement $selector The selector.
	 * @return \hatemile\util\html\HTMLDOMParser The parser with the elements found.
	 */
	public function findChildren($selector);
	
	/**
	 * Find all elements in the parser by selector, descendants of found
	 * elements.
	 * @param string|\hatemile\util\html\HTMLDOMElement $selector The selector.
	 * @return \hatemile\util\html\HTMLDOMParser The parser with the elements found.
	 */
	public function findDescendants($selector);
	
	/**
	 * Find all elements in the parser by selector, ancestors of found elements.
	 * @param string|\hatemile\util\html\HTMLDOMElement $selector The selector.
	 * @return \hatemile\util\html\HTMLDOMParser The parser with the elements found.
	 */
	public function findAncestors($selector);
	
	/**
	 * Returns the first element found.
	 * @return \hatemile\util\html\HTMLDOMElement The first element found or null if
	 * not have elements found.
	 */
	public function firstResult();
	
	/**
	 * Returns the last element found.
	 * @return \hatemile\util\html\HTMLDOMElement The last element found or null if
	 * not have elements found.
	 */
	public function lastResult();
	
	/**
	 * Returns a list with all elements found.
	 * @return \hatemile\util\html\HTMLDOMElement[] The list with all elements found.
	 */
	public function listResults();
	
	/**
	 * Create a element.
	 * @param string $tag The tag of element.
	 * @return \hatemile\util\html\HTMLDOMElement The element created.
	 */
	public function createElement($tag);
	
	/**
	 * Returns the HTML code of parser.
	 * @return string The HTML code of parser.
	 */
	public function getHTML();
	
	/**
	 * Returns the parser.
	 * @return object The parser or root element of the parser.
	 */
	public function getParser();
	
	/**
	 * Clear the memory of this object.
	 */
	public function clearParser();
}