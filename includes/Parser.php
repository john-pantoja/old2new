<?php

class Parser {
	
	protected $signatures;
	protected $replacements;
	protected $content;
	protected $directory;
	protected $test;
	
	public function __construct() {
		$this->content = null;
		$this->signatures = null;
		$this->replacements = null;
		$this->directory = null;
		$this->test = false;
		
		if (file_exists(__DIR__ . '/signatures.json')) {
			$tmp = file_get_contents(__DIR__ . '/signatures.json');
			
			if (json_decode($tmp) !== null) {
				$tmp = json_decode($tmp, true);
				
				if (isset($tmp['signatures'], $tmp['replacements'])) {
					$this->signatures = $tmp['signatures'];
					$this->replacements = $tmp['replacements'];
				}
			}
		}
	}
	
/* Needed reference data */

	public function signatures($signatures = null) {
		if (is_null($signatures) === false && is_array($signatures)) {
			$this->signatures = $signatures;
		}
		
		else {
			return $this->signatures;
		}
	}
	
	public function repalcements($repalcements = null) {
		if (is_null($repalcements) === false && is_array($repalcements)) {
			$this->repalcements = $repalcements;
		}
		
		else {
			return $this->repalcements;
		}
	}
	
	public function content($content = null) {
		if (is_null($content) === false) {
			$this->content = $content;
		}
		
		else {
			return $this->content;
		}
	}
	
	public function test($test = null) {
		if (is_null($test) === false && is_bool($test) && !is_numeric($test)) {
			$this->test = (bool) $test;
		}
		
		else {
			return $this->test;
		}
	}
	
	public function directory($directory) {
		if (is_null($directory) === false) {
			$d = str_ireplace('../', '', $directory);
			
			if (stripos($d, '/') != strlen($d) - 1) {
				$d .= '/';
			}
			
			$this->directory = $d;
		}
		
		else {
			return $this->directory;
		}
	}
	
	public function copySupport() {
		$supportSource = __DIR__ . '/support/';
		$supportDestination = $this->directory;
		
		if (is_dir($supportSource)) {
			$this->copyResource($supportSource, $supportDestination);
		}
	}
	
	public function cdir() {
		if (is_dir($this->directory) !== false) {
					
			$handle = opendir($this->directory);
			
			if ($handle !== false) {
				
				while(($file = readdir($handle)) !== false) {
					
					switch (true) {
						case is_file($this->directory . $file):
							if (stripos($file, '.consumed') === strlen($file) - strlen('.consumed') || stripos($file, '.bak') === strlen($file) - strlen('.bak')) {
								unlink($this->directory . $file);
							}
						break;
						
						case is_dir($this->directory . $file) && $file == 'vendor':
							$this->removeVendorFiles($this->directory . $file);
						break;
					}
				}
				
				return true;
			}
			
			else {
				throw new Exception('Unable to open directory.', 400);
			}	
		}
		
		else {
			throw new Exception('Invalid folder.', 404);
		}
	}

/** Needed reference data **/

/* Primary methods */

	public function parse($content = null) {
		
		switch (true) {
			case is_null($content) && is_null($this->directory) == false && (is_array($this->signatures) && isset($this->signatures['extension'])):
				
				if (is_dir($this->directory) !== false) {
					
					$handle = opendir($this->directory);
					
					if ($handle !== false) {
						
						while(($file = readdir($handle)) !== false) {
							
							if (is_file($this->directory . $file)) {
							
								if ($this->isAt($this->signatures['extension'], $file, (strlen($file) - strlen($this->signatures['extension'])))) {
									$this->content = file_get_contents($this->directory . $file);
									
									$consumed = $this->consume();
									
									if ( is_string($consumed) !== false) {
										
										$write = $this->replaceAppToken($consumed);
										
										if ($this->test === false) {
											rename($this->directory . $file, $this->directory . $file . '.bak');
											file_put_contents($this->directory . $file, $write);
										}
										
										else {
											copy($this->directory . $file, $this->directory . $file . '.bak');
											file_put_contents($this->directory . $file . '.consumed', $write);
										}
									}
								}
							}
						}
						
						$this->copyVendorFiles();
						
						return true;
					}
					
					else {
						throw new Exception('Unable to open directory.', 400);
					}	
				}
				
				else {
					throw new Exception('Invalid folder.', 404);
				}
				
			break;
			
			case is_null($content) === false && (is_array($this->signatures) && count($this->signatures) > 0):
				$this->content = $content;
				
				return $this->consume();
			break;
			
			default:
				throw new Exception('Unable to start parser.', 404);
			break;
		}
	}

/** Primary methods **/

/* Internal methods */

	protected function isAt($needle, $haystack, $position) {
		$location = stripos($haystack, $needle);
		
		return ($location === (int) $position ? true : false);
	}
	
	protected function contains($needle, $haystack) {
		if (stripos($needle, '/') === 0) {
			return preg_match($needle, $haystack);
		}
		
		else {
			return (stripos($haystack, $needle) !== false ? true : false);
		}
	}
	
	protected function copyVendorFiles() {
		$vendorSource = __DIR__ . '/vendor/';
		$vendorDestination = $this->directory . 'vendor/';
		
		if (is_dir($vendorSource)) {
			$this->copyResource($vendorSource, $vendorDestination);
		}
	}
	
	protected function removeVendorFiles() {
		$vendorSource = $this->directory . 'vendor/';
		
		if (is_dir($vendorSource)) {
			$this->removeResource($vendorSource);
		}
	}
	
	protected function copyResource($source, $destination) {
		
		switch (true) {
			case is_dir($source) === true && (stripos($source, '.') != (strlen($source) - 1) && stripos($source, '..') != strlen($source) - 2):
				
				if (stripos($source, '/') !== (strlen($source) - 1)) {
					$source .= '/';
				}
				
				if (stripos($destination, '/') !== (strlen($destination) - 1)) {
					$destination .= '/';
				}
				
				if (is_dir($destination) === false) {
					mkdir($destination);
					
				}
					
				$handle = opendir($source);
				
				if ($handle !== false) {
					while (($item = readdir($handle)) !== false) {
						if ($item != '.' && $item != '..') {
							$this->copyResource($source . $item, $destination . $item);
						}
					}
				}
			break;
			
			case is_file($source) === true:
				copy($source, $destination);
			break;
		}
	}
	
	protected function removeResource($source) {
		
		switch (true) {
			case is_dir($source) === true && (stripos($source, '.') != (strlen($source) - 1) && stripos($source, '..') != strlen($source) - 2):
				
				if (stripos($source, '/') !== (strlen($source) - 1)) {
					$source .= '/';
				}
				
				$handle = opendir($source);
					
				if ($handle !== false) {
					while (($item = readdir($handle)) !== false) {
						if ($item != '.' && $item != '..') {
							$this->removeResource($source . $item);
						}
					}
				}
				
				rmdir($source);
			break;
			
			case is_file($source) === true:
				unlink($source);
			break;
		}
	}
	
	protected function replaceAppToken($content) {

		preg_match($this->signatures['appvar'], $content, $app);
		
		
		if (is_array($app) && count($app) > 0) {
			foreach ($app as $match) {
				if (stripos($match, '$') === 0 && stripos($match, '=') === false) {
					return str_ireplace($this->signatures['apptoken'], $match, $content);
					break;
				}
			}
			
			return str_ireplace($this->signatures['apptoken'], '/* Unable to parse $app variable, please add a signature. */', $content);
		}
		
		else {
			return str_ireplace($this->signatures['apptoken'], '/* Unable to parse $app variable, please add a signature. */', $content);
		}
	}
	
	protected function consume() {
		
		if (isset($this->signatures['sdk']) && (is_array($this->signatures['sdk']) && isset($this->signatures['sdk']['include']))) {
			
			if ($this->contains($this->signatures['sdk']['include'], $this->content)) {
				return $this->render($this->content, $this->signatures, $this->replacements);
			}
			
			else {
				return false;
			}	
		}
		
		else {
			return false;
		}
	}
	
	public function render($content = null, $signature = null, $replacement = null, $depth = 0) {
		$parsed = '';
		
		switch (true) {
			case is_array($signature) && is_array($replacement):
				$parsed = $content;
				
				foreach ($signature as $k => $v) {
					$parsed = $this->render($parsed, $v, (isset($replacement[$k]) ? $replacement[$k] : false), $depth++);
				}
			break;
			
			case is_string($signature) && $replacement === false:
				$parsed = $content;
			break;
			
			default:
				if (stripos($signature, '/') === 0) {
					$parsed = preg_replace($signature, $replacement, $content);
				}
				
				else {
					$parsed = str_ireplace($signature, $replacement, $content);
				}
			break;
		}
		
		return $parsed;
	}
	
/** Internal methods */
	
	public function __destruct() {
		
	}
	
}
	
?>