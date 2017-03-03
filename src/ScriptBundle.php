<?php

	namespace Gvs;

    require_once "./FileBundle.php";
    
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
        
    }