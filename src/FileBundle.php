<?php

	namespace Gvs;

    abstract class FileBundle {
        
        private $_files = array();
        private $_folders = array();
        private $_applicationRootFolder = "";
        private $_showHeaderComments = false;
        private $_bundleFiles = true;
        private $_useMinifyIfAvailable = false;
        
        /// Purpose(__construct):
        ///    
        public function __construct($options) {
            $optionsObject = (object) $options;
            
            $this->_applicationRootFolder = isset($optionsObject->RootFolder) ? $optionsObject->RootFolder : "";
            $this->_showHeaderComments = isset($optionsObject->GenerateHeader) ? $optionsObject->GenerateHeader : false;
            $this->_files = isset($optionsObject->Files) ? $optionsObject->Files : array();
            $this->_folders = isset($optionsObject->Folders) ? $optionsObject->Folders : array();
            $this->_bundleFiles = isset($optionsObject->BundleFiles) ? $optionsObject->BundleFiles : true;

            if ($this->_bundleFiles) {
                $this->_useMinifyIfAvailable = isset($optionsObject->UseMinifyIfAvailable) ? $optionsObject->UseMinifyIfAvailable : false;
            } else {
                $this->_useMinifyIfAvailable = false;
            }
        }
        
        /// Purpose(includeFiles):
        ///    
        public function includeFiles($files) {
            if ("string" == gettype($files)) {
                array_push($this->_files, $files);
            }
            if ("array" == gettype($files)) {
                foreach ($files as $file) {
                    array_push($this->_files, $file);
                }
            }
        }
        
        /// Purpose(includeFolders):
        ///    
        public function includeFolders($folders) {
            if ("string" == gettype($folders)) {
                array_push($this->_folders, $folders);
            }
            if ("array" == gettype($folders)) {
                foreach ($folders as $folder) {
                    array_push($this->_folders, $folder);
                }
            }
        }
        
        /// Purpose(generateBundle):
        ///    
        public function generateBundle() {
            $scriptLines = array();
            
            if ($this->_bundleFiles) {
                $filesString = "f=" . implode(",", $this->_files);
                $timeString = "t=" . time();
                $infoStr = base64_encode($timeString . "&" . $filesString);
                array_push($scriptLines, $this->generateHtmlForBundleFile($infoStr));
            } else {
                foreach ($this->_files as $file) {
                    array_push($scriptLines, $this->generateHtmlForFile($file));
                }
            }
            
            foreach ($scriptLines as $line) {
                echo $line;
            }
        }
        
        /// Purpose(generateBundledScript):
        ///    
        public function generateBundledScript($param) {
            $files = $this->extractFilesFromParam($param);
            $rootFolder = $this->_applicationRootFolder;
            
            if ($this->_showHeaderComments) {
                echo "/*
    This is generated as part of the GVS PHP Script Bundling composer package
*/
";
            }
            
            foreach ($files as $file) {
                $useFileName = $file;
                if ($this->_useMinifyIfAvailable) {
                    $useFileName = $this->getMinifiedFileNameIfExists($rootFolder, $file);
                }
                $filePath = "{$rootFolder}{$useFileName}";
                $scriptContent = file_get_contents($filePath);
                echo $scriptContent;
            }
        }
        
        private function extractFilesFromParam($param) {
            $originalQuery = base64_decode($param);
            list($t, $f) = explode("&", $originalQuery);
            list($key, $fileStr) = explode("=", $f);
            $files = explode(",", $fileStr);
            
            return $files;
        }
        
        protected function getMinifiedFileNameIfExists($rootFolder, $fileName) {
            $parts = explode(".", $fileName);
            $index = sizeof($parts);
            $parts[$index] = $parts[$index-1];
            $parts[$index-1] = "min";
            
            $minFileName = implode(".", $parts);
            $fullPath = "{$rootFolder}{$minFileName}";
            if (file_exists($minFileName)) {
                return $minFileName;
            } else {
                return $fileName;
            }
        }

        abstract protected function generateHtmlForFile($file);
        
        abstract protected function generateHtmlForBundleFile($param);
        
    }
