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

require_once __DIR__ . '/HTMLDOMElement.php';

use hatemile\util\HTMLDOMElement;

class CommonFunctions {
	protected static $count = 0;

	public static function generateId(HTMLDOMElement $element, $prefix) {
		if (!$element->hasAttribute('id')) {
			$element->setAttribute('id', $prefix . CommonFunctions::$count);
			CommonFunctions::$count++;
		}
	}
	
	public static function setListAttributes(HTMLDOMElement $element1, HTMLDOMElement $element2, $attributes) {
		foreach ($attributes as $attribute) {
			if ($element1->hasAttribute($attribute)) {
				$element2->setAttribute($attribute, $element1->getAttribute($attribute));
			}
		}
	}
	
	public static function increaseInList($list, $stringToIncrease) {
		if ((!empty($list)) && (!empty($stringToIncrease))) {
			$elements = preg_split("/[ \n\t\r]+/", $list);
			foreach ($elements as $element) {
				if ($element == $stringToIncrease) {
					return $list;
				}
			}
			return $list . ' ' . $stringToIncrease;
		} else if (empty($list)) {
			return $stringToIncrease;
		} else {
			return $list;
		}
	}
}