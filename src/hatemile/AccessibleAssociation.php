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
