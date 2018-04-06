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

namespace hatemile\implementation;

require_once dirname(__FILE__) . '/../util/html/HTMLDOMElement.php';
require_once dirname(__FILE__) . '/../util/html/HTMLDOMParser.php';
require_once dirname(__FILE__) . '/../util/Configure.php';
require_once dirname(__FILE__) . '/../util/CommonFunctions.php';
require_once dirname(__FILE__) . '/../AccessibleAssociation.php';

use \hatemile\util\html\HTMLDOMElement;
use \hatemile\util\html\HTMLDOMParser;
use \hatemile\util\Configure;
use \hatemile\util\CommonFunctions;
use \hatemile\AccessibleAssociation;

/**
 * The AccessibleAssociationImplementation class is official implementation of
 * AccessibleAssociation.
 */
class AccessibleAssociationImplementation implements AccessibleAssociation {
    
    /**
     * The HTML parser.
     * @var \hatemile\util\html\HTMLDOMParser
     */
    protected $parser;
    
    /**
     * The prefix of generated id.
     * @var string
     */
    protected $prefixId;
    
    /**
     * Initializes a new object that manipulate the accessibility of the tables
     * of parser.
     * @param \hatemile\util\html\HTMLDOMParser $parser The HTML parser.
     * @param \hatemile\util\Configure $configure The configuration of HaTeMiLe.
     */
    public function __construct(HTMLDOMParser $parser, Configure $configure) {
        $this->parser = $parser;
        $this->prefixId = $configure->getParameter('prefix-generated-ids');
    }
    
    /**
     * Returns a list that represents the table.
     * @param \hatemile\util\html\HTMLDOMElement $part The table header, table
     * footer or table body.
     * @return \hatemile\util\html\HTMLDOMElement[][] The list that represents
     * the table.
     */
    protected function generatePart(HTMLDOMElement $part) {
        $rows = $this->parser->find($part)->findChildren('tr')->listResults();
        $table = array();
        foreach ($rows as $row) {
            array_push($table, $this->generateColspan($this->parser->find($row)->findChildren('td,th')
                    ->listResults()));
        }
        return $this->generateRowspan($table);
    }
    
    /**
     * Returns a list that represents the table with the rowspans.
     * @param \hatemile\util\html\HTMLDOMElement[][] $rows The list that represents
     * the table without the rowspans.
     * @return \hatemile\util\html\HTMLDOMElement[][] The list that represents the
     * table with the rowspans.
     */
    protected function generateRowspan($rows) {
        $copy = array_merge($rows);
        $table = array();
        if (!empty($rows)) {
            for ($i = 0, $lengthRows = sizeof($rows); $i < $lengthRows; $i++) {
                $columnIndex = 0;
                if (sizeof($table) <= $i) {
                    $table[$i] = array();
                }
                $cells = array_merge($copy[$i]);
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
    
    /**
     * Returns a list that represents the line of table with the colspans.
     * @param \hatemile\util\html\HTMLDOMElement[] $row The list that represents the
     * line of table without the colspans.
     * @return \hatemile\util\html\HTMLDOMElement[] The list that represents the line
     * of table with the colspans.
     */
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
    
    /**
     * Validate the list that represents the table header.
     * @param \hatemile\util\html\HTMLDOMElement[][] $header The list that
     * represents the table header.
     * @return boolean True if the table header is valid or false if the table
     * header is not valid.
     */
    protected function validateHeader($header) {
        if (empty($header)) {
            return false;
        }
        $length = -1;
        foreach ($header as $elements) {
            if (empty($elements)) {
                return false;
            } else if ($length === -1) {
                $length = sizeof($elements);
            } else if (sizeof($elements) !== $length) {
                return false;
            }
        }
        return true;
    }
    
    /**
     * Returns a list with ids of rows of same column.
     * @param \hatemile\util\html\HTMLDOMElement[][] $header The list that represents
     * the table header.
     * @param integer $index The index of columns.
     * @return string[] The list with ids of rows of same column.
     */
    protected function returnListIdsColumns($header, $index) {
        $ids = array();
        foreach ($header as $row) {
            if ($row[$index]->getTagName() === 'TH') {
                array_push($ids, $row[$index]->getAttribute('id'));
            }
        }
        return $ids;
    }
    
    /**
     * Fix the table body or table footer.
     * @param \hatemile\util\html\HTMLDOMElement $element The table body or table
     * footer.
     */
    protected function fixBodyOrFooter(HTMLDOMElement $element) {
        $table = $this->generatePart($element);
        foreach ($table as $cells) {
            $headersIds = array();
            foreach ($cells as $cell) {
                if ($cell->getTagName() === 'TH') {
                    CommonFunctions::generateId($cell, $this->prefixId);
                    array_push($headersIds, $cell->getAttribute('id'));
                    
                    $cell->setAttribute('scope', 'row');
                }
            }
            if (!empty($headersIds)) {
                foreach ($cells as $cell) {
                    if ($cell->getTagName() === 'TD') {
                        $headers = $cell->getAttribute('headers');
                        foreach ($headersIds as $headerId) {
                            $headers = CommonFunctions::increaseInList($headers, $headerId);
                        }
                        $cell->setAttribute('headers', $headers);
                    }
                }
            }
        }
    }
    
    /**
     * Fix the table header.
     * @param \hatemile\util\html\HTMLDOMElement $tableHeader The table header.
     */
    protected function fixHeader(HTMLDOMElement $tableHeader) {
        $cells = $this->parser->find($tableHeader)->findChildren('tr')->findChildren('th')
                ->listResults();
        foreach ($cells as $cell) {
            CommonFunctions::generateId($cell, $this->prefixId);
            
            $cell->setAttribute('scope', 'col');
        }
    }
    
    public function fixAssociationCellsTable(HTMLDOMElement $table) {
        $header = $this->parser->find($table)->findChildren('thead')->firstResult();
        $body = $this->parser->find($table)->findChildren('tbody')->firstResult();
        $footer = $this->parser->find($table)->findChildren('tfoot')->firstResult();
        if ($header !== null) {
            $this->fixHeader($header);
            
            $headerCells = $this->generatePart($header);
            if (($body !== null) && ($this->validateHeader($headerCells))) {
                $lengthHeader = sizeof($headerCells[0]);
                $fakeTable = $this->generatePart($body);
                if ($footer !== null) {
                    $fakeTable = array_merge($fakeTable, $this->generatePart($footer));
                }
                foreach ($fakeTable as $cells) {
                    if (sizeof($cells) === $lengthHeader) {
                        $i = 0;
                        foreach ($cells as $cell) {
                            $headersIds = $this->returnListIdsColumns($headerCells, $i);
                            $headers = $cell->getAttribute('headers');
                            foreach ($headersIds as $headersId) {
                                $headers = CommonFunctions::increaseInList($headers, $headersId);
                            }
                            $cell->setAttribute('headers', $headers);
                            $i++;
                        }
                    }
                }
            }
        }
        if ($body !== null) {
            $this->fixBodyOrFooter($body);
        }
        if ($footer !== null) {
            $this->fixBodyOrFooter($footer);
        }
    }
    
    public function fixAssociationCellsTables() {
        $tables = $this->parser->find('table')->listResults();
        foreach ($tables as $table) {
            if (CommonFunctions::isValidElement($table)) {
                $this->fixAssociationCellsTable($table);
            }
        }
    }
    
    public function fixLabel(HTMLDOMElement $label) {
        if ($label->getTagName() === 'LABEL') {
            if ($label->hasAttribute('for')) {
                $field = $this->parser->find('#' . $label->getAttribute('for'))->firstResult();
            } else {
                $field = $this->parser->find($label)
                        ->findDescendants('input,select,textarea')->firstResult();
                
                if ($field !== null) {
                    CommonFunctions::generateId($field, $this->prefixId);
                    $label->setAttribute('for', $field->getAttribute('id'));
                }
            }
            if ($field !== null) {
                if (!$field->hasAttribute('aria-label')) {
                    $field->setAttribute('aria-label'
                            , \trim(preg_replace('/[ \n\r\t]+/', ' ', $label->getTextContent())));
                }
                
                CommonFunctions::generateId($label, $this->prefixId);
                $field->setAttribute('aria-labelledby', CommonFunctions::increaseInList
                        ($field->getAttribute('aria-labelledby') , $label->getAttribute('id')));
            }
        }
    }
    
    public function fixLabels() {
        $labels = $this->parser->find('label')->listResults();
        foreach ($labels as $label) {
            if (CommonFunctions::isValidElement($label)) {
                $this->fixLabel($label);
            }
        }
    }
}