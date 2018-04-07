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

require_once dirname(__FILE__) . '/../AccessibleForm.php';
require_once dirname(__FILE__) . '/../util/html/HTMLDOMElement.php';
require_once dirname(__FILE__) . '/../util/html/HTMLDOMParser.php';
require_once dirname(__FILE__) . '/../util/CommonFunctions.php';
require_once dirname(__FILE__) . '/../util/Configure.php';

use \hatemile\AccessibleForm;
use \hatemile\util\html\HTMLDOMElement;
use \hatemile\util\html\HTMLDOMParser;
use \hatemile\util\Configure;
use \hatemile\util\CommonFunctions;

/**
 * The AccessibleFormImplementation class is official implementation of
 * AccessibleForm interface.
 */
class AccessibleFormImplementation implements AccessibleForm
{

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
     * Initializes a new object that manipulate the accessibility of the forms
     * of parser.
     * @param \hatemile\util\html\HTMLDOMParser $parser The HTML parser.
     * @param \hatemile\util\Configure $configure The configuration of HaTeMiLe.
     */
    public function __construct(HTMLDOMParser $parser, Configure $configure)
    {
        $this->parser = $parser;
        $this->prefixId = $configure->getParameter('prefix-generated-ids');
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
                        '#' . $field->getAttribute('form')
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
                    'datalist[id="' . $field->getAttribute('list') . '"]'
                )->firstResult() !== null)
            ) {
                return 'list';
            } elseif ('off' === $value) {
                return 'none';
            }
        }
        return null;
    }

    public function fixRequiredField(HTMLDOMElement $requiredField)
    {
        if ($requiredField->hasAttribute('required')) {
            $requiredField->setAttribute('aria-required', 'true');
        }
    }

    public function fixRequiredFields()
    {
        $requiredFields = $this->parser->find('[required]')->listResults();
        foreach ($requiredFields as $requiredField) {
            if (CommonFunctions::isValidElement($requiredField)) {
                $this->fixRequiredField($requiredField);
            }
        }
    }

    public function fixRangeField(HTMLDOMElement $rangeField)
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

    public function fixRangeFields()
    {
        $rangeFields = $this->parser->find('[min],[max]')->listResults();
        foreach ($rangeFields as $rangeField) {
            if (CommonFunctions::isValidElement($rangeField)) {
                $this->fixRangeField($rangeField);
            }
        }
    }

    public function fixAutoCompleteField(HTMLDOMElement $autoCompleteField)
    {
        $ariaAutoComplete = $this->getARIAAutoComplete($autoCompleteField);
        if (!empty($ariaAutoComplete)) {
            $autoCompleteField->setAttribute(
                'aria-autocomplete',
                $ariaAutoComplete
            );
        }
    }

    public function fixAutoCompleteFields()
    {
        $elements = $this->parser->find(
            'input[autocomplete],textarea[autocomplete],'
            . 'form[autocomplete] input, form[autocomplete] textarea,[list],'
            . '[form]'
        )->listResults();
        foreach ($elements as $element) {
            if (CommonFunctions::isValidElement($element)) {
                $this->fixAutoCompleteField($element);
            }
        }
    }
}
