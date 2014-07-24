<?php
/*
Copyright 2014 Carlson Santana Cruz

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

namespace hatemile;

require_once dirname(__FILE__) . '/util/HTMLDOMElement.php';

use hatemile\util\HTMLDOMElement;

/**
 * The AccessibleShortcut interface fix the problems of accessibility associated
 * with the shortcuts.
 * @version 2014-07-23
 */
interface AccessibleShortcut {
	
	/**
	 * Returns the browser shortcut prefix.
	 * @return string The browser shortcut prefix.
	 */
	public function getPrefix();
	
	/**
	 * Fix the element with shortcuts.
	 * @param \hatemile\util\HTMLDOMElement $element The element with shortcuts. 
	 */
	public function fixShortcut(HTMLDOMElement $element);
	
	/**
	 * Fix the elements with shortcuts.
	 */
	public function fixShortcuts();
}