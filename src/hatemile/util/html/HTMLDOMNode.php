<?php
/**
 * Interface HTMLDOMNode.
 * 
 * @package hatemile\util\html
 * @author Carlson Santana Cruz
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @copyright (c) 2018, HaTeMiLe
 */

namespace hatemile\util\html;

/**
 * The HTMLDOMNode interface contains the methods for access the Node.
 */
interface HTMLDOMNode
{

    /**
     * Returns the text content of node.
     * @return string The text content of node.
     */
    public function getTextContent();

    /**
     * Insert a node before this node.
     * @param \hatemile\util\html\HTMLDOMNode $newNode The node that be
     * inserted.
     * @return \hatemile\util\html\HTMLDOMNode This node.
     */
    public function insertBefore(HTMLDOMNode $newNode);

    /**
     * Insert a node after this node.
     * @param \hatemile\util\html\HTMLDOMNode $newNode The node that be
     * inserted.
     * @return \hatemile\util\html\HTMLDOMNode This node.
     */
    public function insertAfter(HTMLDOMNode $newNode);

    /**
     * Remove this node of the parser.
     * @return \hatemile\util\html\HTMLDOMNode The removed node.
     */
    public function removeNode();

    /**
     * Replace this node for other node.
     * @param \hatemile\util\html\HTMLDOMNode $newNode The node that replace
     * this node.
     * @return \hatemile\util\html\HTMLDOMNode This node.
     */
    public function replaceNode(HTMLDOMNode $newNode);

    /**
     * Append a text content in node.
     * @param string $text The text content.
     * @return \hatemile\util\html\HTMLDOMNode This node.
     */
    public function appendText($text);

    /**
     * Prepend a text content in node.
     * @param string $text The text content.
     * @return \hatemile\util\html\HTMLDOMNode This node.
     */
    public function prependText($text);

    /**
     * Returns the parent element of this node.
     * @return \hatemile\util\html\HTMLDOMElement The parent element of this
     * node.
     */
    public function getParentElement();

    /**
     * Returns the native object of this node.
     * @return object The native object of this node.
     */
    public function getData();

    /**
     * Modify the native object of this node.
     * @param object $data The native object of this node.
     */
    public function setData($data);

    /**
     * Indicates whether some other object is "equal to" this one.
     * @param object $obj The reference object with which to compare.
     * @return bool True if this object is the same as the obj argument or false
     * otherwise.
     */
    public function equals($obj);
}
