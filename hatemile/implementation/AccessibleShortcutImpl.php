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

require_once __DIR__ . '/../util/HTMLDOMParser.php';
require_once __DIR__ . '/../util/Configure.php';
require_once __DIR__ . '/../util/HTMLDOMElement.php';
require_once __DIR__ . '/../AccessibleShortcut.php';

use hatemile\util\HTMLDOMParser;
use hatemile\util\Configure;
use hatemile\util\HTMLDOMElement;
use hatemile\AccessibleShortcut;

class AccessibleShortcutImpl implements AccessibleShortcut {
	protected $parser;
	protected $idContainerShortcuts;
	protected $idSkipLinkContainerShortcuts;
	protected $idSkipContainerShortcuts;
	protected $textSkipLinkContainerShortcuts;
	protected $dataAccessKey;
	protected $dataIgnore;
	protected $prefix;
	protected $list;
	
	public function __construct(HTMLDOMParser $parser, Configure $configure, $userAgent) {
		$this->parser = $parser;
		$this->idContainerShortcuts = $configure->getParameter('id-container-shortcuts');
		$this->idSkipLinkContainerShortcuts = $configure->getParameter('id-skip-link-container-shortcuts');
		$this->idSkipContainerShortcuts = $configure->getParameter('id-skip-container-shortcuts');
		$this->dataAccessKey = $configure->getParameter('data-accesskey');
		$this->textSkipLinkContainerShortcuts = $configure->getParameter('text-skip-container-shortcuts');
		$this->dataIgnore = $configure->getParameter('data-ignore');
		if ($userAgent != null) {
			$userAgent = strtolower($userAgent);
			$mac = strpos($userAgent, 'mac') !== false;
			$konqueror = strpos($userAgent, 'konqueror') !== false;
			$spoofer = strpos($userAgent, 'spoofer') !== false;
			$safari = strpos($userAgent, 'applewebkit') !== false;
			$windows = strpos($userAgent, 'windows') !== false;
			if (strpos($userAgent, 'opera') !== false) {
				$this->prefix = 'SHIFT + ESC';
			} else if ((strpos($userAgent, 'chrome') !== false) && !$spoofer && $mac) {
				$this->prefix = 'CTRL + OPTION';
			} else if ($safari && !$windows && !$spoofer) {
				$this->prefix = 'CTRL + ALT';
			} else if (!$windows && ($safari || $mac || $konqueror)) {
				$this->prefix = 'CTRL';
			} else if (preg_match($userAgent, "/firefox\/[2-9]|minefield\/3/")) {
				$this->prefix = 'ALT + SHIFT';
			} else {
				$this->prefix = 'ALT';
			}
		} else {
			$this->prefix = 'ALT';
		}
	}
	
	protected function getDescription(HTMLDOMElement $element) {
		$description = '';
		if ($element->hasAttribute('title')) {
			$description = $element->getAttribute('title');
		} else if ($element->hasAttribute('aria-labelledby')) {
			$labelsIds = preg_split("/[ \n\t\r]+/", $element->getAttribute('aria-labelledby'));
			foreach ($labelsIds as $labelId) {
				$label = $this->parser->find('#' . $labelId)->firstResult();
				if ($label != null) {
					$description = $label->getTextContent();
					break;
				}
			}
		} else if ($element->hasAttribute('aria-label')) {
			$description = $element->getAttribute('aria-label');
		} else if ($element->hasAttribute('alt')) {
			$description = $element->getAttribute('alt');
		} else if ($element->getTagName() == 'INPUT') {
			$type = \trim(strtolower($element->getAttribute('type')));
			if ((($type == 'button') || ($type == 'submit') || ($type == 'reset')) && ($element->hasAttribute('value'))) {
				$description = $element->getAttribute('value');
			}
		} else {
			$description = $element->getTextContent();
		}
		$description = \trim(\preg_replace("/[ \n\r\t]+/", ' ', $description));
		return $description;
	}
	
	protected function generateList() {
		$container = $this->parser->find('#' . $this->idContainerShortcuts)->firstResult();
		if ($container == null) {
			$container = $this->parser->createElement('div');
			$container->setAttribute('id', $this->idContainerShortcuts);
			$firstChild = $this->parser->find('body')->firstResult()->getFirstElementChild();
			$firstChild->insertBefore($container);
			
			$anchorJump = $this->parser->createElement('a');
			$anchorJump->setAttribute('id', $this->idSkipLinkContainerShortcuts);
			$anchorJump->setAttribute('href', '#' . $this->idSkipContainerShortcuts);
			$anchorJump->appendText($this->textSkipLinkContainerShortcuts);
			$container->insertBefore($anchorJump);
			
			$anchor = $this->parser->createElement('a');
			$anchor->setAttribute('name', $this->idSkipContainerShortcuts);
			$anchor->setAttribute('id', $this->idSkipContainerShortcuts);
			$firstChild->insertBefore($anchor);
		}
		$htmlList = $this->parser->find($container)->findChildren('ul')->firstResult();
		if ($htmlList == null) {
			$htmlList = $this->parser->createElement('ul');
			$container->appendElement($htmlList);
		}
		return $htmlList;
	}
	
	public function getPrefix() {
		return $this->prefix;
	}
	
	public function fixShortcut(HTMLDOMElement $element) {
		if ($element->hasAttribute('accesskey')) {
			$description = $this->getDescription($element);
			if (!$element->hasAttribute('title')) {
				$element->setAttribute('title', $description);
			}
			$keys = preg_split("/[ \n\t\r]+/", $element->getAttribute('accesskey'));
			if ($this->list == null) {
				$this->list = $this->generateList();
			}
			foreach ($keys as $key) {
				$key = strtoupper($key);
				if ($this->parser->find($this->list)->findChildren('[' . $this->dataAccessKey . '=' . $key . ']')->firstResult() == null) {
					$item = $this->parser->createElement('li');
					$item->setAttribute($this->dataAccessKey, $key);
					$item->appendText($this->prefix . ' + ' . $key . ': ' . $description);
					$this->list->appendElement($item);
				}
			}
		}
	}

	public function fixShortcuts() {
		$elements = $this->parser->find('[accesskey]')->listResults();
		foreach ($elements as $element) {
			if (!$element->hasAttribute($this->dataIgnore)) {
				$this->fixShortcut($element);
			}
		}
	}
}