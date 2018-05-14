<?php
/**
 * Class AccessibleAssociationImplementation.
 * 
 * @package hatemile\implementation
 * @author Carlson Santana Cruz
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @copyright (c) 2018, HaTeMiLe
 */

namespace hatemile\implementation;

require_once join(DIRECTORY_SEPARATOR, array(
    dirname(dirname(__FILE__)),
    'AccessibleAssociation.php'
));
require_once join(DIRECTORY_SEPARATOR, array(
    dirname(dirname(__FILE__)),
    'util',
    'CommonFunctions.php'
));
require_once join(DIRECTORY_SEPARATOR, array(
    dirname(dirname(__FILE__)),
    'util',
    'IDGenerator.php'
));
require_once join(DIRECTORY_SEPARATOR, array(
    dirname(dirname(__FILE__)),
    'util',
    'html',
    'HTMLDOMElement.php'
));
require_once join(DIRECTORY_SEPARATOR, array(
    dirname(dirname(__FILE__)),
    'util',
    'html',
    'HTMLDOMParser.php'
));

use \hatemile\AccessibleAssociation;
use \hatemile\util\CommonFunctions;
use \hatemile\util\IDGenerator;
use \hatemile\util\html\HTMLDOMElement;
use \hatemile\util\html\HTMLDOMParser;

/**
 * The AccessibleAssociationImplementation class is official implementation of
 * AccessibleAssociation.
 */
class AccessibleAssociationImplementation implements AccessibleAssociation
{

    /**
     * The HTML parser.
     * @var \hatemile\util\html\HTMLDOMParser
     */
    protected $parser;

    /**
     * The id generator.
     * @var \hatemile\util\IDGenerator
     */
    protected $idGenerator;

    /**
     * Initializes a new object that improve the accessibility of associations
     * of parser.
     * @param \hatemile\util\html\HTMLDOMParser $parser The HTML parser.
     */
    public function __construct(HTMLDOMParser $parser)
    {
        $this->parser = $parser;
        $this->idGenerator = new IDGenerator('association');
    }

    /**
     * Returns a list that represents the table.
     * @param \hatemile\util\html\HTMLDOMElement $part The table header, table
     * footer or table body.
     * @return \hatemile\util\html\HTMLDOMElement[][] The list that represents
     * the table.
     */
    protected function getModelTable(HTMLDOMElement $part)
    {
        $rows = $this->parser->find($part)->findChildren('tr')->listResults();
        $table = array();
        foreach ($rows as $row) {
            array_push($table, $this->getModelRow(
                $this->parser->find($row)->findChildren('td,th')->listResults()
            ));
        }
        return $this->getValidModelTable($table);
    }

    /**
     * Returns a list that represents the table with the rowspans.
     * @param \hatemile\util\html\HTMLDOMElement[][] $originalTable The list
     * that represents the table without the rowspans.
     * @return \hatemile\util\html\HTMLDOMElement[][] The list that represents
     * the table with the rowspans.
     */
    protected function getValidModelTable($originalTable)
    {
        $newTable = array();
        if (!empty($originalTable)) {
            $lengthTable = sizeof($originalTable);
            for ($rowIndex = 0; $rowIndex < $lengthTable; $rowIndex++) {
                $cellsAdded = 0;
                if (sizeof($newTable) <= $rowIndex) {
                    $newTable[$rowIndex] = array();
                }
                $originalRow = array_merge($originalTable[$rowIndex]);
                $lengthRow = sizeof($originalRow);
                for ($cellIndex = 0; $cellIndex < $lengthRow; $cellIndex++) {
                    $cell = $originalRow[$cellIndex];
                    $newCellIndex = $cellIndex + $cellsAdded;
                    $newRow = $newTable[$rowIndex];
                    while (!empty($newRow[$newCellIndex])) {
                        $cellsAdded++;
                        $newCellIndex = $cellIndex + $cellsAdded;
                    }
                    $newRow[$newCellIndex] = $cell;
                    if ($cell->hasAttribute('rowspan')) {
                        $rowspan = intval($cell->getAttribute('rowspan'));
                        for (
                            $newRowIndex = $rowIndex + 1;
                            $rowspan > 1;
                            $rowspan--,
                            $newRowIndex++
                        ) {
                            if (empty($newTable[$newRowIndex])) {
                                $newTable[$newRowIndex] = array();
                            }
                            $newTable[$newRowIndex][$newCellIndex] = $cell;
                        }
                    }
                    $newTable[$rowIndex] = $newRow;
                }
            }
        }
        return $newTable;
    }

    /**
     * Returns a list that represents the line of table with the colspans.
     * @param \hatemile\util\html\HTMLDOMElement[] $originalRow The list that
     * represents the line of table without the colspans.
     * @return \hatemile\util\html\HTMLDOMElement[] The list that represents the
     * line of table with the colspans.
     */
    protected function getModelRow($originalRow)
    {
        $newRow = array_merge($originalRow);
        for ($i = 0, $size = sizeof($originalRow); $i < $size; $i++) {
            $cell = $originalRow[$i];
            if ($cell->hasAttribute('colspan')) {
                $colspan = intval($cell->getAttribute('colspan'));
                for ($j = 1; $j < $colspan; $j++) {
                    array_splice($newRow, $i + $j, 0, array($cell));
                }
            }
        }
        return $newRow;
    }

    /**
     * Validate the list that represents the table header.
     * @param \hatemile\util\html\HTMLDOMElement[][] $header The list that
     * represents the table header.
     * @return bool True if the table header is valid or false if the table
     * header is not valid.
     */
    protected function validateHeader($header)
    {
        if (empty($header)) {
            return false;
        }
        $length = -1;
        foreach ($header as $elements) {
            if (empty($elements)) {
                return false;
            } elseif ($length === -1) {
                $length = sizeof($elements);
            } elseif (sizeof($elements) !== $length) {
                return false;
            }
        }
        return true;
    }

    /**
     * Returns a list with ids of rows of same column.
     * @param \hatemile\util\html\HTMLDOMElement[][] $header The list that
     * represents the table header.
     * @param int $index The index of columns.
     * @return string[] The list with ids of rows of same column.
     */
    protected function getCellsHeadersIds($header, $index)
    {
        $ids = array();
        foreach ($header as $row) {
            if ($row[$index]->getTagName() === 'TH') {
                array_push($ids, $row[$index]->getAttribute('id'));
            }
        }
        return $ids;
    }

    /**
     * Associate the data cell with header cell of row.
     * @param \hatemile\util\html\HTMLDOMElement $element The table body or
     * table footer.
     */
    protected function associateDataCellsWithHeaderCellsOfRow(
        HTMLDOMElement $element
    ) {
        $table = $this->getModelTable($element);
        foreach ($table as $row) {
            $headersIds = array();
            foreach ($row as $cell) {
                if ($cell->getTagName() === 'TH') {
                    $this->idGenerator->generateId($cell);
                    array_push($headersIds, $cell->getAttribute('id'));

                    $cell->setAttribute('scope', 'row');
                }
            }
            if (!empty($headersIds)) {
                foreach ($row as $cell) {
                    if ($cell->getTagName() === 'TD') {
                        $headers = $cell->getAttribute('headers');
                        foreach ($headersIds as $headerId) {
                            $headers = CommonFunctions::increaseInList(
                                $headers,
                                $headerId
                            );
                        }
                        $cell->setAttribute('headers', $headers);
                    }
                }
            }
        }
    }

    /**
     * Set the scope of header cells of table header.
     * @param \hatemile\util\html\HTMLDOMElement $tableHeader The table header.
     */
    protected function prepareHeaderCells(HTMLDOMElement $tableHeader)
    {
        $cells = $this->parser->find($tableHeader)->findChildren(
            'tr'
        )->findChildren('th')->listResults();
        foreach ($cells as $cell) {
            $this->idGenerator->generateId($cell);

            $cell->setAttribute('scope', 'col');
        }
    }

    public function associateDataCellsWithHeaderCells(HTMLDOMElement $table)
    {
        $header = $this->parser->find($table)->findChildren(
            'thead'
        )->firstResult();
        $body = $this->parser->find($table)->findChildren(
            'tbody'
        )->firstResult();
        $footer = $this->parser->find($table)->findChildren(
            'tfoot'
        )->firstResult();
        if ($header !== null) {
            $this->prepareHeaderCells($header);

            $headerRows = $this->getModelTable($header);
            if (($body !== null) && ($this->validateHeader($headerRows))) {
                $lengthHeader = sizeof($headerRows[0]);
                $fakeTable = $this->getModelTable($body);
                if ($footer !== null) {
                    $fakeTable = array_merge(
                        $fakeTable,
                        $this->getModelTable($footer)
                    );
                }
                foreach ($fakeTable as $row) {
                    if (sizeof($row) === $lengthHeader) {
                        $i = 0;
                        foreach ($row as $cell) {
                            $headersIds = $this->getCellsHeadersIds(
                                $headerRows,
                                $i
                            );
                            $headers = $cell->getAttribute('headers');
                            foreach ($headersIds as $headersId) {
                                $headers = CommonFunctions::increaseInList(
                                    $headers,
                                    $headersId
                                );
                            }
                            $cell->setAttribute('headers', $headers);
                            $i++;
                        }
                    }
                }
            }
        }
        if ($body !== null) {
            $this->associateDataCellsWithHeaderCellsOfRow($body);
        }
        if ($footer !== null) {
            $this->associateDataCellsWithHeaderCellsOfRow($footer);
        }
    }

    public function associateAllDataCellsWithHeaderCells()
    {
        $tables = $this->parser->find('table')->listResults();
        foreach ($tables as $table) {
            if (CommonFunctions::isValidElement($table)) {
                $this->associateDataCellsWithHeaderCells($table);
            }
        }
    }

    public function associateLabelWithField(HTMLDOMElement $label)
    {
        if ($label->getTagName() === 'LABEL') {
            if ($label->hasAttribute('for')) {
                $field = $this->parser->find(
                    '#' .
                    $label->getAttribute('for')
                )->firstResult();
            } else {
                $field = $this->parser->find(
                    $label
                )->findDescendants('input,select,textarea')->firstResult();

                if ($field !== null) {
                    $this->idGenerator->generateId($field);
                    $label->setAttribute('for', $field->getAttribute('id'));
                }
            }
            if ($field !== null) {
                if (!$field->hasAttribute('aria-label')) {
                    $field->setAttribute(
                        'aria-label',
                        \trim(preg_replace(
                            '/[ \n\r\t]+/',
                            ' ',
                            $label->getTextContent()
                        ))
                    );
                }

                $this->idGenerator->generateId($label);
                $field->setAttribute(
                    'aria-labelledby',
                    CommonFunctions::increaseInList(
                        $field->getAttribute('aria-labelledby'),
                        $label->getAttribute('id')
                    )
                );
            }
        }
    }

    public function associateAllLabelsWithFields()
    {
        $labels = $this->parser->find('label')->listResults();
        foreach ($labels as $label) {
            if (CommonFunctions::isValidElement($label)) {
                $this->associateLabelWithField($label);
            }
        }
    }
}
