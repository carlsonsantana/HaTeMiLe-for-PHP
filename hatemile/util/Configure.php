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

namespace hatemile\util;

require_once dirname(__FILE__) . '/Skipper.php';

/**
 * The Configure class contains the configuration of HaTeMiLe.
 */
class Configure {
    
    /**
     * The parameters of configuration of HaTeMiLe.
     * @var string[]
     */
    protected $parameters;
    
    /**
     * The skippers.
     * @var \hatemile\util\Skipper
     */
    protected $skippers;


    /**
     * Initializes a new object that contains the configuration of HaTeMiLe.
     * @param string $fileName The full path of file.
     */
    public function __construct($fileName = null) {
        $this->parameters = array();
        $this->skippers = array();
        if ($fileName === null) {
            $fileName = dirname(__FILE__) . '/../../hatemile-configure.xml';
        }
        
        $file = new \DOMDocument();
        $file->load($fileName);
        $document = $file->documentElement;
        $childNodes = $document->childNodes;
        $nodeParameters = null;
        $nodeSkippers = null;
        for ($i = 0, $length = $childNodes->length; $i < $length; $i++) {
            $child = $childNodes->item($i);
            if ($child instanceof \DOMElement) {
                if (strtoupper($child->tagName) === 'PARAMETERS') {
                    $nodeParameters = $child->childNodes;
                } else if (strtoupper($child->tagName) === 'SKIPPERS') {
                    $nodeSkippers = $child->childNodes;
                }
            }
        }
        
        if ($nodeParameters !== null) {
            for ($i = 0, $length = $nodeParameters->length; $i < $length; $i++) {
                $parameter = $nodeParameters->item($i);
                if ($parameter instanceof \DOMElement) {
                    if ((strtoupper($parameter->tagName) === 'PARAMETER')
                            && ($parameter->hasAttribute('name'))) {
                        $this->parameters[$parameter->getAttribute('name')] = $parameter->textContent;
                    }
                }
            }
        }
        
        if ($nodeSkippers !== null) {
            for ($i = 0, $length = $nodeSkippers->length; $i < $length; $i++) {
                $skipper = $nodeSkippers->item($i);
                if ($skipper instanceof \DOMElement) {
                    if ((strtoupper($skipper->tagName) === 'SKIPPER')
                            && ($skipper->hasAttribute('selector'))
                            && ($skipper->hasAttribute('default-text'))
                            && ($skipper->hasAttribute('shortcut'))) {
                        array_push($this->skippers, new Skipper($skipper->getAttribute('selector')
                                , $skipper->getAttribute('default-text')
                                , $skipper->getAttribute('shortcut')));
                    }
                }
            }
        }
    }
    
    /**
     * Returns the parameters of configuration.
     * @return string[] The parameters of configuration.
     */
    public function getParameters() {
        return array_merge($this->parameters);
    }
    
    /**
     * Returns the value of a parameter of configuration.
     * @param string $parameter The parameter.
     * @return string The value of the parameter.
     */
    public function getParameter($parameter) {
        return $this->parameters[$parameter];
    }
    
    public function getSkippers() {
        return array_merge($this->skippers);
    }
}
