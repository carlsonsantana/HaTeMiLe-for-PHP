<?php
/**
 * Class AccessibleCSSImplementation.
 * 
 * @package hatemile\implementation
 * @author Carlson Santana Cruz
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @copyright (c) 2018, HaTeMiLe
 */

namespace hatemile\implementation;

require_once join(DIRECTORY_SEPARATOR, array(
    dirname(dirname(__FILE__)),
    'AccessibleCSS.php'
));
require_once join(DIRECTORY_SEPARATOR, array(
    dirname(dirname(__FILE__)),
    'util',
    'CommonFunctions.php'
));
require_once join(DIRECTORY_SEPARATOR, array(
    dirname(dirname(__FILE__)),
    'util',
    'Configure.php'
));
require_once join(DIRECTORY_SEPARATOR, array(
    dirname(dirname(__FILE__)),
    'util',
    'css',
    'StyleSheetParser.php'
));
require_once join(DIRECTORY_SEPARATOR, array(
    dirname(dirname(__FILE__)),
    'util',
    'css',
    'StyleSheetRule.php'
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

use \hatemile\AccessibleCSS;
use \hatemile\util\CommonFunctions;
use \hatemile\util\Configure;
use \hatemile\util\css\StyleSheetParser;
use \hatemile\util\css\StyleSheetRule;
use \hatemile\util\html\HTMLDOMElement;
use \hatemile\util\html\HTMLDOMParser;

/**
 * The AccessibleCSSImplementation class is official implementation of
 * AccessibleCSS.
 */
class AccessibleCSSImplementation implements AccessibleCSS
{

    /**
     * The name of attribute for identify isolator elements.
     * @var string
     */
    const DATA_ISOLATOR_ELEMENT = 'data-auxiliarspan';

    /**
     * The name of attribute for identify the element created or modified to
     * support speak property.
     * @var string
     */
    const DATA_SPEAK = 'data-cssspeak';

    /**
     * The name of attribute for identify the element created or modified to
     * support speak-as property.
     * @var string
     */
    const DATA_SPEAK_AS = 'data-cssspeakas';

    /**
     * The valid element tags for inherit the speak and speak-as properties.
     * @var string[]
     */
    public static $VALID_INHERIT_TAGS = array(
        'SPAN',
        'A',
        'RT',
        'DFN',
        'ABBR',
        'Q',
        'CITE',
        'EM',
        'TIME',
        'VAR',
        'SAMP',
        'I',
        'B',
        'SUB',
        'SUP',
        'SMALL',
        'STRONG',
        'MARK',
        'RUBY',
        'INS',
        'DEL',
        'KBD',
        'BDO',
        'CODE',
        'P',
        'FIGCAPTION',
        'FIGURE',
        'PRE',
        'DIV',
        'OL',
        'UL',
        'LI',
        'BLOCKQUOTE',
        'DL',
        'DT',
        'DD',
        'FIELDSET',
        'LEGEND',
        'LABEL',
        'FORM',
        'BODY',
        'ASIDE',
        'ADDRESS',
        'H1',
        'H2',
        'H3',
        'H4',
        'H5',
        'H6',
        'SECTION',
        'HEADER',
        'NAV',
        'ARTICLE',
        'FOOTER',
        'HGROUP',
        'CAPTION',
        'SUMMARY',
        'DETAILS',
        'TABLE',
        'TR',
        'TD',
        'TH',
        'TBODY',
        'THEAD',
        'TFOOT'
    );

    /**
     * The valid element tags for speak and speak-as properties.
     * @var string[]
     */
    public static $VALID_TAGS = array(
        'SPAN',
        'A',
        'RT',
        'DFN',
        'ABBR',
        'Q',
        'CITE',
        'EM',
        'TIME',
        'VAR',
        'SAMP',
        'I',
        'B',
        'SUB',
        'SUP',
        'SMALL',
        'STRONG',
        'MARK',
        'RUBY',
        'INS',
        'DEL',
        'KBD',
        'BDO',
        'CODE',
        'P',
        'FIGCAPTION',
        'FIGURE',
        'PRE',
        'DIV',
        'LI',
        'BLOCKQUOTE',
        'DT',
        'DD',
        'FIELDSET',
        'LEGEND',
        'LABEL',
        'FORM',
        'BODY',
        'ASIDE',
        'ADDRESS',
        'H1',
        'H2',
        'H3',
        'H4',
        'H5',
        'H6',
        'SECTION',
        'HEADER',
        'NAV',
        'ARTICLE',
        'FOOTER',
        'CAPTION',
        'SUMMARY',
        'DETAILS',
        'TD',
        'TH'
    );

    /**
     * The regular expression to validate speak-as property.
     * @var string
     */
    public static $REGULAR_EXPRESSION_SPEAK_AS = (
        '/^((normal)|(inherit)|(initial)|(digits)|(literal\\-punctuation)|' .
        '(no\\-punctuation)|(spell\\-out)|((digits) ((literal\\-punctuation)|' .
        '(no\\-punctuation)|(spell\\-out)))|(((literal\\-punctuation)|' .
        '(no\\-punctuation)|(spell\\-out)) (digits))|' .
        '(((literal\\-punctuation)|(no\\-punctuation)) (spell\\-out))|' .
        '((spell\\-out) ((literal\\-punctuation)|(no\\-punctuation)))|' .
        '((digits) ((literal\\-punctuation)|(no\\-punctuation)) ' .
        '(spell\\-out))|((digits) (spell\\-out) ((literal\\-punctuation)|' .
        '(no\\-punctuation)))|(((literal\\-punctuation)|' .
        '(no\\-punctuation)) (digits) (spell\\-out))|' .
        '(((literal\\-punctuation)|(no\\-punctuation)) (spell\\-out) ' .
        '(digits))|((spell\\-out) (digits) ((literal\\-punctuation)|' .
        '(no\\-punctuation)))|((spell\\-out) ((literal\\-punctuation)' .
        '|(no\\-punctuation)) (digits)))$/'
    );

    /**
     * The HTML parser.
     * @var \hatemile\util\html\HTMLDOMParser
     */
    protected $htmlParser;

    /**
     * The CSS parser.
     * @var \hatemile\util\css\StyleSheetParser
     */
    protected $cssParser;

    /**
     * The configuration of HaTeMiLe.
     * @var \hatemile\util\Configure
     */
    protected $configure;

    /**
     * The symbols with descriptions.
     * @var string[]
     */
    protected $symbols;

    /**
     * The operation to speak one letter at a time for each word.
     * @var callable
     */
    protected $operationSpeakAsSpellOut;

    /**
     * The operation to speak the punctuation.
     * @var callable
     */
    protected $operationSpeakAsLiteralPunctuation;

    /**
     * The operation to no speak the punctuation for element.
     * @var callable
     */
    protected $operationSpeakAsNoPunctuation;

    /**
     * The operation to speak the digit at a time for each number.
     * @var callable
     */
    protected $operationSpeakAsDigits;

    /**
     * Initializes a new object that manipulate the accessibility of the CSS of
     * parser.
     * @param \hatemile\util\html\HTMLDOMParser $htmlParser The HTML parser.
     * @param \hatemile\util\css\StyleSheetParser $cssParser The CSS parser.
     * @param \hatemile\util\Configure $configure The configuration of HaTeMiLe.
     * @param string $symbolFileName The file path of symbol configuration.
     */
    public function __construct(
        HTMLDOMParser $htmlParser,
        StyleSheetParser $cssParser,
        Configure $configure,
        $symbolFileName = null
    ) {
        $this->htmlParser = $htmlParser;
        $this->cssParser = $cssParser;
        $this->configure = $configure;
        $this->symbols = $this->getSymbols($symbolFileName, $configure);
        $this->operationSpeakAsSpellOut = null;
        $this->operationSpeakAsLiteralPunctuation = null;
        $this->operationSpeakAsNoPunctuation = null;
        $this->operationSpeakAsDigits = null;
    }

    /**
     * Returns the symbols of configuration.
     * @param string $fileName The file path of symbol configuration.
     * @param \hatemile\util\Configure $configure The configuration of HaTeMiLe.
     * @return string[] The symbols of configuration.
     */
    protected function getSymbols($fileName, Configure $configure)
    {
        $symbols = array();
        if ($fileName === null) {
            $fileName = join(DIRECTORY_SEPARATOR, array(
                dirname(dirname(dirname(__FILE__))),
                'symbols.xml'
            ));
        }
        $file = new \DOMDocument();
        $file->load($fileName);
        $document = $file->documentElement;
        $childNodes = $document->childNodes;
        foreach ($childNodes as $child) {
            if (
                ($child instanceof \DOMElement)
                && (strtoupper($child->tagName) === 'SYMBOL')
                && ($child->hasAttribute('symbol'))
                && ($child->hasAttribute('description'))
            ) {
                $symbols[$child->getAttribute(
                    'symbol'
                )] = $configure->getParameter(
                    $child->getAttribute('description')
                );
            }
        }

        return $symbols;
    }

    /**
     * Returns the symbol formated to be searched by regular expression.
     * @param string $symbol The symbol.
     * @return string The symbol formated.
     */
    protected function getFormatedSymbol($symbol)
    {
        $search = array(
            '\\',
            '.',
            '+',
            '*',
            '?',
            '^',
            '$',
            '[',
            ']',
            '{',
            '}',
            '(',
            ')',
            '|',
            '/',
            ',',
            '!',
            '=',
            ':',
            '-'
        );
        $replace = array(
            '\\\\',
            '\\.',
            '\\+',
            '\\*',
            '\\?',
            '\\^',
            '\\$',
            '\\[',
            '\\]',
            '\\{',
            '\\}',
            '\\(',
            '\\)',
            '\\|',
            '\\/',
            '\\,',
            '\\!',
            '\\=',
            '\\:',
            '\\-'
        );
        return str_replace($search, $replace, $symbol);
    }

    /**
     * Returns the description of symbol.
     * @param string $symbol The symbol.
     * @return string The description of symbol.
     */
    protected function getDescriptionOfSymbol($symbol)
    {
        if (isset($this->symbols[$symbol])) {
            return $this->symbols[$symbol];
        }
        return null;
    }

    /**
     * Returns the regular expression to search all symbols.
     * @return string The regular expression to search all symbols.
     */
    protected function getRegularExpressionOfSymbols()
    {
        $regularExpression = null;
        foreach ($this->symbols as $symbol => $description) {
            $formatedSymbol = $this->getFormatedSymbol($symbol);
            if ($regularExpression === null) {
                $regularExpression = '(' . $formatedSymbol . ')';
            } else {
                $regularExpression = (
                    $regularExpression .
                    '|(' .
                    $formatedSymbol .
                    ')'
                );
            }
        }
        return '/' . $regularExpression . '/';
    }

    /**
     * Check that the children of element can be manipulated to apply the CSS
     * properties.
     * @param \hatemile\util\html\HTMLDOMElement $element The element.
     * @return bool True if the children of element can be manipulated to apply
     * the CSS properties or false if the children of element cannot be
     * manipulated to apply the CSS properties.
     */
    protected function isValidInheritElement(HTMLDOMElement $element)
    {
        return (
            in_array(
                $element->getTagName(),
                AccessibleCSSImplementation::$VALID_INHERIT_TAGS
            )
            && (!$element->hasAttribute(CommonFunctions::DATA_IGNORE))
        );
    }

    /**
     * Check that the element can be manipulated to apply the CSS properties.
     * @param \hatemile\util\html\HTMLDOMElement $element The element.
     * @return bool True if the element can be manipulated to apply the CSS
     * properties or false if the element cannot be manipulated to apply the CSS
     * properties.
     */
    protected function isValidElement(HTMLDOMElement $element)
    {
        return in_array(
            $element->getTagName(),
            AccessibleCSSImplementation::$VALID_TAGS
        );
    }

    /**
     * Isolate text nodes of element nodes.
     * @param \hatemile\util\html\HTMLDOMElement $element The element.
     */
    protected function isolateTextNode(HTMLDOMElement $element)
    {
        if (
            ($element->hasChildrenElements())
            && ($this->isValidElement($element))
        ) {
            if ($this->isValidElement($element)) {
                $childNodes = $element->getChildren();
                foreach ($childNodes as $childNode) {
                    if ($childNode instanceof HTMLDOMTextNode) {
                        $span = $this->htmlParser->createElement('span');
                        $span->setAttribute(
                            AccessibleCSSImplementation::DATA_ISOLATOR_ELEMENT,
                            'true'
                        );
                        $span->appendText($childNode->getTextContent());

                        $childNode->replaceNode($span);
                    }
                }
            }
            $children = $element->getChildrenElements();
            foreach ($children as $elementChild) {
                $this->isolateTextNode($elementChild);
            }
        }
    }

    /**
     * Replace the element by own text content.
     * @param \hatemile\util\html\HTMLDOMElement $element The element.
     */
    protected function replaceElementByOwnContent(HTMLDOMElement $element)
    {
        if ($element->hasChildrenElements()) {
            $children = $element->getChildrenElements();
            foreach ($children as $child) {
                $element->insertBefore($child);
            }
            $element->removeNode();
        } elseif ($element->hasChildren()) {
            $element->replaceNode($element->getFirstNodeChild());
        }
    }

    /**
     * Visit and execute a operation in element and descendants.
     * @param \hatemile\util\html\HTMLDOMElement $element The element.
     * @param callable $operation The operation to be executed.
     */
    protected function visit(HTMLDOMElement $element, $operation)
    {
        if ($this->isValidInheritElement($element)) {
            if ($element->hasChildrenElements()) {
                $children = $element->getChildrenElements();
                foreach ($children as $child) {
                    $this->visit($child, $operation);
                }
            } elseif ($this->isValidElement($element)) {
                call_user_func(array($this, $operation), $element);
            }
        }
    }

    /**
     * Create a element to show the content.
     * @param string $content The text content of element.
     * @param string $dataPropertyValue The value of custom attribute used to
     * identify the fix.
     * @return \hatemile\util\html\HTMLDOMElement The element to show the
     * content.
     */
    protected function createContentElement($content, $dataPropertyValue)
    {
        $contentElement = $this->htmlParser->createElement('span');
        $contentElement->setAttribute(
            AccessibleCSSImplementation::DATA_ISOLATOR_ELEMENT,
            'true'
        );
        $contentElement->setAttribute(
            AccessibleCSSImplementation::DATA_SPEAK_AS,
            $dataPropertyValue
        );
        $contentElement->appendText($content);
        return $contentElement;
    }

    /**
     * Create a element to show the content, only to aural displays.
     * @param string $content The text content of element.
     * @param string $dataPropertyValue The value of custom attribute used to
     * identify the fix.
     * @return \hatemile\util\html\HTMLDOMElement The element to show the
     * content.
     */
    protected function createAuralContentElement($content, $dataPropertyValue)
    {
        $contentElement = $this->createContentElement(
            $content,
            $dataPropertyValue
        );
        $contentElement->setAttribute('unselectable', 'on');
        $contentElement->setAttribute('class', 'screen-reader-only');
        return $contentElement;
    }

    /**
     * Create a element to show the content, only to visual displays.
     * @param string $content The text content of element.
     * @param string $dataPropertyValue The value of custom attribute used to
     * identify the fix.
     * @return \hatemile\util\html\HTMLDOMElement The element to show the
     * content.
     */
    protected function createVisualContentElement($content, $dataPropertyValue)
    {
        $contentElement = $this->createContentElement(
            $content,
            $dataPropertyValue
        );
        $contentElement->setAttribute('aria-hidden', 'true');
        $contentElement->setAttribute('role', 'presentation');
        return $contentElement;
    }

    /**
     * Speak the content of element only.
     * @param \hatemile\util\html\HTMLDOMElement $element The element.
     */
    protected function speakNormal(HTMLDOMElement $element)
    {
        if ($element->hasAttribute(AccessibleCSSImplementation::DATA_SPEAK)) {
            if (
                ($element->getAttribute(
                    AccessibleCSSImplementation::DATA_SPEAK
                ) === 'none')
                && (!$element->hasAttribute(
                    AccessibleCSSImplementation::DATA_ISOLATOR_ELEMENT
                ))
            ) {
                $element->removeAttribute('role');
                $element->removeAttribute('aria-hidden');
                $element->removeAttribute(
                    AccessibleCSSImplementation::DATA_SPEAK
                );
            } else {
                $this->replaceElementByOwnContent($element);
            }
        }
    }

    /**
     * Speak the content of element and descendants.
     * @param \hatemile\util\html\HTMLDOMElement $element The element.
     */
    protected function speakNormalInherit(HTMLDOMElement $element)
    {
        $this->visit($element, 'speakNormal');

        $element->normalize();
    }

    /**
     * No speak any content of element only.
     * @param \hatemile\util\html\HTMLDOMElement $element The element.
     */
    protected function speakNone(HTMLDOMElement $element)
    {
        $element->setAttribute('role', 'presentation');
        $element->setAttribute('aria-hidden', 'true');
        $element->setAttribute(AccessibleCSSImplementation::DATA_SPEAK, 'none');
    }

    /**
     * No speak any content of element and descendants.
     * @param \hatemile\util\html\HTMLDOMElement $element The element.
     */
    protected function speakNoneInherit(HTMLDOMElement $element)
    {
        $this->isolateTextNode($element);

        $this->visit($element, 'speakNone');
    }

    /**
     * Execute a operation by regular expression for element only.
     * @param \hatemile\util\html\HTMLDOMElement $element The element.
     * @param string $regularExpression The regular expression.
     * @param string $dataPropertyValue The value of custom attribute used to
     * identify the fix.
     * @param callable $operation The operation to be executed.
     */
    protected function speakAs(
        HTMLDOMElement $element,
        $regularExpression,
        $dataPropertyValue,
        $operation
    ) {
        $matches = null;
        $children = array();
        $content = $element->getTextContent();
        while (!empty($content)) {
            if (\preg_match(
                $regularExpression,
                $content,
                $matches,
                PREG_OFFSET_CAPTURE
            )) {
                $index = $matches[0][1];
                $children = $operation($content, $index, $children);

                $newIndex = $index + 1;
                $content = substr($content, $newIndex);
            } else {
                break;
            }
        }
        if (!empty($children)) {
            if (!empty($content)) {
                array_push($children, $this->createContentElement(
                    $content,
                    $dataPropertyValue
                ));
            }
            while ($element->hasChildren()) {
                $element->getFirstNodeChild()->removeNode();
            }
            foreach ($children as $child) {
                $element->appendElement($child);
            }
        }
    }

    /**
     * Revert changes of a speakAs method for element and descendants.
     * @param \hatemile\util\html\HTMLDOMElement $element The element.
     * @param string $dataPropertyValue The value of custom attribute used to
     * identify the fix.
     */
    protected function reverseSpeakAs(
        HTMLDOMElement $element,
        $dataPropertyValue
    ) {
        $dataProperty = (
            '[' .
            AccessibleCSSImplementation::DATA_SPEAK_AS .
            '="' .
            $dataPropertyValue .
            '"]'
        );

        $auxiliarElements = $this->htmlParser->find($element)->findDescendants(
            $dataProperty .
            '[unselectable="on"]'
        )->listResults();
        foreach ($auxiliarElements as $auxiliarElement) {
            $auxiliarElement->removeNode();
        }

        $contentElements = $this->htmlParser->find($element)->findDescendants(
            $dataProperty .
            '[' .
            AccessibleCSSImplementation::DATA_ISOLATOR_ELEMENT .
            '="true"]'
        )->listResults();
        foreach ($contentElements as $contentElement) {
            $this->replaceElementByOwnContent($contentElement);
        }

        $element->normalize();
    }

    /**
     * Use the default speak configuration of user agent for element and
     * descendants.
     * @param \hatemile\util\html\HTMLDOMElement $element The element.
     */
    protected function speakAsNormal(HTMLDOMElement $element)
    {
        $this->reverseSpeakAs($element, 'spell-out');
        $this->reverseSpeakAs($element, 'literal-punctuation');
        $this->reverseSpeakAs($element, 'no-punctuation');
        $this->reverseSpeakAs($element, 'digits');
    }

    /**
     * Speak one letter at a time for each word for element only.
     * @param \hatemile\util\html\HTMLDOMElement $element The element.
     */
    protected function speakAsSpellOut(HTMLDOMElement $element)
    {
        $dataPropertyValue = 'spell-out';
        if ($this->operationSpeakAsSpellOut === null) {
            $this->operationSpeakAsSpellOut = function (
                $content,
                $index,
                $children
            ) use ($dataPropertyValue) {
                array_push($children, $this->createContentElement(
                    substr($content, 0, $index + 1),
                    $dataPropertyValue
                ));

                array_push($children, $this->createAuralContentElement(
                    ' ',
                    $dataPropertyValue
                ));

                return $children;
            };
        }

        $this->speakAs(
            $element,
            '/[a-zA-Z]/',
            $dataPropertyValue,
            $this->operationSpeakAsSpellOut
        );
    }

    /**
     * Speak one letter at a time for each word for elements and descendants.
     * @param \hatemile\util\html\HTMLDOMElement $element The element.
     */
    protected function speakAsSpellOutInherit(HTMLDOMElement $element)
    {
        $this->reverseSpeakAs($element, 'spell-out');

        $this->isolateTextNode($element);

        $this->visit($element, 'speakAsSpellOut');
    }

    /**
     * Speak the punctuation for elements only.
     * @param \hatemile\util\html\HTMLDOMElement $element The element.
     */
    protected function speakAsLiteralPunctuation(HTMLDOMElement $element)
    {
        $dataPropertyValue = 'literal-punctuation';
        if ($this->operationSpeakAsLiteralPunctuation === null) {
            $this->operationSpeakAsLiteralPunctuation = function (
                $content,
                $index,
                $children
            ) use ($dataPropertyValue) {
                if ($index !== 0) {
                    array_push($children, $this->createContentElement(
                        substr($content, 0, $index),
                        $dataPropertyValue
                    ));
                }
                array_push($children, $this->createAuralContentElement(
                    (
                        ' ' .
                        $this->getDescriptionOfSymbol(substr(
                            $content,
                            $index,
                            1)
                        ) .
                        ' '
                    ),
                    $dataPropertyValue)
                );

                array_push($children, $this->createVisualContentElement(
                    substr($content, $index, 1),
                    $dataPropertyValue
                ));

                return $children;
            };
        }
        $this->speakAs(
            $element,
            $this->getRegularExpressionOfSymbols(),
            $dataPropertyValue,
            $this->operationSpeakAsLiteralPunctuation
        );
    }

    /**
     * Speak the punctuation for elements and descendants.
     * @param \hatemile\util\html\HTMLDOMElement $element The element.
     */
    protected function speakAsLiteralPunctuationInherit(HTMLDOMElement $element)
    {
        $this->reverseSpeakAs($element, 'literal-punctuation');
        $this->reverseSpeakAs($element, 'no-punctuation');

        $this->isolateTextNode($element);

        $this->visit($element, 'speakAsLiteralPunctuation');
    }

    /**
     * No speak the punctuation for element only.
     * @param \hatemile\util\html\HTMLDOMElement $element The element.
     */
    protected function speakAsNoPunctuation(HTMLDOMElement $element)
    {
        $dataPropertyValue = 'no-punctuation';
        if ($this->operationSpeakAsNoPunctuation === null) {
            $this->operationSpeakAsNoPunctuation = function (
                $content,
                $index,
                $children
            ) use ($dataPropertyValue) {
                if ($index !== 0) {
                    array_push($children, $this->createContentElement(
                        substr($content, 0, $index),
                        $dataPropertyValue
                    ));
                }
                array_push($children, $this->createVisualContentElement(
                    substr($content, $index, 1),
                    $dataPropertyValue
                ));

                return $children;
            };
        }
        $this->speakAs(
            $element,
            '/[!"#$%&\'\\(\\)\\*\\+,-\\.\\/:;<=>?@\\[\\\\\\]\\^_`\\' .
                    '{\\|\\}\\~]/',
            $dataPropertyValue,
            $this->operationSpeakAsNoPunctuation
        );
    }

    /**
     * No speak the punctuation for element and descendants.
     * @param \hatemile\util\html\HTMLDOMElement $element The element.
     */
    protected function speakAsNoPunctuationInherit(HTMLDOMElement $element)
    {
        $this->reverseSpeakAs($element, 'literal-punctuation');
        $this->reverseSpeakAs($element, 'no-punctuation');

        $this->isolateTextNode($element);

        $this->visit($element, 'speakAsNoPunctuation');
    }

    /**
     * Speak the digit at a time for each number for element only.
     * @param \hatemile\util\html\HTMLDOMElement $element The element.
     */
    protected function speakAsDigits(HTMLDOMElement $element)
    {
        $dataPropertyValue = 'digits';
        if ($this->operationSpeakAsDigits === null) {
            $this->operationSpeakAsDigits = function (
                $content,
                $index,
                $children
            ) use ($dataPropertyValue) {
                if ($index !== 0) {
                    array_push($children, $this->createContentElement(
                        substr($content, 0, $index),
                        $dataPropertyValue
                    ));
                }
                array_push($children, $this->createAuralContentElement(
                    ' ',
                    $dataPropertyValue
                ));

                array_push($children, $this->createContentElement(
                    substr($content, $index, 1),
                    $dataPropertyValue
                ));

                return $children;
            };
        }
        $this->speakAs(
            $element,
            '/[0-9]/',
            $dataPropertyValue,
            $this->operationSpeakAsDigits
        );
    }

    /**
     * Speak the digit at a time for each number for element and descendants.
     * @param \hatemile\util\html\HTMLDOMElement $element The element.
     */
    protected function speakAsDigitsInherit(HTMLDOMElement $element)
    {
        $this->reverseSpeakAs($element, 'digits');

        $this->isolateTextNode($element);

        $this->visit($element, 'speakAsDigits');
    }

    /**
     * Speaks the numbers for element and descendants as a word number.
     * @param \hatemile\util\html\HTMLDOMElement $element The element.
     */
    protected function speakAsContinuousInherit(HTMLDOMElement $element)
    {
        $this->reverseSpeakAs($element, 'digits');
    }

    /**
     * The cells headers will be spoken for every data cell for element and
     * descendants.
     * @param \hatemile\util\html\HTMLDOMElement $element The element.
     */
    protected function speakHeaderAlwaysInherit(HTMLDOMElement $element)
    {
        $this->speakHeaderOnceInherit($element);

        $cellElements = $this->htmlParser->find($element)->findDescendants(
            'td[headers],th[headers]'
        )->listResults();
        $accessibleDisplay = new AccessibleDisplayScreenReaderImplementation(
            $this->htmlParser,
            $this->configure
        );
        foreach ($cellElements as $cellElement) {
            $accessibleDisplay->displayCellHeader($cellElement);
        }
    }

    /**
     * The cells headers will be spoken one time for element and descendants.
     * @param \hatemile\util\html\HTMLDOMElement $element The element.
     */
    protected function speakHeaderOnceInherit(HTMLDOMElement $element)
    {
        $headerElements = $this->htmlParser->find($element)->findDescendants(
            '[' .
            AccessibleDisplayScreenReaderImplementation
                    ::DATA_ATTRIBUTE_HEADERS_OF .
            ']'
        )->listResults();
        foreach ($headerElements as $headerElement) {
            $headerElement->removeNode();
        }
    }

    /**
     * Provide the CSS features of speaking and speech properties in element.
     * @param \hatemile\util\html\HTMLDOMElement $element The element.
     * @param \hatemile\util\css\StyleSheetRule $rule The stylesheet rule.
     */
    protected function provideSpeakPropertiesWithRule(
        HTMLDOMElement $element,
        StyleSheetRule $rule
    ) {
        if ($rule->hasProperty('speak')) {
            $declarations = $rule->getDeclarations('speak');
            foreach ($declarations as $declaration) {
                $propertyValue = $declaration->getValue();
                if ($propertyValue === 'none') {
                    $this->speakNoneInherit($element);
                } elseif ($propertyValue === 'normal') {
                    $this->speakNormalInherit($element);
                } elseif ($propertyValue === 'spell-out') {
                    $this->speakAsSpellOutInherit($element);
                }
            }
        }
        if ($rule->hasProperty('speak-as')) {
            $declarations = $rule->getDeclarations('speak-as');
            foreach ($declarations as $declaration) {
                $propertyValue = $declaration->getValue();
                if (
                    \preg_match(
                        AccessibleCSSImplementation
                                ::$REGULAR_EXPRESSION_SPEAK_AS,
                        $propertyValue
                    )
                ) {
                    $speakAsValues = $declaration->getValues();
                    $this->speakAsNormal($element);
                    foreach ($speakAsValues as $speakAsValue) {
                        if ($speakAsValue === 'spell-out') {
                            $this->speakAsSpellOutInherit($element);
                        } elseif ($speakAsValue === 'literal-punctuation') {
                            $this->speakAsLiteralPunctuationInherit($element);
                        } elseif ($speakAsValue === 'no-punctuation') {
                            $this->speakAsNoPunctuationInherit($element);
                        } elseif ($speakAsValue === 'digits') {
                            $this->speakAsDigitsInherit($element);
                        }
                    }
                }
            }
        }
        if ($rule->hasProperty('speak-punctuation')) {
            $declarations = $rule->getDeclarations('speak-punctuation');
            foreach ($declarations as $declaration) {
                $propertyValue = $declaration->getValue();
                if ($propertyValue === 'code') {
                    $this->speakAsLiteralPunctuationInherit($element);
                } elseif ($propertyValue === 'none') {
                    $this->speakAsNoPunctuationInherit($element);
                }
            }
        }
        if ($rule->hasProperty('speak-numeral')) {
            $declarations = $rule->getDeclarations('speak-numeral');
            foreach ($declarations as $declaration) {
                $propertyValue = $declaration->getValue();
                if ($propertyValue === 'digits') {
                    $this->speakAsDigitsInherit($element);
                } elseif ($propertyValue === 'continuous') {
                    $this->speakAsContinuousInherit($element);
                }
            }
        }
        if ($rule->hasProperty('speak-header')) {
            $declarations = $rule->getDeclarations('speak-header');
            foreach ($declarations as $declaration) {
                $propertyValue = $declaration->getValue();
                if ($propertyValue === 'always') {
                    $this->speakHeaderAlwaysInherit($element);
                } elseif ($propertyValue === 'once') {
                    $this->speakHeaderOnceInherit($element);
                }
            }
        }
    }

    public function provideSpeakProperties(HTMLDOMElement $element)
    {
        $rules = $this->cssParser->getRules(array(
            'speak',
            'speak-punctuation',
            'speak-numeral',
            'speak-header',
            'speak-as'
        ));
        foreach ($rules as $rule) {
            $speakElements = $this->htmlParser->find(
                $rule->getSelector()
            )->listResults();
            foreach ($speakElements as $speakElement) {
                if ($speakElement->equals($element)) {
                    $this->provideSpeakPropertiesWithRule($element, $rule);
                    break;
                }
            }
        }
    }

    public function provideAllSpeakProperties()
    {
        $selector = null;
        $rules = $this->cssParser->getRules(array(
            'speak',
            'speak-punctuation',
            'speak-numeral',
            'speak-header',
            'speak-as'
        ));
        foreach ($rules as $rule) {
            if ($selector === null) {
                $selector = $rule->getSelector();
            } else {
                $selector = $selector . ',' . $rule->getSelector();
            }
        }
        if ($selector !== null) {
            $elements = $this->htmlParser->find($selector)->listResults();
            foreach ($elements as $element) {
                if (CommonFunctions::isValidElement($element)) {
                    $this->provideSpeakProperties($element);
                }
            }
        }
    }
}
