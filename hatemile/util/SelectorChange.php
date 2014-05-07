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

class SelectorChange {
	protected $selector;
	protected $attribute;
	protected $valueForAttribute;
	
	public function __construct($selector, $attribute, $valueForAttribute) {
		$this->selector = $selector;
		$this->attribute = $attribute;
		$this->valueForAttribute = $valueForAttribute;
	}
	
	public function getSelector() {
		return $this->selector;
	}

	public function setSelector($selector) {
		$this->selector = $selector;
	}

	public function getAttribute() {
		return $this->attribute;
	}

	public function setAttribute($attribute) {
		$this->attribute = $attribute;
	}

	public function getValueForAttribute() {
		return $this->valueForAttribute;
	}

	public function setValueForAttribute($valueForAttribute) {
		$this->valueForAttribute = $valueForAttribute;
	}
}