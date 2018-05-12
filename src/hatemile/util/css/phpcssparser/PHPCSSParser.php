<?php
/**
 * Class PHPCSSParser.
 * 
 * @package hatemile\util\css\phpcssparser
 * @author Carlson Santana Cruz
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @copyright (c) 2018, HaTeMiLe
 */

namespace hatemile\util\css\phpcssparser;

require_once join(DIRECTORY_SEPARATOR, array(
    dirname(dirname(__FILE__)),
    'StyleSheetParser.php'
));
require_once join(DIRECTORY_SEPARATOR, array(
    dirname(__FILE__),
    'PHPCSSParserRule.php'
));
require_once join(DIRECTORY_SEPARATOR, array(
    dirname(dirname(dirname(__FILE__))),
    'html',
    'HTMLDOMParser.php'
));

use \hatemile\util\css\StyleSheetParser;
use \hatemile\util\css\phpcssparser\PHPCSSParserRule;
use \hatemile\util\html\HTMLDOMParser;

/**
 * The PHPCSSParser class is official implementation of StyleSheetParser for
 * Sabberworm PHP CSS Parser.
 */
class PHPCSSParser implements StyleSheetParser
{
    /**
     * The CSS document.
     * @var \Sabberworm\CSS\CSSList\Document
     */
    protected $cssDocument;

    /**
     * Initializes a new object that encapsulate the Sabberworm PHP CSS Parser
     * parser.
     * @param \hatemile\util\html\HTMLDOMParser|string $cssCodeOrHTMLParser
     * The HTML parser or CSS code of page.
     * @param string $currentURL The current URL of page.
     */
    public function __construct($cssCodeOrHTMLParser, $currentURL = null)
    {
        if ($cssCodeOrHTMLParser instanceof HTMLDOMParser) {
            if ($currentURL === null) {
                if (filter_input(INPUT_SERVER, 'HTTPS') !== 'on') {
                    $currentURL = 'https://';
                } else {
                    $currentURL = 'http://';
                }
                $currentURL = (
                    $currentURL .
                    filter_input(INPUT_SERVER, 'SERVER_NAME') .
                    ':' .
                    filter_input(INPUT_SERVER, 'SERVER_PORT') .
                    filter_input(INPUT_SERVER, 'REQUEST_URI')
                );
            }
            $this->cssDocument = $this->createParser(
                $cssCodeOrHTMLParser,
                $currentURL
            );
        } else {
            $cssParser = new \Sabberworm\CSS\Parser($cssCodeOrHTMLParser);
            $this->cssDocument = $cssParser->parse();
        }
    }

    /**
     * Returns the absolute path of a URL.
     * @param string $currentURL The current URL of document.
     * @param string $otherURL The other URL.
     * @return string The absolute path of other URL.
     */
    protected function getAbsolutePath($currentURL, $otherURL)
    {
        $parsedURL = parse_url($currentURL);
        if (
            (strpos($otherURL, 'https://') === 0)
            || (strpos($otherURL, 'http://') === 0)
        ) {
            return $otherURL;
        } elseif (strpos($otherURL, 'data:') === 0) {
            return null;
        } elseif (strpos($otherURL, '//') === 0) {
            return $parsedURL['scheme'] . ':' . $otherURL;
        } else {
            if (isset($parsedURL['port'])) {
                $port = $parsedURL['port'];
            } else {
                $port = '';
            }
            if (strpos($otherURL, '/') === 0) {
                return (
                    $parsedURL['scheme'] .
                    '://' .
                    $parsedURL['host'] .
                    $port .
                    $otherURL
                );
            } else {
                $currentPath = preg_split('/\//', $parsedURL['path'] . 'a');
                array_pop($currentPath);
                $relativeParts = preg_split('/\//', $otherURL);
                foreach ($relativeParts as $relativePart) {
                    if ($relativePart === '..') {
                        array_pop($currentPath);
                    } elseif ($relativePart !== '.') {
                        array_push($currentPath, $relativePart);
                    }
                }
                return (
                    $parsedURL['scheme'] .
                    '://' .
                    $parsedURL['host'] .
                    $port .
                    '/' .
                    implode('/', $currentPath)
                );
            }
        }
    }

    /**
     * Create the CSS document.
     * @param \hatemile\util\html\HTMLDOMParser $htmlParser The HTML parser.
     * @param string $currentURL The current URL of page.
     * @return \Sabberworm\CSS\CSSList\Document The CSS document.
     */
    protected function createParser(HTMLDOMParser $htmlParser, $currentURL)
    {
        $cssCode = '';

        $elements = $htmlParser->find(
            'style,link[rel="stylesheet"]'
        )->listResults();
        foreach ($elements as $element) {
            if ($element->getTagName() === 'STYLE') {
                $cssCode = $cssCode . $element->getTextContent();
            } else {
                $cssCode = $cssCode . file_get_contents(
                    $this->getAbsolutePath(
                        $currentURL,
                        $element->getAttribute('href')
                    )
                );
            }
        }

        $cssParser = new \Sabberworm\CSS\Parser($cssCode);
        return $cssParser->parse();
    }

    public function getRules($properties)
    {
        $array = array();
        $rules = $this->cssDocument->getAllDeclarationBlocks();
        foreach ($rules as $rule) {
            $atomicRule = new PHPCSSParserRule($rule);
            foreach ($properties as $property) {
                if ($atomicRule->hasProperty($property)) {
                    array_push($array, $atomicRule);
                    break;
                }
            }
        }
        return $array;
    }
}
