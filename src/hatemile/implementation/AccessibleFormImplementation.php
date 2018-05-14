<?php
/**
 * Class AccessibleFormImplementation.
 * 
 * @package hatemile\implementation
 * @author Carlson Santana Cruz
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @copyright (c) 2018, HaTeMiLe
 */

namespace hatemile\implementation;

require_once join(DIRECTORY_SEPARATOR, array(
    dirname(dirname(__FILE__)),
    'AccessibleForm.php'
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

use \hatemile\AccessibleForm;
use \hatemile\util\CommonFunctions;
use \hatemile\util\IDGenerator;
use \hatemile\util\html\HTMLDOMElement;
use \hatemile\util\html\HTMLDOMParser;

/**
 * The AccessibleFormImplementation class is official implementation of
 * AccessibleForm.
 */
class AccessibleFormImplementation implements AccessibleForm
{

    /**
     * The ID of script element that contains the list of IDs of fields with
     * validation.
     * @var string
     */
    const ID_SCRIPT_LIST_VALIDATION_FIELDS =
            'hatemile-scriptlist-validation-fields';

    /**
     * The ID of script element that execute validations on fields.
     * @var string
     */
    const ID_SCRIPT_EXECUTE_VALIDATION = 'hatemile-validation-script';

    /**
     * The client-site required fields list.
     * @var string
     */
    const REQUIRED_FIELDS_LIST = 'required_fields';

    /**
     * The client-site pattern fields list.
     * @var string
     */
    const PATTERN_FIELDS_LIST = 'pattern_fields';

    /**
     * The client-site fields with length list.
     * @var string
     */
    const LIMITED_FIELDS_LIST = 'fields_with_length';

    /**
     * The client-site range fields list.
     * @var string
     */
    const RANGE_FIELDS_LIST = 'range_fields';

    /**
     * The client-site week fields list.
     * @var string
     */
    const WEEK_FIELDS_LIST = 'week_fields';

    /**
     * The client-site month fields list.
     * @var string
     */
    const MONTH_FIELDS_LIST = 'month_fields';

    /**
     * The client-site datetime fields list.
     * @var string
     */
    const DATETIME_FIELDS_LIST = 'datetime_fields';

    /**
     * The client-site time fields list.
     * @var string
     */
    const TIME_FIELDS_LIST = 'time_fields';

    /**
     * The client-site date fields list.
     * @var string
     */
    const DATE_FIELDS_LIST = 'date_fields';

    /**
     * The client-site email fields list.
     * @var string
     */
    const EMAIL_FIELDS_LIST = 'email_fields';

    /**
     * The client-site URL fields list.
     * @var string
     */
    const URL_FIELDS_LIST = 'url_fields';

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
     * The state that indicates if the scripts used by solutions was added in
     * parser.
     * @var bool
     */
    protected $scriptsAdded;

    /**
     * The script element that contains the list with IDs of fields with
     * validation.
     * @var \hatemile\util\html\HTMLDOMElement
     */
    protected $scriptListFieldsWithValidation;

    /**
     * Initializes a new object that manipulate the accessibility of the forms
     * of parser.
     * @param \hatemile\util\html\HTMLDOMParser $parser The HTML parser.
     */
    public function __construct(HTMLDOMParser $parser)
    {
        $this->parser = $parser;
        $this->idGenerator = new IDGenerator('form');
    }

    /**
     * Returns the appropriate value for attribute aria-autocomplete of field.
     * @param \hatemile\util\html\HTMLDOMElement $field The field.
     * @return string The ARIA value of field.
     */
    protected function getARIAAutoComplete(HTMLDOMElement $field)
    {
        $tagName = $field->getTagName();
        $type = null;
        if ($field->hasAttribute('type')) {
            $type = strtolower($field->getAttribute('type'));
        }
        if (
            ($tagName === 'TEXTAREA')
            || (
                ($tagName === 'INPUT')
                && (!(
                    ('button' === $type)
                    || ('submit' === $type)
                    || ('reset' === $type)
                    || ('image' === $type)
                    || ('file' === $type)
                    || ('checkbox' === $type)
                    || ('radio' === $type)
                    || ('hidden' === $type)
                ))
            )
        ) {
            $value = null;
            if ($field->hasAttribute('autocomplete')) {
                $value = strtolower($field->getAttribute('autocomplete'));
            } else {
                $form = $this->parser->find($field)->findAncestors(
                    'form'
                )->firstResult();
                if (($form === null) && ($field->hasAttribute('form'))) {
                    $form = $this->parser->find(
                        '#' .
                        $field->getAttribute('form')
                    )->firstResult();
                }
                if (($form !== null) && ($form->hasAttribute('autocomplete'))) {
                    $value = strtolower($form->getAttribute('autocomplete'));
                }
            }
            if ('on' === $value) {
                return 'both';
            } elseif (
                ($field->hasAttribute('list'))
                && ($this->parser->find(
                    'datalist[id="' .
                    $field->getAttribute('list') .
                    '"]'
                )->firstResult() !== null)
            ) {
                return 'list';
            } elseif ('off' === $value) {
                return 'none';
            }
        }
        return null;
    }

    /**
     * Include the scripts used by solutions.
     */
    protected function generateValidationScripts()
    {
        $local = $this->parser->find('head,body')->firstResult();
        if ($local !== null) {
            if ($this->parser->find(
                '#' .
                AccessibleEventImplementation::ID_SCRIPT_COMMON_FUNCTIONS
            )->firstResult() === null) {
                $commonFunctionsScript = $this->parser->createElement('script');
                $commonFunctionsScript->setAttribute(
                    'id',
                    AccessibleEventImplementation::ID_SCRIPT_COMMON_FUNCTIONS
                );
                $commonFunctionsScript->setAttribute('type', 'text/javascript');
                $commonFunctionsScript->appendText(file_get_contents(
                    join(DIRECTORY_SEPARATOR, array(
                        dirname(dirname(dirname(__FILE__))),
                        'js',
                        'common.js'
                    ))
                ));
                $local->prependElement($commonFunctionsScript);
            }
            
            $this->scriptListFieldsWithValidation = $this->parser->find(
                '#' .
                AccessibleFormImplementation::ID_SCRIPT_LIST_VALIDATION_FIELDS
            )->firstResult();
            if ($this->scriptListFieldsWithValidation === null) {
                $this->scriptListFieldsWithValidation
                        = $this->parser->createElement('script');
                $this->scriptListFieldsWithValidation->setAttribute(
                    'id',
                    AccessibleFormImplementation
                        ::ID_SCRIPT_LIST_VALIDATION_FIELDS
                );
                $this->scriptListFieldsWithValidation->setAttribute(
                    'type',
                    'text/javascript'
                );
                $this->scriptListFieldsWithValidation->appendText(
                    file_get_contents(join(DIRECTORY_SEPARATOR, array(
                        dirname(dirname(dirname(__FILE__))),
                        'js',
                        'scriptlist_validation_fields.js'
                    )))
                );
                $local->appendElement($this->scriptListFieldsWithValidation);
            }
            if ($this->parser->find(
                '#' .
                AccessibleFormImplementation::ID_SCRIPT_EXECUTE_VALIDATION
            )->firstResult() === null) {
                $scriptFunction = $this->parser->createElement('script');
                $scriptFunction->setAttribute(
                    'id',
                    AccessibleFormImplementation::ID_SCRIPT_EXECUTE_VALIDATION
                );
                $scriptFunction->setAttribute('type', 'text/javascript');
                $scriptFunction->appendText(file_get_contents(
                    join(DIRECTORY_SEPARATOR, array(
                        dirname(dirname(dirname(__FILE__))),
                        'js',
                        'validation.js'
                    ))
                ));

                $this->parser->find('body')->firstResult()->appendElement(
                    $scriptFunction
                );
            }
        }
        $this->scriptsAdded = true;
    }

    /**
     * Validate the field when its value change.
     * @param \hatemile\util\html\HTMLDOMElement $field The field.
     * @param string $listAttribute The list attribute of field with validation.
     */
    protected function validate(HTMLDOMElement $field, $listAttribute)
    {
        if (!$this->scriptsAdded) {
            $this->generateValidationScripts();
        }
        $this->idGenerator->generateId($field);
        $this->scriptListFieldsWithValidation->appendText(
            'hatemileValidationList.' .
            $listAttribute .
            '.push("' .
            $field->getAttribute('id') .
            '");'
        );
    }

    public function markRequiredField(HTMLDOMElement $requiredField)
    {
        if ($requiredField->hasAttribute('required')) {
            $requiredField->setAttribute('aria-required', 'true');
        }
    }

    public function markAllRequiredFields()
    {
        $requiredFields = $this->parser->find('[required]')->listResults();
        foreach ($requiredFields as $requiredField) {
            if (CommonFunctions::isValidElement($requiredField)) {
                $this->markRequiredField($requiredField);
            }
        }
    }

    public function markRangeField(HTMLDOMElement $rangeField)
    {
        if ($rangeField->hasAttribute('min')) {
            $rangeField->setAttribute(
                'aria-valuemin',
                $rangeField->getAttribute('min')
            );
        }
        if ($rangeField->hasAttribute('max')) {
            $rangeField->setAttribute(
                'aria-valuemax',
                $rangeField->getAttribute('max')
            );
        }
    }

    public function markAllRangeFields()
    {
        $rangeFields = $this->parser->find('[min],[max]')->listResults();
        foreach ($rangeFields as $rangeField) {
            if (CommonFunctions::isValidElement($rangeField)) {
                $this->markRangeField($rangeField);
            }
        }
    }

    public function markAutoCompleteField(HTMLDOMElement $autoCompleteField)
    {
        $ariaAutoComplete = $this->getARIAAutoComplete($autoCompleteField);
        if (!empty($ariaAutoComplete)) {
            $autoCompleteField->setAttribute(
                'aria-autocomplete',
                $ariaAutoComplete
            );
        }
    }

    public function markAllAutoCompleteFields()
    {
        $autoCompleteFields = $this->parser->find(
            'input[autocomplete],textarea[autocomplete],' .
            'form[autocomplete] input, form[autocomplete] textarea,[list],' .
            '[form]'
        )->listResults();
        foreach ($autoCompleteFields as $autoCompleteField) {
            if (CommonFunctions::isValidElement($autoCompleteField)) {
                $this->markAutoCompleteField($autoCompleteField);
            }
        }
    }

    public function markInvalidField(HTMLDOMElement $field)
    {
        if (
            ($field->hasAttribute('required'))
            || (
                ($field->hasAttribute('aria-required'))
                && (\strtolower(
                    $field->getAttribute('aria-required')
                ) === 'true')
            )
        ) {
            $this->validate(
                $field,
                AccessibleFormImplementation::REQUIRED_FIELDS_LIST
            );
        }
        if ($field->hasAttribute('pattern')) {
            $this->validate(
                $field,
                AccessibleFormImplementation::PATTERN_FIELDS_LIST
            );
        }
        if (
            ($field->hasAttribute('minlength'))
            || ($field->hasAttribute('maxlength'))
        ) {
            $this->validate(
                $field,
                AccessibleFormImplementation::LIMITED_FIELDS_LIST
            );
        }
        if (
            ($field->hasAttribute('aria-valuemin'))
            || ($field->hasAttribute('aria-valuemax'))
        ) {
            $this->validate(
                $field,
                AccessibleFormImplementation::RANGE_FIELDS_LIST
            );
        }
        if ($field->hasAttribute('type')) {
            $type = \strtolower($field->getAttribute('type'));
            if ($type === 'week') {
                $this->validate(
                    $field,
                    AccessibleFormImplementation::WEEK_FIELDS_LIST
                );
            } elseif ($type === 'month') {
                $this->validate(
                    $field,
                    AccessibleFormImplementation::MONTH_FIELDS_LIST
                );
            } elseif (($type === 'datetime-local') || ($type === 'datetime')) {
                $this->validate(
                    $field,
                    AccessibleFormImplementation::DATETIME_FIELDS_LIST
                );
            } elseif ($type === 'time') {
                $this->validate(
                    $field,
                    AccessibleFormImplementation::TIME_FIELDS_LIST
                );
            } elseif ($type === 'date') {
                $this->validate(
                    $field,
                    AccessibleFormImplementation::DATE_FIELDS_LIST
                );
            } elseif (($type === 'number') || ($type === 'range')) {
                $this->validate(
                    $field,
                    AccessibleFormImplementation::RANGE_FIELDS_LIST
                );
            } elseif ($type === 'email') {
                $this->validate(
                    $field,
                    AccessibleFormImplementation::EMAIL_FIELDS_LIST
                );
            } elseif ($type === 'url') {
                $this->validate(
                    $field,
                    AccessibleFormImplementation::URL_FIELDS_LIST
                );
            }
        }
    }

    public function markAllInvalidFields()
    {
        $fields = $this->parser->find(
            '[required],input[pattern],input[minlength],input[maxlength],' .
            'textarea[minlength],textarea[maxlength],input[type=week],' .
            'input[type=month],input[type=datetime-local],' .
            'input[type=datetime],input[type=time],input[type=date],' .
            'input[type=number],input[type=range],input[type=email],' .
            'input[type=url],[aria-required=true],input[aria-valuemin],' .
            'input[aria-valuemax]'
        )->listResults();
        foreach ($fields as $field) {
            if (CommonFunctions::isValidElement($field)) {
                $this->markInvalidField($field);
            }
        }
    }
}
