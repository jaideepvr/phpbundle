<?php

    namespace Gvs;
    
    class ScriptBundle extends \Gvs\FileBundle {
        
        private $_bundleScriptProcessor = "";
        private $_bundleName = "";
        
        /// Purpose(Constructor):
        ///    
        public function __construct($options) {
            $optionsObject = (object) $options;
            
            if (isset($optionsObject->BundleScriptProcessUrl)) {
                $this->_bundleScriptProcessor = $optionsObject->BundleScriptProcessUrl;
            } else {
                $optionsObject->BundleFiles = false;
            }
            $this->_bundleName = isset($optionsObject->BundleName) ? $optionsObject->BundleName : "";
            parent::__construct($optionsObject);
        }
        
        /// Purpose(generateHtmlForFile):
        ///    
        protected function generateHtmlForFile($file) {
            return "<script type='text/javascript' src='{$file}'></script>";
        }
        
        /// Purpose(generateHtmlForBundleFile):
        ///    
        protected function generateHtmlForBundleFile($param) {
            if (!empty($this->_bundleName)) {
                $bundleName = $this->_bundleName;
                $file = $this->_bundleScriptProcessor . "/{$bundleName}?q={$param}";
            } else {
                $file = $this->_bundleScriptProcessor . "?q={$param}";
            }
            return "<script type='text/javascript' src='{$file}'></script>";
        }
        
        /// Purpose(minifyFile):
        ///    Minifes the js script file in place using the 3rd party composer package(matthiasmullie/minify)
        protected function minifyFile($rootFolder, $fileName, $minFileName) {
        	$filePath = "{$rootFolder}{$fileName}";
        	$minFilePath = "{$rootFolder}{$minFileName}";
        	$minifier = new \MatthiasMullie\Minify\JS($filePath);
        	
        	$minContent = $minifier->minify($minFilePath);
        	return ($minContent != "");
        }

        /// Purpose(getContentSeparator):
        ///    Returns the content separator to be used to separate contents
        protected function getContentSeparator() {
        	return ";\n\n";
        }
        
    }
