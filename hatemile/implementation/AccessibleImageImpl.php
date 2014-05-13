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

require_once __DIR__ . '/../AccessibleImage.php';
require_once __DIR__ . '/../util/HTMLDOMElement.php';
require_once __DIR__ . '/../util/HTMLDOMParser.php';
require_once __DIR__ . '/../util/Configure.php';
require_once __DIR__ . '/../util/CommonFunctions.php';

use hatemile\AccessibleImage;
use hatemile\util\HTMLDOMElement;
use hatemile\util\HTMLDOMParser;
use hatemile\util\Configure;
use hatemile\util\CommonFunctions;

class AccessibleImageImpl implements AccessibleImage {
	protected $parser;
	protected $prefixId;
	protected $classListImageAreas;
	protected $classLongDescriptionLink;
	protected $sufixLongDescriptionLink;
	protected $dataListForImage;
	protected $dataLongDescriptionForImage;
	protected $dataIgnore;
	
	public function __construct(HTMLDOMParser $parser, Configure $configure) {
		$this->parser = $parser;
		$this->prefixId = $configure->getParameter('prefix-generated-ids');
		$this->classListImageAreas = $configure->getParameter('class-list-image-areas');
		$this->classLongDescriptionLink = $configure->getParameter('class-longdescription-link');
		$this->sufixLongDescriptionLink = $configure->getParameter('sufix-longdescription-link');
		$this->dataListForImage = $configure->getParameter('data-list-for-image');
		$this->dataLongDescriptionForImage = $configure->getParameter('data-longdescription-for-image');
		$this->dataIgnore = $configure->getParameter('data-ignore');
	}
	
	public function fixMap(HTMLDOMElement $element) {
		if ($element->getTagName() == 'MAP') {
			$name = null;
			if ($element->hasAttribute('name')) {
				$name = $element->getAttribute('name');
			} else if ($element->hasAttribute('id')) {
				$name = $element->getAttribute('id');
			}
			if (!empty($name)) {
				$list = $this->parser->createElement('ul');
				$list->setAttribute('class', $this->classListImageAreas);
				$areas = $this->parser->find($element)->findChildren('area, a')->listResults();
				foreach ($areas as $area) {
					if ($area->hasAttribute('alt')) {
						$item = $this->parser->createElement('li');
						$anchor = $this->parser->createElement('a');
						$anchor->appendText($area->getAttribute('alt'));
						CommonFunctions::setListAttributes($area, $anchor, array('href',
								'target', 'download', 'hreflang', 'media',
								'rel', 'type', 'title'));
						$item->appendElement($anchor);
						$list->appendElement($item);
					}
				}
				if ($list->hasChildren()) {
					$images = $this->parser->find('[usemap=#' . $name . ']')->listResults();
					foreach ($images as $image) {
						CommonFunctions::generateId($image, $this->prefixId);
						if ($this->parser->find('[' . $this->dataListForImage . '=' . $image->getAttribute('id') . ']')->firstResult() == null) {
							$newList = $list->cloneElement();
							$newList->setAttribute($this->dataListForImage, $image->getAttribute('id'));
							$image->insertAfter($newList);
						}
					}
				}
			}
		}
	}

	public function fixMaps() {
		$elements = $this->parser->find('map')->listResults();
		foreach ($elements as $element) {
			if (!$element->hasAttribute($this->dataIgnore)) {
				$this->fixMap($element);
			}
		}
	}
	
	public function fixLongDescription(HTMLDOMElement $element) {
		if ($element->hasAttribute('longdesc')) {
			CommonFunctions::generateId($element, $this->prefixId);
			if ($this->parser->find('[' . $this->dataLongDescriptionForImage . '=' . $element->getAttribute('id') . ']')->firstResult() == null) {
				$text = null;
				if ($element->hasAttribute('alt')) {
					$text = $element->getAttribute('alt') . ' ' . $this->sufixLongDescriptionLink;
				} else {
					$text = $this->sufixLongDescriptionLink;
				}
				$longDescription = $element->getAttribute('longdesc');
				$anchor = $this->parser->createElement('a');
				$anchor->setAttribute('href', $longDescription);
				$anchor->setAttribute('target', '_blank');
				$anchor->setAttribute($this->dataLongDescriptionForImage, $element->getAttribute('id'));
				$anchor->setAttribute('class', $this->classLongDescriptionLink);
				$anchor->appendText($text);
				$element->insertAfter($anchor);
			}
		}
	}

	public function fixLongDescriptions() {
		$elements = $this->parser->find('[longdesc]')->listResults();
		foreach ($elements as $element) {
			if (!$element->hasAttribute($this->dataIgnore)) {
				$this->fixLongDescription($element);
			}
		}
	}
}
