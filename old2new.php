<?php
	
	header('Content-type: text/plain');
	error_reporting(E_ALL);
	ini_set('display_errors', true);
	require 'includes/Parser.php';
	
	try {
		$parser = new Parser();
	
		$parser->directory('samples');
		$parser->cdir();
	
		$parser->parse();
		
		$parser->cdir();
		
		$parser->copySupport();
		echo 'files parsed.';
		
		
	}
	
	catch (Exception $err) {
		echo $err->getMessage();
	}
	
?>