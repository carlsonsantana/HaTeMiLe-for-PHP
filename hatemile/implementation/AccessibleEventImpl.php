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

namespace hatemile\implementation;

require_once __DIR__ . '/../util/HTMLDOMElement.php';
require_once __DIR__ . '/../util/HTMLDOMParser.php';
require_once __DIR__ . '/../util/Configure.php';
require_once __DIR__ . '/../AccessibleEvent.php';
require_once __DIR__ . '/../util/CommonFunctions.php';

use hatemile\util\HTMLDOMElement;
use hatemile\util\HTMLDOMParser;
use hatemile\util\Configure;
use hatemile\AccessibleEvent;
use hatemile\util\CommonFunctions;

class AccessibleEventImpl implements AccessibleEvent {
	protected $parser;
	protected $idScriptEvent;
	protected $prefixId;
	protected $idListIdsScriptOnClick;
	protected $idFunctionScriptFixOnClick;
	protected $dataFocused;
	protected $dataPressed;
	protected $dataIgnore;
	protected $mainScriptAdded;
	protected $otherScriptsAdded;
	protected $scriptList;

	public function __construct(HTMLDOMParser $parser, Configure $configure) {
		$this->parser = $parser;
		$this->prefixId = $configure->getParameter('prefix-generated-ids');
		$this->idScriptEvent = $configure->getParameter('id-script-event');
		$this->idListIdsScriptOnClick = $configure->getParameter('id-list-ids-script-onclick');
		$this->idFunctionScriptFixOnClick = $configure->getParameter('id-function-script-fix-onclick');
		$this->dataFocused = $configure->getParameter('data-focused');
		$this->dataPressed = $configure->getParameter('data-pressed');
		$this->dataIgnore = $configure->getParameter('data-ignore');
		$this->mainScriptAdded = false;
		$this->otherScriptsAdded = false;
	}
	
	protected function generateMainScript() {
		if ($this->parser->find('#' . $this->idScriptEvent)->firstResult() == null) {
			$script = $this->parser->createElement('script');
			$script->setAttribute('id', $this->idScriptEvent);
			$script->setAttribute('type', 'text/javascript');

			$javascript = "\nfunction onFocusEvent(element) {\n"
					. "	element.setAttribute('" . $this->dataFocused . "', 'true');\n"
					. "	if (element.onmouseover != undefined) {\n"
					. "		element.onmouseover();\n"
					. "	}\n"
					. "}\n"
					. "function onBlurEvent(element) {\n"
					. "	if (element.hasAttribute('" . $this->dataFocused . "')) {\n"
					. "		if ((element.getAttribute('" . $this->dataFocused . "').toLowerCase() == 'true') && (element.onmouseout != undefined)) {\n"
					. "			element.onmouseout();\n"
					. "		}\n"
					. "		element.setAttribute('" . $this->dataFocused . "', 'false');\n"
					. "	}\n"
					. "}\n"
					. "function onKeyPressEvent(element, event) {\n"
					. "	element.setAttribute('" . $this->dataPressed . "', event.keyCode);\n"
					. "}\n"
					. "function onKeyPressUp(element, event) {\n"
					. "	var key = event.keyCode;\n"
					. "	var enter1 = '\\n'.charCodeAt(0);\n"
					. "	var enter2 = '\\r'.charCodeAt(0);\n"
					. "	if ((key == enter1) || (key == enter2)) {\n"
					. "		if (element.hasAttribute('" . $this->dataPressed . "')) {\n"
					. "			if (key == parseInt(element.getAttribute('" . $this->dataPressed . "'))) {\n"
					. "				if (element.onclick != undefined) {\n"
					. "					element.click();\n"
					. "				}\n"
					. "				element.removeAttribute('" . $this->dataPressed . "');\n"
					. "			}\n"
					. "		}\n"
					. "	}\n"
					. "}\n";
			$script->appendText($javascript);

			$local = $this->parser->find('head')->firstResult();
			if ($local == null) {
				$local = $this->parser->find('body')->firstResult();
			}
			$local->appendElement($script);
		}
		$this->mainScriptAdded = true;
	}
	
	protected function generateOtherScripts() {
		$this->scriptList = $this->parser->find('#' . $this->idListIdsScriptOnClick)->firstResult();
		if ($this->scriptList == null) {
			$this->scriptList = $this->parser->createElement('script');
			$this->scriptList->setAttribute('id', $this->idListIdsScriptOnClick);
			$this->scriptList->setAttribute('type', 'text/javascript');
			$this->scriptList->appendText("\nidsElementsWithOnClick = [];\n");
			$this->parser->find('body')->firstResult()->appendElement($this->scriptList);
		}
		if ($this->parser->find('#' . $this->idFunctionScriptFixOnClick)->firstResult() == null) {
			$scriptFunction = $this->parser->createElement('script');
			$scriptFunction->setAttribute('id', $this->idFunctionScriptFixOnClick);
			$scriptFunction->setAttribute('type', 'text/javascript');

			$javascript = "\n for (var i = 0, length = idsElementsWithOnClick.length; i < length; i++) {\n"
					. "	var element = document.getElementById(idsElementsWithOnClick[i]);\n"
					. "	element.onkeypress = function(event) {\n"
					. "		onKeyPressEvent(element, event);\n"
					. "	};\n"
					. "	element.onkeyup = function(event) {\n"
					. "		onKeyPressUp(element, event);\n"
					. "	};\n"
					. "}\n";
			$scriptFunction->appendText($javascript);
			$this->parser->find('body')->firstResult()->appendElement($scriptFunction);
		}
		$this->otherScriptsAdded = true;
	}
	
	protected function addElementIdWithOnClick($id) {
		$this->scriptList->appendText("idsElementsWithOnClick.push('" . $id . "');\n");
	}
	
	public function fixOnHover(HTMLDOMElement $element) {
		if (!$this->mainScriptAdded) {
			$this->generateMainScript();
		}
		$tag = $element->getTagName();
		if (!(($tag == 'INPUT') || ($tag == 'BUTTON') || ($tag == 'A') || ($tag == 'SELECT') || ($tag == 'TEXTAREA') || ($element->hasAttribute('tabindex')))) {
			$element->setAttribute('tabindex', '0');
		}
		if (!$element->hasAttribute('onfocus')) {
			$element->setAttribute('onfocus', 'onFocusEvent(this);');
		}
		if (!$element->hasAttribute('onblur')) {
			$element->setAttribute('onblur', 'onBlurEvent(this);');
		}
	}

	public function fixOnHovers() {
		$elements = $this->parser->find('[onmouseover],[onmouseout]')->listResults();
		foreach ($elements as $element) {
			if (!$element->hasAttribute($this->dataIgnore)) {
				$this->fixOnHover($element);
			}
		}
	}

	public function fixOnClick(HTMLDOMElement $element) {
		$tag = $element->getTagName();
		if (!(($tag == 'INPUT') || ($tag == 'BUTTON') || ($tag == 'A'))) {
			if (!$this->mainScriptAdded) {
				$this->generateMainScript();
			}
			if (!$this->otherScriptsAdded) {
				$this->generateOtherScripts();
			}
			if (!(($element->hasAttribute('tabindex')) || ($tag == 'SELECT') || ($tag == 'TEXTAREA'))) {
				$element->setAttribute('tabindex', '0');
			}
			CommonFunctions::generateId($element, $this->prefixId);
			if ((!$element->hasAttribute('onkeypress')) && (!$element->hasAttribute('onkeyup')) && (!$element->hasAttribute('onkeydown'))) {
				$this->addElementIdWithOnClick($element->getAttribute('id'));
			}
		}
	}

	public function fixOnClicks() {
		$elements = $this->parser->find('[onclick]')->listResults();
		foreach ($elements as $element) {
			if (!$element->hasAttribute($this->dataIgnore)) {
				$this->fixOnClick($element);
			}
		}
	}
}