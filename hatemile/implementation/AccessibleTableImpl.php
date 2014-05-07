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
require_once __DIR__ . '/../util/CommonFunctions.php';
require_once __DIR__ . '/../AccessibleTable.php';

use hatemile\util\HTMLDOMElement;
use hatemile\util\HTMLDOMParser;
use hatemile\util\Configure;
use hatemile\util\CommonFunctions;
use hatemile\AccessibleTable;

class AccessibleTableImpl implements AccessibleTable {
	protected $parser;
	protected $prefixId;
	protected $dataIgnore;

	public function __construct(HTMLDOMParser $parser, Configure $configure) {
		$this->parser = $parser;
		$this->prefixId = $configure->getParameter('prefix-generated-ids');
		$this->dataIgnore = $configure->getParameter('data-ignore');
	}

	protected function generatePart(HTMLDOMElement $part) {
		$rows = $this->parser->find($part)->findChildren('tr')->listResults();
		$table = array();
		foreach ($rows as $row) {
			array_push($table, $this->generateColspan($this->parser->find($row)->findChildren('td,th')->listResults()));
		}
		return $this->generateRowspan($table);
	}

	protected function generateRowspan($rows) {
		$copy = array_merge($rows);
		$table = array();
		if (!empty($rows)) {
			for ($i = 0, $lengthRows = sizeof($rows); $i < $lengthRows; $i++) {
				$columnIndex = 0;
				$cells = array_merge($copy[$i]);
				if (empty($table[$i])) {
					$table[$i] = array();
				}
				for ($j = 0, $lengthCells = sizeof($cells); $j < $lengthCells; $j++) {
					$cell = $cells[$j];
					$m = $j + $columnIndex;
					$row = $table[$i];
					while (!empty($row[$m])) {
						$columnIndex++;
						$m = $j + $columnIndex;
					}
					$row[$m] = $cell;
					if ($cell->hasAttribute('rowspan')) {
						$rowspan = intval($cell->getAttribute('rowspan'));
						for ($k = 1; $k < $rowspan; $k++) {
							$n = $i + $k;
							if (empty($table[$n])) {
								$table[$n] = array();
							}
							$table[$n][$m] = $cell;
						}
					}
					$table[$i] = $row;
				}
			}
		}
		return $table;
	}

	protected function generateColspan($row) {
		$copy = array_merge($row);
		$cells = array_merge($row);
		for ($i = 0, $size = sizeof($row); $i < $size; $i++) {
			$cell = $cells[$i];
			if ($cell->hasAttribute('colspan')) {
				$colspan = intval($cell->getAttribute('colspan'));
				for ($j = 1; $j < $colspan; $j++) {
					array_splice($copy, $i + $j, 0, array($cell));
				}
			}
		}
		return $copy;
	}

	protected function validateHeader($header) {
		if (empty($header)) {
			return false;
		}
		$length = -1;
		foreach ($header as $elements) {
			if (empty($elements)) {
				return false;
			} else if ($length == -1) {
				$length = sizeof($elements);
			} else if (sizeof($elements) != $length) {
				return false;
			}
		}
		return true;
	}

	protected function returnListIdsColumns($header, $index) {
		$ids = array();
		foreach ($header as $row) {
			if ($row[$index]->getTagName() == 'TH') {
				array_push($ids, $row[$index]->getAttribute('id'));
			}
		}
		return $ids;
	}
	
	protected function fixBodyOrFooter(HTMLDOMElement $element) {
		$table = $this->generatePart($element);
		foreach ($table as $cells) {
			$headersIds = array();
			foreach ($cells as $cell) {
				if ($cell->getTagName() == 'TH') {
					CommonFunctions::generateId($cell, $this->prefixId);
					$cell->setAttribute('scope', 'row');
					array_push($headersIds, $cell->getAttribute('id'));
				}
			}
			if (!empty($headersIds)) {
				foreach ($cells as $cell) {
					if ($cell->getTagName() == 'TD') {
						$headers = null;
						if ($cell->hasAttribute('headers')) {
							$headers = $cell->getAttribute('headers');
						}
						foreach ($headersIds as $headerId) {
							$headers = CommonFunctions::increaseInList($headers, $headerId);
						}
						$cell->setAttribute('headers', $headers);
					}
				}
			}
		}
	}

	public function fixHeader(HTMLDOMElement $element) {
		if ($element->getTagName() == 'THEAD') {
			$cells = $this->parser->find($element)->findChildren('tr')->findChildren('th')->listResults();
			foreach ($cells as $cell) {
				CommonFunctions::generateId($cell, $this->prefixId);
				$cell->setAttribute('scope', 'col');
			}
		}
	}

	public function fixFooter(HTMLDOMElement $element) {
		if ($element->getTagName() == 'TFOOT') {
			$this->fixBodyOrFooter($element);
		}
	}

	public function fixBody(HTMLDOMElement $element) {
		if ($element->getTagName() == 'TBODY') {
			$this->fixBodyOrFooter($element);
		}
	}

	public function fixTable(HTMLDOMElement $element) {
		$header = $this->parser->find($element)->findChildren('thead')->firstResult();
		$body = $this->parser->find($element)->findChildren('tbody')->firstResult();
		$footer = $this->parser->find($element)->findChildren('tfoot')->firstResult();
		if (!empty($header)) {
			$this->fixHeader($header);

			$headerCells = $this->generatePart($header);
			if (($this->validateHeader($headerCells)) && (!empty($body))) {
				$lengthHeader = sizeof($headerCells[0]);
				$table = $this->generatePart($body);
				if (!empty($footer)) {
					$table = array_merge($table, $this->generatePart($footer));
				}
				foreach ($table as $cells) {
					$i = 0;
					if (sizeof($cells) == $lengthHeader) {
						foreach ($cells as $cell) {
							$ids = $this->returnListIdsColumns($headerCells, $i);
							$headers = null;
							if ($cell->hasAttribute('headers')) {
								$headers = $cell->getAttribute('headers');
							}
							foreach ($ids as $id) {
								$headers = CommonFunctions::increaseInList($headers, $id);
							}
							$cell->setAttribute('headers', $headers);
							$i++;
						}
					}
				}
			}
		}
		if (!empty($body)) {
			$this->fixBody($body);
		}
		if (!empty($footer)) {
			$this->fixFooter($footer);
		}
	}

	public function fixTables() {
		$elements = $this->parser->find('table')->listResults();
		foreach ($elements as $element) {
			if (!$element->hasAttribute($this->dataIgnore)) {
				$this->fixTable($element);
			}
		}
	}
}