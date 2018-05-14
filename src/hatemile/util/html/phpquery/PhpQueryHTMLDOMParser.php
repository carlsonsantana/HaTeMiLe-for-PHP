<?php
/**
 * Class PhpQueryHTMLDOMParser.
 * 
 * @package hatemile\util\html\phpquery
 * @author Carlson Santana Cruz
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @copyright (c) 2018, HaTeMiLe
 */

namespace hatemile\util\html\phpquery;

require_once join(DIRECTORY_SEPARATOR, array(
    dirname(dirname(__FILE__)),
    'HTMLDOMParser.php'
));
require_once join(DIRECTORY_SEPARATOR, array(
    dirname(dirname(__FILE__)),
    'vanilla',
    'VanillaHTMLDOMElement.php'
));

use \hatemile\util\html\HTMLDOMParser;
use \hatemile\util\html\vanilla\VanillaHTMLDOMElement;

/**
 * The class PhpQueryHTMLDOMParser is official implementation of HTMLDOMParser
 * interface for the phpQuery library.
 */
class PhpQueryHTMLDOMParser implements HTMLDOMParser
{

    /**
     * The root element of the parser.
     * @var \phpQueryObject
     */
    protected $document;

    /**
     * The found elements.
     * @var \phpQueryObject
     */
    protected $results;

    /**
     * Initializes a new object that encapsulate the parser of phpQuery
     * library.
     * @param string|\phpQueryObject $codeOrParser The html code or the parser
     * from phpQuery library.
     */
    public function __construct($codeOrParser)
    {
        if (is_string($codeOrParser)) {
            $this->document = \phpQuery::newDocumentHTML($codeOrParser);
        } elseif ($codeOrParser instanceof \phpQueryObject) {
            $this->document = $codeOrParser;
        }
    }

    public function find($selector)
    {
        if ($selector instanceof VanillaHTMLDOMElement) {
            $this->results = \pq(
                $selector->getData(),
                $this->document->getDocumentID()
            );
        } else {
            $this->results = \pq($selector, $this->document->getDocumentID());
        }
        return $this;
    }

    public function findChildren($selector)
    {
        if ($selector instanceof VanillaHTMLDOMElement) {
            $this->results = $this->results->children($selector->getData());
        } else {
            $this->results = $this->results->children($selector);
        }

        return $this;
    }

    public function findDescendants($selector)
    {
        if ($selector instanceof VanillaHTMLDOMElement) {
            $this->results = $this->results->find($selector->getData());
        } else {
            $this->results = $this->results->find($selector);
        }

        return $this;
    }

    public function findAncestors($selector)
    {
        if ($selector instanceof VanillaHTMLDOMElement) {
            $this->results = $this->results->parents($selector->getData());
        } else {
            $this->results = $this->results->parents($selector);
        }

        return $this;
    }

    public function firstResult()
    {
        if (empty($this->results->elements)) {
            return null;
        }
        foreach ($this->results->elements as $item) {
            $key = \pq('*', $this->document->getDocumentID())->index($item);
            $array[$key] = $item;
        }
        ksort($array);
        foreach ($array as $key => $item) {
            return new VanillaHTMLDOMElement($item, $this);
        }
    }

    public function lastResult()
    {
        if (empty($this->results->elements)) {
            return null;
        }
        foreach ($this->results->elements as $item) {
            $key = \pq('*', $this->document->getDocumentID())->index($item);
            $array[$key] = $item;
        }
        krsort($array);
        foreach ($array as $key => $item) {
            return new VanillaHTMLDOMElement($item, $this);
        }
    }

    public function listResults()
    {
        $array = array();
        foreach ($this->results->elements as $item) {
            $key = \pq('*', $this->document->getDocumentID())->index($item);
            $array[$key] = $item;
        }
        ksort($array);
        $arraySorted = array();
        foreach ($array as $key => $item) {
            if ($item instanceof \DOMElement) {
                array_push($arraySorted, new VanillaHTMLDOMElement(
                    $item,
                    $this
                ));
            }
        }
        return $arraySorted;
    }

    public function createElement($tag)
    {
        return new VanillaHTMLDOMElement(
            $this->document->document->createElement($tag),
            $this
        );
    }

    public function getHTML()
    {
        return $this->document->htmlOuter();
    }

    public function getParser()
    {
        return $this->document;
    }

    public function clearParser()
    {
        \pq('*')->remove();
        $this->document = null;
    }
}
