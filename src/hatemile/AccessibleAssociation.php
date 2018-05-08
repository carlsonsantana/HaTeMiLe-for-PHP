<?php
/**
 * Interface AccessibleAssociation.
 * 
 * @package hatemile
 * @author Carlson Santana Cruz
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @copyright (c) 2018, HaTeMiLe
 */

namespace hatemile;

require_once join(DIRECTORY_SEPARATOR, array(
    dirname(__FILE__),
    'util',
    'html',
    'HTMLDOMElement.php'
));

use \hatemile\util\html\HTMLDOMElement;

/**
 * The AccessibleAssociation interface improve accessibility, associating
 * elements.
 */
interface AccessibleAssociation
{

    /**
     * Associate all data cells with header cells of table.
     * @param \hatemile\util\html\HTMLDOMElement $table The table.
     */
    public function associateDataCellsWithHeaderCells(HTMLDOMElement $table);

    /**
     * Associate all data cells with header cells of all tables of page.
     */
    public function associateAllDataCellsWithHeaderCells();

    /**
     * Associate label with field.
     * @param \hatemile\util\html\HTMLDOMElement $label The label.
     */
    public function associateLabelWithField(HTMLDOMElement $label);

    /**
     * Associate all labels of page with fields.
     */
    public function associateAllLabelsWithFields();
}
