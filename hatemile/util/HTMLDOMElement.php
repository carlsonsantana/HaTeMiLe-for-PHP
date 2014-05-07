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

namespace hatemile\util;

interface HTMLDOMElement {
	public function getTagName();

	public function getAttribute($name);

	public function setAttribute($name, $value);

	public function removeAttribute($name);

	public function hasAttribute($name);

	public function hasAttributes();

	public function getTextContent();

	public function insertBefore(HTMLDOMElement $newElement);

	public function insertAfter(HTMLDOMElement $newElement);

	public function removeElement();

	public function replaceElement(HTMLDOMElement $newElement);

	public function appendElement(HTMLDOMElement $element);

	public function getChildren();

	public function appendText($text);

	public function hasChildren();

	public function getParentElement();

	public function getInnerHTML();

	public function setInnerHTML($html);

	public function getOuterHTML();

	public function getData();

	public function setData($data);

	public function cloneElement();

	public function getFirstElementChild();

	public function getLastElementChild();
}