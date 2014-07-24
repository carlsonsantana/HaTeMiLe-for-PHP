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

/**
 * The SelectorChange class store the selector that be attribute change.
 * @version 2014-07-23
 */
class SelectorChange {
	
	/**
	 * The selector.
	 * @var string
	 */
	protected $selector;
	
	/**
	 * The attribute that will change.
	 * @var string
	 */
	protected $attribute;
	
	/**
	 * The value of the attribute.
	 * @var string
	 */
	protected $valueForAttribute;
	
	/**
	 * Inicializes a new object with the values pre-defineds.
	 * @param string $selector The selector.
	 * @param string $attribute The attribute.
	 * @param string $valueForAttribute The value of the attribute.
	 */
	public function __construct($selector, $attribute, $valueForAttribute) {
		$this->selector = $selector;
		$this->attribute = $attribute;
		$this->valueForAttribute = $valueForAttribute;
	}
	
	/**
	 * Returns the selector.
	 * @return string The selector.
	 */
	public function getSelector() {
		return $this->selector;
	}
	
	/**
	 * Returns the attribute.
	 * @return string The attribute.
	 */
	public function getAttribute() {
		return $this->attribute;
	}
	
	/**
	 * Returns the value of the attribute.
	 * @return string The value of the attribute.
	 */
	public function getValueForAttribute() {
		return $this->valueForAttribute;
	}
}