<?php

/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 * @package Amfphp_Backoffice_Generators
 * 
 */

/**
 * Handles typical client generation, override various methods for customisation
 * 1. copies the template.
 * 2. looks for template directives in the code. Usually these directives indicate a block of code that must be replicated.
 * Each directive starts with '/**ACG' and must end with * / 
 * note that services in subfolders should get a special treatment, and ideally code would be generated in them 
 * with additionnal sub-packages. This is technically too messy, so the '/' is simply replaced
 * by '__'.  This will be replaced by a '/' in Amfphp. 
 *
 * @author Ariel Sommeria-klein
 * @package Amfphp_Backoffice_Generators
 */
class Amfphp_BackOffice_ClientGenerator_LocalClientGenerator {

    /**
     * code file extensions to parse.
     * for example array('as')
     * @var array 
     */
    protected $codeFileExtensions;

    /**
     * absolute path to folder where template is
     * @var string 
     */
    protected $templateFolderUrl;

    /**
     * services
     * @var array 
     */
    protected $services;

    /**
     * service being processed. 
     * untyped
     * @see AmfphpDiscovery_ServiceDescriptor
     * @var stdClass 
     */
    protected $serviceBeingProcessed;

    /**
     * method being processed. 
     * untyped
     * @see AmfphpDiscovery_MethodDescriptor
     * @var stdClass 
     */
    protected $methodBeingProcessed;

    /**
     * file being processed, useful for error messages
     * @var SplFileInfo 
     */
    protected $fileBeingProcessed;

    /**
     * url
     * @var string 
     */
    protected $amfphpEntryPointUrl;

    /**
     * absolute!
     * @var string
     */
    public $targetFolder = 'ClientGenerator/Generated/';

    //terms to replace

    const _SERVICE_ = '_SERVICE_';
    const _METHOD_ = '_METHOD_';
    const _PARAMETER_ = '_PARAMETER_';
    //directive types
    const SERVICE = 'SERVICE';
    const METHOD = 'METHOD';
    const PARAMETER = 'PARAMETER';
    const PARAMETER_COMMA = 'PARAMETER_COMMA';

    /**
     * constructor
     * @param array $codeFileExtensions
     * @param type $templateFolderUrl
     */
    public function __construct(array $codeFileExtensions, $templateFolderUrl) {
        $this->codeFileExtensions = $codeFileExtensions;
        $this->templateFolderUrl = $templateFolderUrl;
    }

    /**
     * override to provide a custom text in the Client Generator UI button for this generator.
     * @return String 
     */
    public function getUiCallText() {
        return get_class($this);
    }

    /**
     * override to provide a custom url for a page containing info for this generator.
     * @return String 
     */
    public function getInfoUrl() {
        return "http://www.silexlabs.org/amfphp/documentation/client-generators/";
    }

    /**
     * added to the url of the generated code to go to its test page directly fro, the client generator ui
     * for example: 'testhtml'/index.html'
     * return false if none, for example if the generated client must be compiled first
     * 
     */
    public function getTestUrlSuffix() {
        return false;
    }

    /**
     * generate project based on template
     * @param array $services . note: here '/' in each service name is replaced by '__', to avoid dealing with packages
     * @param string $amfphpEntryPointUrl 
     * @param String absolute url to folder where to put the generated code
     * @return null
     */
    public function generate($services, $amfphpEntryPointUrl, $targetFolder) {
        foreach ($services as $service) {
            $service->name = str_replace('/', '__', $service->name);
        }
        $this->services = $services;
        $this->amfphpEntryPointUrl = $amfphpEntryPointUrl;
        Amfphp_BackOffice_ClientGenerator_Util::recurseCopy($this->templateFolderUrl, $targetFolder);
        $it = new RecursiveDirectoryIterator($targetFolder);
        foreach (new RecursiveIteratorIterator($it) as $file) {
            if (In_Array(SubStr($file, StrrPos($file, '.') + 1), $this->codeFileExtensions) == true) {
                $this->fileBeingProcessed = $file;
                $this->processSourceFile($file);
            }
        }
    }

    /**
     * looks for blocks delimited by the start and stop markers matching the directive, and applies a processing function to each
     * found block.
     * @param String $code the template code. Is modified continually
     * @param String $directive for example 'SERVICE'
     * @param String functionName
     * @return mixed. if there was a change, returns the modified code, else returns false
     */
    protected function searchForBlocksAndApplyProcessing($code, $directive, $functionName) {
        $markers = array('<!--ACG_' . $directive . '-->', '/*ACG_' . $directive . '*/');

        $hasChanged = false;

        foreach ($markers as $marker) {
            $markerLength = strlen($marker);
            $codeLength = strlen($code);
            $callBack = array($this, $functionName);


            $startPos = 0;
            $stopPos = 0;
            $seekStartPos = 0;


            while (1) {
                $startPos = strpos($code, $marker, $seekStartPos);
                if ($startPos === false) {
                    break;
                }
                //echo $startPos . '<br/><br/>';
                //startPos: before start Marker, stopPos: after stop Marker

                $stopPos = strpos($code, $marker, $startPos + 1) + $markerLength;
                if ($stopPos < $startPos) {
                    throw new Exception("missing stop marker $marker. in file $this->fileBeingProcessed");
                }
                //blockText: text within the Markers, excluding the Markers
                $blockText = substr($code, $startPos + $markerLength, $stopPos - $startPos - 2 * $markerLength);
                //$processedText = $this->processServiceListBlock($blockText);
                $processedText = call_user_func($callBack, $blockText);
                //up to, but exculding Marker
                $beforeBlock = substr($code, 0, $startPos);
                //after Marker
                $afterBlock = substr($code, $stopPos);
                $code = $beforeBlock . $processedText . $afterBlock;
                $hasChanged = true;
                $seekStartPos = strlen($beforeBlock . $processedText);
            }
        }
        if ($hasChanged) {
            return $code;
        } else {
            return false;
        }
    }

    /**
     * load the code, and look if either file is a service block, or if it contains service blocks.
     * If the file is a service block(detected by having '_SERVICE_' in the file name), call generateServiceFiles
     * If not, look for block delimited by the 'SERVICE' directive and call processServiceListBlock on them
     * Also sets the amfphp entry point url
     * @param SplFileInfo $file 
     */
    protected function processSourceFile(SplFileInfo $file) {
        $code = file_get_contents($file);
        $amfphpUrlMarkerPos = strpos($code, '/*ACG_AMFPHPURL*/');
        if ($amfphpUrlMarkerPos !== false) {
            $code = str_replace('/*ACG_AMFPHPURL*/', $this->amfphpEntryPointUrl, $code);
            file_put_contents($file, $code);
        }
        $fileName = $file->getFilename();
        if (strpos($fileName, self::_SERVICE_) !== false) {
            $this->generateServiceFiles($code, $file);
        } else {
            $processed = $this->searchForBlocksAndApplyProcessing($code, self::SERVICE, 'processServiceListBlock');
            if ($processed) {
                file_put_contents($file, $processed);
            }
        }
    }

    /**
     * generate as many copies as there are services and 
     * treat it as a service block.
     * @param String $code 
     * @param SplFileInfo $file
     */
    protected function generateServiceFiles($code, SplFileInfo $file) {
        foreach ($this->services as $service) {
            $fileNameMatchingService = str_replace(self::_SERVICE_, $service->name, $file->getFilename());
            $this->serviceBeingProcessed = $service;
            $newFilePath = $file->getPath() . '/' . $fileNameMatchingService;
            $codeMatchingService = $this->generateOneServiceFileCode($code);
            file_put_contents($newFilePath, $codeMatchingService);
        }
        unlink($file);
    }

    /**
     * generates code for one Service File. 
     * @param String $code
     * @return String 
     */
    protected function generateOneServiceFileCode($code) {
        $wrappedComment = $this->wrapComment($this->serviceBeingProcessed->comment);
        $codeMatchingService = str_replace('/*ACG_SERVICE_COMMENT*/', $wrappedComment, $code);
        $codeMatchingService = str_replace(self::_SERVICE_, $this->serviceBeingProcessed->name, $codeMatchingService);
        $processed = $this->searchForBlocksAndApplyProcessing($codeMatchingService, self::METHOD, 'processMethodListBlock');
        if ($processed) {
            $codeMatchingService = $processed;
        }
        return $codeMatchingService;
    }

    /**
     * finds method blocks.
     * applies processMethodListBlock to each of them
     * then multiplies and adapts the resulting code for each service
     * @param type $code
     */
    protected function processServiceListBlock($code) {
        $ret = '';
        foreach ($this->services as $service) {
            $this->serviceBeingProcessed = $service;
            $wrappedComment = $this->wrapComment($service->comment);
            $blockForService = str_replace('/*ACG_SERVICE_COMMENT*/', $wrappedComment, $code);
            $blockForService = str_replace(self::_SERVICE_, $service->name, $blockForService);

            $processed = $this->searchForBlocksAndApplyProcessing($blockForService, self::METHOD, 'processMethodListBlock');
            if ($processed) {
                $blockForService = $processed;
            }

            $ret .= $blockForService;
        }
        return $ret;
    }
    
   

    /**
     * finds parameter blocks.
     * applies processParameterListBlock to each of them
     * then multiplies and adapts the resulting code for each method
     * @param type $code
     */
    protected function processMethodListBlock($code) {
        $ret = '';
        foreach ($this->serviceBeingProcessed->methods as $method) {
            $this->methodBeingProcessed = $method;
            $wrappedComment = $this->wrapComment($method->comment);
            $blockForMethod = str_replace('/*ACG_METHOD_COMMENT*/', $wrappedComment, $code);
            $blockForMethod = str_replace(self::_METHOD_, $method->name, $blockForMethod);
            
            $processed = $this->searchForBlocksAndApplyProcessing($blockForMethod, self::PARAMETER, 'processParameterListBlock');
            if ($processed) {
                $blockForMethod = $processed;
            }
            $processed = $this->searchForBlocksAndApplyProcessing($blockForMethod, self::PARAMETER_COMMA, 'processParameterCommaListBlock');
            if ($processed) {
                $blockForMethod = $processed;
            }

            $ret .= $blockForMethod;
        }
        return $ret;
    }

    /**
     * multiplies and adapts the code for each parameter
     * @param type $code
     */
    protected function processParameterListBlock($code) {
        $ret = '';
        foreach ($this->methodBeingProcessed->parameters as $parameter) {
            $blockForParameter = str_replace(self::_PARAMETER_, $parameter->name, $code);
            $ret .= $blockForParameter;
        }
        return $ret;
    }

    /**
     * multiplies and adapts the code for each parameter, but adds a comma between each
     * @param type $code
     */
    protected function processParameterCommaListBlock($code) {
        $ret = '';
        foreach ($this->methodBeingProcessed->parameters as $parameter) {
            $blockForParameter = str_replace(self::_PARAMETER_, $parameter->name, $code);
            $ret .= $blockForParameter . ', ';
        }
        //remove last comma
        $ret = substr($ret, 0, -2);
        return $ret;
    }
    
    /**
     * $comment is stripped in discovery service, and needs to be wrapped with comment markers before 
     *  being reinserted in code. Here this is done with slashes and stars 
     * @param type $comment 
     */
    protected function wrapComment($comment){
        $comment = "/** $comment */";
        $comment = str_replace("\n", "\n* ", $comment);
        return $comment;
    }

}

?>
