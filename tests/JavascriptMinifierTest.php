<?php
	require_once "../vendor/autoload.php";
	
	class JavascriptMiniferTest extends PHPUnit\Framework\TestCase {
		
		public function testMinifyJs() {
			$fileName = "/data/js/SimpleScript.js";
			$rootFolder = "e:/Projects/phpbundle/tests";
			
			$commonBundle = new \Gvs\ScriptBundle((object) array(
					"BundleFiles" => true,
					"Files" => array("/data/js/SimpleScript.js"),
					"RootFolder" => $rootFolder,
					"BundleName" => "common",
					"BundleScriptProcessUrl" => "/index.php/bundle/scripts",
					"AutoMinify" => true
			));
			$token = $commonBundle->getTokenForFiles();
			$commonBundle->generateBundledScript($token, false);
			
			$this->assertTrue(true);
		}
		
		/* static function main() {
		
			$suite = new PHPUnit_Framework_TestSuite( __CLASS__);
			PHPUnit_TextUI_TestRunner::run( $suite);
		} */
		
	}

//	JavascriptMiniferTest::main();
	