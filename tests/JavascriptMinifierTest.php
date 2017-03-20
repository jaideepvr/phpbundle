<?php
	require_once "../vendor/autoload.php";
	
	class JavascriptMiniferTest extends PHPUnit\Framework\TestCase {
		
		public function testMinifyJs() {
			$fileName = "/data/js/SimpleScript.js";
			$rootFolder = "e:/Projects/phpbundle/tests";
			
			$commonBundle = new \Gvs\ScriptBundle((object) array(
					"BundleFiles" => true,
					"Files" => array("/data/js/SimpleScript.js", "/data/js/SimpleScript2.js"),
					"RootFolder" => $rootFolder,
					"BundleName" => "common",
					"BundleScriptProcessUrl" => "/index.php/bundle/scripts",
					"AutoMinify" => true
			));
			$token = $commonBundle->getTokenForFiles();
			$content = $commonBundle->generateBundledScript($token, false);
			
			$this->assertTrue(true);
		}
		
		static function main() {
			$suite = new PHPUnit\Framework\TestSuite( __CLASS__);
			PHPUnit\TextUI\TestRunner::run( $suite);
		}
		
	}

JavascriptMiniferTest::main();
	