<?php

	namespace Gvs;

    abstract class FileBundle {
        
        private $_files = array();
        private $_folders = array();
        private $_applicationRootFolder = "";
        private $_showHeaderComments = false;
        private $_bundleFiles = true;
        private $_useMinifyIfAvailable = false;
        private $_autoMinify = false;
        
        /// Purpose(__construct):
        ///    Initializes the abstract FileBundle class based on the options passed
        public function __construct($options) {
            $optionsObject = (object) $options;
            
            $this->_applicationRootFolder = isset($optionsObject->RootFolder) ? $optionsObject->RootFolder : "";
            $this->_showHeaderComments = isset($optionsObject->GenerateHeader) ? $optionsObject->GenerateHeader : false;
            $this->_files = isset($optionsObject->Files) ? $optionsObject->Files : array();
            $this->_folders = isset($optionsObject->Folders) ? $optionsObject->Folders : array();
            $this->_bundleFiles = isset($optionsObject->BundleFiles) ? $optionsObject->BundleFiles : true;
            $this->_autoMinify = isset($optionsObject->AutoMinify) ? $optionsObject->AutoMinify : false;

            if ($this->_bundleFiles) { // If BundleFiles is set to true then check the option to Use Minified files if available
                $this->_useMinifyIfAvailable = isset($optionsObject->UseMinifyIfAvailable) ? $optionsObject->UseMinifyIfAvailable : false;
            } else { // If BundleFiles is set to false the ignore Minified Files
                $this->_useMinifyIfAvailable = false;
            }
			if ($this->_autoMinify) { // If Auto Minify is true irrespective of BundleFiles, then use Minified files if available
				$this->_useMinifyIfAvailable = true;
			}
        }
        
        /// Purpose(includeFiles):
        ///    Add files to the internal array to bundle/minify based on options. Accepts a string of one file or an array of strings(file names)
        ///    The file names need to be relative to the RootFolder set in options 
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
        ///    Add folders to the internal array to bundle/minify based on options. Accepts a string of one folder or an array of strings(folder names)
        ///    The folder names need to be relative to the RootFolder set in options
        ///    Thfolder option is currently not being used, but is proposed to be used very soon.
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
        ///    Prepares bundled/unbudled script tag(s) based on the options and echos or returns the script tag.
        ///    If Bundling is enabled then a unique token based on the time and filenames is generated and for the q query parameter.
        public function generateBundle($echo = true) {
            $scriptLines = array();
            $script = "";
            
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
                $script = $script . $line;
            }
            if ($echo) {
            	$this->generateScriptTags($script);
            }
            return $script;
        }
        
        public function getTokenForFiles() {
        	$filesString = "f=" . implode(",", $this->_files);
        	$timeString = "t=" . time();
        	$infoStr = base64_encode($timeString . "&" . $filesString);
        	
        	return $infoStr;
        }
        
        private function generateScriptTags($script) {
        	echo $script;
        }
        
        /// Purpose(generateBundledScript):
        ///    If the script is bundled then the script tag would have been generated with the bundle processing route which
        ///    should initiate the script generation request to generate the actual script.
        public function generateBundledScript($param, $echo = true) {
            $files = $this->extractFilesFromParam($param);
            $rootFolder = $this->_applicationRootFolder;
            $content = "";
            
            if ($this->_showHeaderComments) {
                echo "/*
    This is generated as part of the GVS PHP Script Bundling composer package
*/
";
            }
            
            foreach ($files as $file) {
                $useFileName = $file;
                $minFileName = $this->getMinifiedFileNameIfExists($rootFolder, $file);
                if ($this->_useMinifyIfAvailable) {
                	if ($minFileName !== "") {
                		$useFileName = $minFileName;
                	} else {
                		if ($this->_autoMinify) {
                			$useFileName = $this->generateMinifiedFile($rootFolder, $file);
                		}
                	}
                }
                $filePath = "{$rootFolder}{$useFileName}";
                $scriptContent = file_get_contents($filePath);
                $content = $content . $scriptContent;
            }
            
            if ($echo) {
            	echo $content;
            } else {
            	return $content;
            }
        }
        
        /// Purpose(extractFilesFromParam):
        ///    Decodes the parameter and extracts the list of file names to be bundled/minifed
        private function extractFilesFromParam($param) {
            $originalQuery = base64_decode($param);
            list($t, $f) = explode("&", $originalQuery);
            list($key, $fileStr) = explode("=", $f);
            $files = explode(",", $fileStr);
            
            return $files;
        }
        
        /// Purpose(getMinifiedFileNameIfExists):
        ///    Checks if the minified file exists for the file. Minified files are identified with the ext min.<fileext>
        ///    Returns the minified file name if exists else empty string
        protected function getMinifiedFileNameIfExists($rootFolder, $fileName) {
            $minFileName = $this->getMinifiedFileName($fileName);
            $fullPath = "{$rootFolder}{$minFileName}";
            if (file_exists($fullPath)) {
                return $minFileName;
            } else {
                return "";
            }
        }
        
        /// Purpose(getMinifiedFileName):
        ///    Prepares the min file name from the original file name and returns the same
        protected function getMinifiedFileName($fileName) {
        	$parts = explode(".", $fileName);
        	$index = sizeof($parts);
        	$parts[$index] = $parts[$index-1];
        	$parts[$index-1] = "min";
        	
        	return implode(".", $parts);
        }
        
        /// Purpose(minifyFile):
        ///    Minifies the file and generates the corresponding .min.<ext> file 
        private function generateMinifiedFile($rootFolder, $fileName) {
        	$minFileName = $this->getMinifiedFileName($fileName);
        	$srcPath = "{$rootFolder}{$fileName}";
        	$minPath = "{$rootFolder}{$minFileName}";
        	
            $minifyStatus = $this->minifyFile($rootFolder, $fileName, $minFileName);
        	if (!$minifyStatus) {
        		$minFileName = $fileName;
        	}
        	
        	return $minFileName;
        }

        abstract protected function minifyFile($rootFolder, $fileName, $minFileName);
        
        abstract protected function generateHtmlForFile($file);
        
        abstract protected function generateHtmlForBundleFile($param);
        
    }
