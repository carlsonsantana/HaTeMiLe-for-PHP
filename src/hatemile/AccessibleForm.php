<?php
/**
 * Interface AccessibleForm.
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
 * The AccessibleForm interface improve the accessibility of forms.
 */
interface AccessibleForm
{

    /**
     * Mark that the field is required.
     * @param \hatemile\util\html\HTMLDOMElement $requiredField The required
     * field.
     */
    public function markRequiredField(HTMLDOMElement $requiredField);

    /**
     * Mark that the fields is required.
     */
    public function markAllRequiredFields();

    /**
     * Mark that the field have range.
     * @param \hatemile\util\html\HTMLDOMElement $rangeField The range field.
     */
    public function markRangeField(HTMLDOMElement $rangeField);

    /**
     * Mark that the fields have range.
     */
    public function markAllRangeFields();

    /**
     * Mark that the field have autocomplete.
     * @param \hatemile\util\html\HTMLDOMElement $autoCompleteField The field
     * with autocomplete.
     */
    public function markAutoCompleteField(HTMLDOMElement $autoCompleteField);

    /**
     * Mark that the fields have autocomplete.
     */
    public function markAllAutoCompleteFields();

    /**
     * Mark a solution to display that this field is invalid.
     * @param \hatemile\util\html\HTMLDOMElement $field The field.
     */
    public function markInvalidField(HTMLDOMElement $field);

    /**
     * Mark a solution to display that a fields are invalid.
     */
    public function markAllInvalidFields();
}
